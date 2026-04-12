<?php

namespace App\Http\Controllers;

use App\Mail\CoffeeOnWallReceiptMail;
use App\Mail\AdminCoffeeOnWallDonationMail;
use Carbon\Carbon;
use App\Models\Admin;
use App\Models\Article;
use App\Models\CoffeeWallet;
use App\Models\CoffeeWallPageSettingDetail;
use App\Models\FCMToken;
use App\Models\HomePageSettingDetail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\Package;
use App\Models\Rating;
use App\Models\Ride;
use App\Models\Booking;
use App\Models\BillingAddressSettingDetail;
use App\Models\ChatsPageSettingDetail;
use App\Models\RideDetail;
use App\Models\Message;
use App\Models\RideDetailPageSettingDetail;
use App\Models\PostRidePageSettingDetail;
use App\Models\FindRidePageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Price;
use Stripe\Product;
use Stripe\SetupIntent;
use Stripe\Stripe;
use Stripe\Subscription;
use Illuminate\Support\Facades\View;

class HomeController extends Controller
{
    public function index($lang = null)
    {

        $latestFilteredReviews = Rating::latest('added_on')->where('is_disply', 1)->get();

        $homePage = HomePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $video = Video::where('page', 'Introduction Video')->orderBy('id', 'desc')->first();
        if ($video) {
            $videoDetails = VideoDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id, ['video_id' => $video->id]);
        }

        $token = null;
        if (auth()->user()) {
            // $token = FCMToken::where('user_id', auth()->user()->id)->pluck('token')->first();
            $token = auth()->user()->createToken('auth_token')->plainTextToken;

            // from step5 with skip -> update step5 to 1 (no validations)
            if (request()->has('skip')) {
                User::whereId(auth()->user()->id)->update([
                    'step' => 5
                ]);
            }
        }


        // $ratings = Rating::all();

        $langId = $this->selectedLanguage->id;

        // $articles = Article::whereHas('articleDetail', function ($query) use ($langId) {
        //     $query->where('language_id', $langId);
        // })->with('articleDetail')->orderBy('id', 'desc')->limit(8)->get();

        // two rides
        $rides = Ride::limit(2)->get();

        foreach ($rides as $ride) {
            $ride = $this->makeDetailOfRide($ride);
        }

        $rideDetailPage = RideDetailPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $findRidePage = FindRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        View::share([
            'findRidePage' => $findRidePage,
            'rideDetailPage' => $rideDetailPage,
        ]);

        return view(
            'index',
            [
                'token' => $token,
                'rideDetailPage' => $rideDetailPage,
                'rides' => $rides,
                'video' => $videoDetails,
                'reviews' => $latestFilteredReviews,
                'homePage' => $homePage,
                'findRidePage' => $findRidePage,
                // 'articles' => $articles,
                // 'ratings' => $ratings,
            ]
        );
    }

    function redirectToAdminDashboard()
    {
        return view('admin.app');
    }

    public function updateToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ], [
            'token.required' => 'The token is required',
        ]);

        $user_id = auth()->user()->id;

        $fcm_token = FCMToken::where('user_id', $user_id)->where('token', $request->token)->first();

        if (!$fcm_token) {
            FCMToken::create([
                'user_id' => $user_id,
                'token' => $request->token,
            ]);
        }

        return response()->json(['message' => 'FCM token updated successfully.']);
    }

    public function createSubscription(Request $request)
    {
        if ($request->package_id) {
            $package = Package::whereId($request->package_id)->first();
        } else {
            $package = Package::where('price', $request->custom_amount)->first();
            if (!$package) {
                DB::beginTransaction();
                $package = Package::create([
                    'price' => $request->custom_amount ?? 0,
                    'custom' => 1,
                ]);

                $packageName = $request->name ?? env('APP_NAME');
                $packageDescription = 'custom' ?? env('APP_NAME');

                Stripe::setApiKey(config('stripe.secret'));

                if ($package->stripe_product_id) {
                    $product = Product::retrieve($package->stripe_product_id);
                    $product->name = $packageName;
                    $product->save();
                } else {
                    $product = Product::create([
                        'name' => $packageName,
                        'type' => 'service',
                    ]);
                    $package->update(['stripe_product_id' => $product->id]);
                }

                if ($package->price) {
                    $priceData = [
                        'product' => $product->id,
                        'unit_amount' => $package->price * 100,
                        'currency' => 'usd',
                        'recurring' => ['interval' => 'month', 'interval_count' => 1],
                    ];

                    $price = Price::create($priceData);
                }

                $package->update(['stripe_price_id' => $price->id ?? null]);

                $paypal_plan_id = null;

                $paypal = new PayPalClient;
                $paypal->setApiCredentials(config('paypal'));
                $token = $paypal->getAccessToken();
                $paypal->setAccessToken($token);

                if ($package->paypal_product_id) {
                    $product = $paypal->showProductDetails($package->paypal_product_id);
                    $paypal_plan_id = $product['id'] ?? null;
                }
                if (!$paypal_plan_id) {
                    $data = [
                        'name' => $packageName,
                        'type' => 'SERVICE',
                        'description' => $packageDescription,
                        'category' => 'SOFTWARE',
                    ];
                    $product = $paypal->createProduct($data);
                    $paypal_plan_id = $product['id'] ?? null;

                    $package->update(['paypal_product_id' => $paypal_plan_id]);
                }
                if ($paypal_plan_id && $package) {
                    if ($package->price > 0) {
                        $productId = $package->paypal_product_id;

                        $interval_count = 1;
                        $price = $package->price;

                        $billing_detail = [
                            [
                                'frequency' => [
                                    'interval_unit' => 'MONTH',
                                    'interval_count' => $interval_count, // Interval count
                                ],
                                'tenure_type' => 'REGULAR', // Tenure type
                                'sequence' => 1, // Cycle sequence number
                                'total_cycles' => 0, // Total cycles
                                'pricing_scheme' => [
                                    'fixed_price' => [
                                        'value' => $price, // Price value
                                        'currency_code' => 'USD',
                                    ],
                                ],
                            ]
                        ];

                        $data = [
                            'product_id' => $productId, // Replace with your product ID
                            'name' => $packageName . ' for 1 month ', // Plan name
                            'description' => $packageName . ' for 1 month plan is auto renewal', // Plan description
                            'status' => 'ACTIVE', // Plan status
                            'billing_cycles' => $billing_detail,
                            'payment_preferences' => [
                                'auto_bill_outstanding' => true,
                                'auto_renewal' => true,
                                'setup_fee' => [
                                    'value' => '0',
                                    'currency_code' => 'USD',
                                ],
                                'setup_fee_failure_action' => 'CONTINUE',
                                'payment_failure_threshold' => 5,
                            ],
                        ];

                        $plan = $paypal->createPlan($data);

                        if ($package == null) {
                            $plan['id'] = null;
                        } else {
                            $package->update(['paypal_price_id' => $plan['id']]);
                        }
                    } else {
                        $package->update(['paypal_price_id' => null]);
                    }
                }

                DB::commit();
            }
        }

        Stripe::setApiKey(config('stripe.secret'));

        $customer = Customer::create([
            'email' => $request->email ?? null,
            'payment_method' => $request->payment_method,
            'invoice_settings' => [
                'default_payment_method' => $request->payment_method,
            ],
        ]);

        $subscription = Subscription::create([
            'customer' => $customer->id,
            'items' => [[
                'price' => $package->stripe_price_id, // The recurring price ID from Stripe dashboard
            ]],
            'expand' => ['latest_invoice.payment_intent'],
        ]);

        return response()->json([
            'clientSecret' => $subscription->latest_invoice->payment_intent->client_secret,
            'subscriptionId' => $subscription->id
        ]);
    }

    public function coffeeOnWallStory($lang = null)
    {
        $languages = Language::getAllCached();
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $selectedLanguage = $selectedLanguage ? Language::where('abbreviation', $selectedLanguage)->first() : null;
        $coffeeWallPage = CoffeeWallPageSettingDetail::getByLanguageWithFallback($selectedLanguage->id, $this->defaultLang->id) ?? null;
        if (!$selectedLanguage) {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }
        return view('coffee_wall_story', [
            'selectedLanguage' => $selectedLanguage,
            'languages' => $languages,
            'coffeeWallPage' => $coffeeWallPage,
        ]);
    }

    public function coffeeOnWall($lang = null)
    {
        $coffeeWallPage = null;
        $paymentSettingDetail = null;

        $coffeeWallPage = CoffeeWallPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $paymentSettingDetail = BillingAddressSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $selectedLanguage = $this->selectedLanguage;
        $packages = Package::where('custom', 0)->with(['PackageDetail' => function ($query) use ($selectedLanguage) {
            $query->where('language_id', $selectedLanguage->id);
        }])->get();

        return view('coffee_wall', [
            'coffeeWallPage' => $coffeeWallPage,
            'packages' => $packages,
            'paymentSettingDetail' => $paymentSettingDetail,
            'stripeKey' => config('stripe.key')
        ]);
    }

    /**
     * Guest SetupIntent for Coffee on the Wall — powers Stripe Payment Element (same flow as my_cards).
     */
    public function coffeeOnWallCreateSetupIntent(Request $request)
    {
        Stripe::setApiKey(config('stripe.secret'));

        try {
            $setupIntent = SetupIntent::create([
                'payment_method_types' => ['card'],
            ]);

            return response()->json([
                'clientSecret' => $setupIntent->client_secret,
            ]);
        } catch (\Throwable $e) {
            Log::error('Coffee wall SetupIntent error: ' . $e->getMessage());

            return response()->json(['error' => 'Failed to create setup intent'], 500);
        }
    }

    public function coffeeOnWallStore(Request $request)
    {
        // Validate the form data
        $displayName = $request->anonymous ? true : false; // Checkbox meaning: "display my name"
        $validatedData = $request->validate([
            'package' => $request->custom_amount ? 'nullable' : 'required',
            'custom_amount' => $request->package == 'custom' ? 'required' : 'nullable',
            'name' => $displayName ? 'required' : 'nullable',
            'email' => 'nullable|email',
            'payment_method' => 'required|in:stripe,paypal',
            'donation_acknowledgment' => 'required',
            'terms_privacy' => 'required',
            'name_on_card' => 'required_if:payment_method,stripe',
            'card_element' => 'required_if:payment_method,stripe',
        ]);

        $messages = $this->successMessage;

        if ($request->package) {
            $package = Package::whereId($request->package)->first();
        } else {
            $package = Package::where('price', $request->custom_amount)->first();
            if (!$package) {
                DB::beginTransaction();
                $package = Package::create([
                    'price' => $request->custom_amount ?? 0,
                    'custom' => 1,
                ]);

                $packageName = $request->name ?? env('APP_NAME');
                $packageDescription = 'custom' ?? env('APP_NAME');

                Stripe::setApiKey(config('stripe.secret'));

                if ($package->stripe_product_id) {
                    $product = Product::retrieve($package->stripe_product_id);
                    $product->name = $packageName;
                    $product->save();
                } else {
                    $product = Product::create([
                        'name' => $packageName,
                        'type' => 'service',
                    ]);
                    $package->update(['stripe_product_id' => $product->id]);
                }

                if ($package->price) {
                    $priceData = [
                        'product' => $product->id,
                        'unit_amount' => $package->price * 100,
                        'currency' => 'usd',
                        'recurring' => ['interval' => 'month', 'interval_count' => 1],
                    ];

                    $price = Price::create($priceData);
                }

                $package->update(['stripe_price_id' => $price->id ?? null]);

                $paypal_plan_id = null;

                $paypal = new PayPalClient;
                $paypal->setApiCredentials(config('paypal'));
                $token = $paypal->getAccessToken();
                $paypal->setAccessToken($token);

                if ($package->paypal_product_id) {
                    $product = $paypal->showProductDetails($package->paypal_product_id);
                    $paypal_plan_id = $product['id'] ?? null;
                }
                if (!$paypal_plan_id) {
                    $data = [
                        'name' => $packageName,
                        'type' => 'SERVICE',
                        'description' => $packageDescription,
                        'category' => 'SOFTWARE',
                    ];
                    $product = $paypal->createProduct($data);
                    $paypal_plan_id = $product['id'] ?? null;

                    $package->update(['paypal_product_id' => $paypal_plan_id]);
                }
                if ($paypal_plan_id && $package) {
                    if ($package->price > 0) {
                        $productId = $package->paypal_product_id;

                        $interval_count = 1;
                        $price = $package->price;

                        $billing_detail = [
                            [
                                'frequency' => [
                                    'interval_unit' => 'MONTH',
                                    'interval_count' => $interval_count, // Interval count
                                ],
                                'tenure_type' => 'REGULAR', // Tenure type
                                'sequence' => 1, // Cycle sequence number
                                'total_cycles' => 0, // Total cycles
                                'pricing_scheme' => [
                                    'fixed_price' => [
                                        'value' => $price, // Price value
                                        'currency_code' => 'USD',
                                    ],
                                ],
                            ]
                        ];

                        $data = [
                            'product_id' => $productId, // Replace with your product ID
                            'name' => $packageName . ' for 1 month ', // Plan name
                            'description' => $packageName . ' for 1 month plan is auto renewal', // Plan description
                            'status' => 'ACTIVE', // Plan status
                            'billing_cycles' => $billing_detail,
                            'payment_preferences' => [
                                'auto_bill_outstanding' => true,
                                'auto_renewal' => true,
                                'setup_fee' => [
                                    'value' => '0',
                                    'currency_code' => 'USD',
                                ],
                                'setup_fee_failure_action' => 'CONTINUE',
                                'payment_failure_threshold' => 5,
                            ],
                        ];

                        $plan = $paypal->createPlan($data);

                        if ($package == null) {
                            $plan['id'] = null;
                        } else {
                            $package->update(['paypal_price_id' => $plan['id']]);
                        }
                    } else {
                        $package->update(['paypal_price_id' => null]);
                    }
                }

                DB::commit();
            }
        }

        if ($package->price > 0 && $request->payment_method === 'stripe') {
            $request->validate([
                'stripeToken' => 'required',
            ]);
        }

        $package_price = $package->price;
        $payerEmail = trim((string) ($request->email ?: (auth()->user()?->email ?? '')));

        if ($request->notify_coffee_used && empty($payerEmail)) {
            return back()
                ->withErrors(['email' => 'Email field is required'])
                ->withInput();
        }

        if ($request->payment_method == 'stripe') {
            Stripe::setApiKey(config('stripe.secret'));
        }

        try {
            if ($request->payment_method == 'stripe' && $package_price > 0) {
                $interval = null;

                if ($request->frequency == 'weekly') {
                    $interval = 'week';
                    $interval_count = 1; // Charge every 1 week
                } else if ($request->frequency == 'monthly') {
                    $interval = 'month';
                } else if ($request->frequency == 'quarterly') {
                    $interval = 'month';
                    $interval_count = 3;
                } else if ($request->frequency == 'semi_annually') {
                    $interval = 'month';
                    $interval_count = 6;
                } else if ($request->frequency == 'annually') {
                    $interval = 'year';
                }

                // Payment Element returns pm_…; legacy flow used tok_…
                $stripeCredential = (string) $request->stripeToken;
                $isPaymentMethodFromElement = str_starts_with($stripeCredential, 'pm_');
                if (str_starts_with($stripeCredential, 'pm_')) {
                    $paymentMethods = PaymentMethod::retrieve($stripeCredential);
                } else {
                    $paymentMethods = PaymentMethod::create([
                        'type' => 'card',
                        'card' => ['token' => $stripeCredential],
                        'billing_details' => [
                            'name' => $request->name_on_card,
                            'address' => [
                                'line1' => $request->address,
                            ],
                        ],
                    ]);
                }

                $stripeCustomer = Customer::create([
                    'name' => $displayName ? ($request->name ?? 'Anonymous Donor') : 'Anonymous Donor',
                    'email' => $request->email ?? (auth()->user()?->email ?? null),
                ]);

                $stripe_customer_id = $stripeCustomer->id;

                // Attach a payment method to the customer
                $paymentMethods->attach(['customer' => $stripe_customer_id]);

                // For Payment Element `pm_...`, update billing details only after attachment.
                if ($isPaymentMethodFromElement && $request->filled('name_on_card')) {
                    PaymentMethod::update($paymentMethods->id, [
                        'billing_details' => ['name' => $request->name_on_card],
                    ]);
                }

                // Set the attached payment method as the default for the customer
                Customer::update($stripe_customer_id, [
                    'invoice_settings' => ['default_payment_method' => $paymentMethods->id],
                ]);

                $subscription_items = [
                    ['price' => $package->stripe_price_id],
                ];

                $subscription_params = [
                    'customer' => $stripe_customer_id,
                    'items' => $subscription_items,
                    'cancel_at_period_end' => false,
                ];

                // Add the interval and interval_count if applicable
                if ($interval) {

                    $timeStamp = now()->timestamp;
                    if ($interval == "week") {
                        $timeStamp = now()->addDays(7)->timestamp;
                    } elseif ($interval == 'month') {
                        $timeStamp = now()->addMonth(1)->timestamp;
                    } elseif ($interval == 'year') {
                        $timeStamp = now()->addMonth(12)->timestamp;
                    } else {
                        $timeStamp = now()->addMonth($interval_count)->timestamp;
                    }

                    $subscription_params['billing_cycle_anchor'] = $timeStamp; // Anchor the subscription
                    $subscription_items[0]['plan'] = [
                        'interval' => $interval,
                    ];
                    if (isset($interval_count)) {
                        $subscription_items[0]['plan']['interval_count'] = $interval_count;
                    }
                }

                $subscription = Subscription::create($subscription_params);

                $subscription_id = $subscription->id;
                $stripe_item_id = isset($subscription->items->data[0]) ? $subscription->items->data[0]->id : null;
            } else if ($request->payment_method == 'paypal' && $package_price > 0) {
                $paypal = new PayPalClient;
                $paypal->setApiCredentials(config('paypal'));
                $token = $paypal->getAccessToken();
                $paypal->setAccessToken($token);

                $planId = $package->paypal_price_id;
                if (empty($planId)) {
                    Log::error('Coffee Wall PayPal plan id missing', [
                        'package_id' => $package->id ?? null,
                        'frequency' => $request->frequency,
                    ]);
                    return redirect()->route('coffee_on_wall', ['lang' => $this->selectedLanguage->abbreviation])
                        ->with(['message' => 'Subscription creation failed. Error: PayPal plan is not configured for this package.']);
                }

                $interval_unit = null;
                $interval_count = 1;
                if ($request->frequency == 'weekly') {
                    $interval_unit = 'DAY';
                    $interval_count = 7; // Every 7 days
                } else if ($request->frequency == 'monthly') {
                    $interval_unit = 'MONTH';
                } else if ($request->frequency == 'quarterly') {
                    $interval_unit = 'MONTH';
                    $interval_count = 3;
                } else if ($request->frequency == 'semi_annually') {
                    $interval_unit = 'MONTH';
                    $interval_count = 6;
                } else if ($request->frequency == 'annually') {
                    $interval_unit = 'YEAR';
                }

                $data = [
                    'plan_id' => $planId, // Replace with your actual plan ID
                    'application_context' => [
                        'return_url' => route('paypal.subscription.success', [
                            'name' => $request->name,
                            'email' => $payerEmail,
                            'package_id' => $package->id,
                            'phone' => $request->phone ?? null,
                            // This query param means: "display name checkbox state"
                            'anonymous' => $request->anonymous ?? null,
                            'notify_coffee_used' => $request->notify_coffee_used ? 1 : 0,
                            'donation_acknowledgment' => $request->donation_acknowledgment ? 1 : 0,
                            'terms_privacy' => $request->terms_privacy ? 1 : 0,
                            'designation' => is_array($request->designation) ? implode(', ', $request->designation) : ($request->designation ?? null),
                            'frequency' => $request->frequency ?? null,
                        ]),
                        'cancel_url' => route('paypal.cancel')
                    ],
                ];
                if (!empty($payerEmail)) {
                    $data['subscriber'] = [
                        'name' => [
                            'given_name' => $displayName ? ($request->name ?? '') : 'Anonymous Donor',
                            'surname' => '',
                        ],
                        'email_address' => $payerEmail,
                    ];
                }

                $responseData = $paypal->createSubscription($data);
                $approveUrl = null;
                $links = data_get($responseData, 'links', []);
                if (is_array($links)) {
                    foreach ($links as $link) {
                        $rel = is_array($link) ? ($link['rel'] ?? null) : data_get($link, 'rel');
                        $href = is_array($link) ? ($link['href'] ?? null) : data_get($link, 'href');
                        if ($rel === 'approve' && !empty($href)) {
                            $approveUrl = $href;
                            break;
                        }
                    }
                    if (!$approveUrl) {
                        $fallbackHref = is_array($links[0] ?? null)
                            ? ($links[0]['href'] ?? null)
                            : data_get($links, '0.href');
                        if (!empty($fallbackHref)) {
                            $approveUrl = $fallbackHref;
                        }
                    }
                }

                if (!empty($approveUrl)) {
                    $paypalResponse = [
                        'status' => 'Success',
                        'redirect_url' => $approveUrl
                    ];
                } else {
                    $errorMessage = data_get($responseData, 'details.0.description')
                        ?? data_get($responseData, 'details.0.issue')
                        ?? data_get($responseData, 'message')
                        ?? "An unknown error occurred.";

                    Log::error('Coffee Wall PayPal subscription creation failed', [
                        'package_id' => $package->id ?? null,
                        'plan_id' => $planId,
                        'frequency' => $request->frequency,
                        'paypal_response' => $responseData,
                    ]);

                    $paypalResponse = [
                        'status' => 'Error',
                        'message' => "Subscription creation failed. Error: $errorMessage"
                    ];
                }

                if ($paypalResponse['status'] == 'Error') {
                    return redirect()->route('coffee_on_wall', ['lang' => $this->selectedLanguage->abbreviation])
                        ->with(['message' => $paypalResponse['message']]);
                } else if ($paypalResponse['status'] == 'Success') {
                    return redirect()->to($paypalResponse['redirect_url']);
                }
                return redirect()->route('coffee_on_wall', ['lang' => $this->selectedLanguage->abbreviation])->with(['message' => "Amount not processed"]);
            }

            // Handle designation array - convert to comma-separated string
            $designation = null;
            if ($request->designation) {
                $designation = is_array($request->designation) ? implode(', ', $request->designation) : $request->designation;
            }

            $coffeeWallet =  CoffeeWallet::create([
                'user_id' => auth()->id(),
                'name' => $displayName ? $request->name : null,
                'email' => $payerEmail,
                'phone' => $request->phone,
                // DB column means: anonymous donor
                'anonymous' => $displayName ? 0 : 1,
                'designation' => $designation,
                'notify_coffee_used' => $request->notify_coffee_used ? true : false,
                'donation_acknowledgment' => $request->donation_acknowledgment ? true : false,
                'terms_privacy' => $request->terms_privacy ? true : false,
                'package_id' => $package->id,
                'frequency' => $request->frequency ? $request->frequency : null,
                'dr_amount' => $package_price,
                'paypal_id' => null,
                'stripe_id' => isset($stripe_item_id) ? $stripe_item_id : null,
                'subscription_id' => $request->payment_method == 'stripe' && $package_price > 0 ? $subscription_id : null,
                'payment_method' => $package_price > 0 ? $request->payment_method : null,
                'status' => 'completed',
            ]);
            $user = auth()->user() ?? ($request->email ? User::where('email', $request->email)->first() : null);
            $fullNameFromUser = $user ? $user->first_name . ' ' . $user->last_name : ($request->name ?? 'Anonymous Donor');
            $fullName = $displayName ? $fullNameFromUser : 'Anonymous Donor';
            $data = [
                // 'full_name' => $request->name ?? 'Anonymous Donor',
                'full_name' => $fullName,
                'amount' => $package_price,
                'transaction_id' =>  $coffeeWallet->random_id,
                'transaction_date' => Carbon::now()->format('F j, Y \a\t H:i \E\S\T'),
                'payment_method' => $request->payment_method,
            ];

            if ($request->payment_method == 'paypal') {
                $data['paypal_email'] = $request->email;
            } elseif ($request->payment_method == 'stripe') {
                $card = $coffeeWallet->card;
                $data['card_type'] = $request->card_type ?? $card->card_type ?? 'Card';
                $data['cardholder_name'] = $request->name ?? $card->name ?? 'Cardholder';
                $data['last_four_digits'] = $request->card_number ?? $card->card_number ?? '****';
                $data['expiration_date'] = ($request->exp_month ?? $card->exp_month ?? 'MM') . '/' . ($request->exp_year ?? $card->exp_year ?? 'YY');
            }

            if ($request->email) {
                Mail::to($request->email)->send(new CoffeeOnWallReceiptMail($data));
            }

            // Send admin notification about Coffee on Wall donation
            $admin = Admin::first();
            if ($admin && $admin->admin_email) {
                $adminData = [
                    'donor_name' => $fullName,
                    'donor_email' => $request->notify_coffee_used ? $request->email : null,
                    'amount' => $package_price,
                    'transaction_id' => $coffeeWallet->random_id,
                    'transaction_date' => Carbon::now()->format('F j, Y \a\t H:i \E\S\T'),
                    'payment_method' => $request->payment_method,
                    'frequency' => $request->frequency ?? null,
                ];

                // Add payment method specific details
                if ($request->payment_method == 'paypal') {
                    $adminData['paypal_email'] = $request->email;
                } elseif ($request->payment_method == 'stripe') {
                    $card = $coffeeWallet->card;
                    $adminData['card_type'] = $request->card_type ?? $card->card_type ?? 'Card';
                    $adminData['cardholder_name'] = $request->name ?? $card->name ?? 'Cardholder';
                    $adminData['last_four_digits'] = $request->card_number ?? $card->card_number ?? '****';
                    $adminData['expiration_date'] = ($request->exp_month ?? $card->exp_month ?? 'MM') . '/' . ($request->exp_year ?? $card->exp_year ?? 'YY');
                }

                Mail::to($admin->admin_email)->queue(new AdminCoffeeOnWallDonationMail($adminData));
            }

            return redirect()->route('coffee_on_wall', ['lang' => $this->selectedLanguage->abbreviation])->with(['message' => $messages->coffee_wall_text_success_message ?? "Thank you for your generosity. Please accept our best wishes"])->with('heading', $messages->coffee_wall_heading_success_message ?? 'Payment successful');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function paypalSuccessResponse(Request $request)
    {
        $package = null;
        if (!isset($request->subscription_id)) {
            return false;
        }
        if (isset($_GET['package_id']) && $_GET['package_id'] != '') {
            $package = Package::whereId($_GET['package_id'])->first();
        }

        $name = isset($_GET['name']) ? $_GET['name'] : null;
        $email = isset($_GET['email']) ? $_GET['email'] : null;
        $phone = isset($_GET['phone']) ? $_GET['phone'] : null;
        // Here `anonymous` query param means: "display name checkbox state"
        $displayName = isset($_GET['anonymous']) ? ($_GET['anonymous'] ? true : false) : false;
        $designation = isset($_GET['designation']) ? $_GET['designation'] : null;
        $frequency = isset($_GET['frequency']) ? $_GET['frequency'] : null;

        $coffeeWallet = CoffeeWallet::create([
            'name' => $displayName ? $name : null,
            'email' => $email,
            'phone' => $phone,
            // DB column means: anonymous donor?
            'anonymous' => $displayName ? 0 : 1,
            'designation' => $designation,
            'notify_coffee_used' => isset($_GET['notify_coffee_used']) ? ($_GET['notify_coffee_used'] ? true : false) : false,
            'donation_acknowledgment' => isset($_GET['donation_acknowledgment']) ? ($_GET['donation_acknowledgment'] ? true : false) : false,
            'terms_privacy' => isset($_GET['terms_privacy']) ? ($_GET['terms_privacy'] ? true : false) : false,
            'package_id' => $_GET['package_id'],
            'frequency' => $frequency,
            'dr_amount' => $package->price,
            'paypal_id' => $request->subscription_id,
            'stripe_id' => null,
            'subscription_id' => null,
            'payment_method' => 'paypal',
            'status' => 'completed',
        ]);
        $user = $email ? User::where('email', $email)->first() : null;
        $fullNameFromUser = $user
            ? $user->first_name . ' ' . $user->last_name
            : ($name ?? 'Anonymous Donor');
        $fullName = $displayName ? $fullNameFromUser : 'Anonymous Donor';

        $data = [
            'full_name' => $fullName,
            'amount' => $package->price,
            'transaction_id' => $coffeeWallet->random_id,
            'transaction_date' => Carbon::now()->format('F j, Y \a\t H:i \E\S\T'),
            'payment_method' => 'paypal',
            'paypal_email' => $email,
        ];

        if ($email) {
            Mail::to($email)->queue(new CoffeeOnWallReceiptMail($data));
        }

        $messages = $this->successMessage;
            
        return redirect()->route('coffee_on_wall', ['lang' => $this->selectedLanguage->abbreviation])->with(['message' => $messages->coffee_wall_text_success_message ?? "Thank you for your generosity. Please accept our best wishes"])->with('heading', $messages->coffee_wall_heading_success_message ?? 'Payment successful');
    }

    public function getPackages(Request $request)
    {
        $selectedLanguage = $this->selectedLanguage;

        $data['packages'] = Package::with(['PackageDetail' => function ($query) use ($selectedLanguage) {
            $query->where('language_id', $selectedLanguage->id);
        }])
            ->get();
        return response()->json($data);
    }
}
