<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PackageResource;
use App\Models\Language;
use App\Models\Package;
use App\Models\PackageDetail;
use App\Traits\StatusResponser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;

class PackageController extends Controller
{
    use StatusResponser;

    public function index()
    {
        $packages = Package::query()->where('custom', 0);

        $packages = $this->whereClause($packages);
        $packages = $this->loadRelations($packages);
        $packages = $this->sortingAndLimit($packages);

        return $this->apiSuccessResponse(PackageResource::collection($packages), 'Data Get Successfully!');
    }

    public function show(Package $package)
    {
        if (isset($_GET['withPackageDetail']) && $_GET['withPackageDetail'] == '1') {
            $package = $package->loadMissing('packageDetail');
        }

        return $this->apiSuccessResponse(new PackageResource($package), 'Data Get Successfully!');
    }

    public function store(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['name.name_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['name.name_' . $language->id . '.required' => 'Name in ' . $language->name . ' is required']);
                // $validationRule = array_merge($validationRule, ['short_description.short_description_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['short_description.short_description_' . $language->id . '.required' => 'Short description in ' . $language->name . ' is required']);
                $validationRule = array_merge($validationRule, ['price' => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['price' . '.required' => 'Price is required']);
            }
        }

        $this->validate(
            $request,
            $validationRule,
            $errorMessages
        );

        $isPackageExists = Package::where('price', $request->price)->where('custom', '0')->exists();
        if ($isPackageExists) {
            return $this->errorResponse("Package has been already created.");
        }
        
        DB::beginTransaction();
        try {
            $is_default = 0;
            if ($request->is_default == true) {
                $packages = Package::get();
                foreach ($packages as $item) {
                    $item->update([
                        'is_default' => 0,
                    ]);
                }
                $is_default = 1;
            }

            $package = Package::create([
                'price' => $request->price ?? 0,
                'is_default' => $is_default,
            ]);

            foreach ($languages as $language) {
                $packageDetail = PackageDetail::whereLanguageId($language->id)->wherePackageId($package['id'])->first();

                $packageData = [
                    'package_id' => $package['id'],
                    'language_id' => $language->id,
                    'name' => $request['name']['name_' . $language->id] ?? null,
                    'short_description' => $request['short_description']['short_description_' . $language->id] ?? null,
                ];

                if ($packageDetail) {
                    $packageDetail->update($packageData);
                } else {
                    PackageDetail::create($packageData);
                }
            }

            // GET package name
            $language = Language::whereIsDefault(1)->first();
            $packageDetail = PackageDetail::wherePackageId($package->id);
            if ($language) {
                $packageDetail = $packageDetail->whereLanguageId($language->id);
            }
            $packageDetail = $packageDetail->first();
            $packageName = $packageDetail->name ?? env('APP_NAME');
            $packageDescription = $packageDetail->description ?? env('APP_NAME');

            Stripe::setApiKey(env('STRIPE_SECRET'));

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

            return $this->apiSuccessResponse(new PackageResource($package), 'Package has been added successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            $exceptionMessage = $e->getMessage() ?? null;
            if ($exceptionMessage) {
                return $this->errorResponse($e->getMessage());
            }
        }

        return $this->errorResponse();
    }

    public function update(Request $request, Package $package)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['name.name_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['name.name_' . $language->id . '.required' => 'Name in ' . $language->name . ' is required']);
                // $validationRule = array_merge($validationRule, ['short_description.short_description_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['short_description.short_description_' . $language->id . '.required' => 'Short description in ' . $language->name . ' is required']);
                $validationRule = array_merge($validationRule, ['price' => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['price' . '.required' => 'Price is required']);
            }
        }

        $this->validate(
            $request,
            $validationRule,
            $errorMessages
        );

        $isPackageExists = Package::where('id', '!=', $package->id)->where('custom', '0')->where('price', $request->price)->exists();
        if ($isPackageExists) {
            return $this->errorResponse("Package has been already created.");
        }
        
        $is_default = 0;
        if ($request->is_default == true) {
            $packages = Package::get();
            foreach ($packages as $item) {
                $item->update([
                    'is_default' => 0,
                ]);
            }
            $is_default = 1;
        }

        $package->update([
            'is_default' => $is_default,
        ]);

        foreach ($languages as $language) {
            $packageDetail = PackageDetail::whereLanguageId($language->id)->wherePackageId($package->id)->first();

            $packageData = [
                'package_id' => $package->id,
                'language_id' => $language->id,
                'name' => $request['name']['name_' . $language->id] ?? null,
                'short_description' => $request['short_description']['short_description_' . $language->id] ?? null,
            ];

            if ($packageDetail) {
                $packageDetail->update($packageData);
            } else {
                PackageDetail::create($packageData);
            }
        }

        if ($request->price != $package->price) {
            DB::beginTransaction();
            try {
                $package->update([
                    'price' => $request->price ?? 0,
                ]);
    
                // GET package name
                $language = Language::whereIsDefault(1)->first();
                $packageDetail = PackageDetail::wherePackageId($package->id);
                if ($language) {
                    $packageDetail = $packageDetail->whereLanguageId($language->id);
                }
                $packageDetail = $packageDetail->first();
                $packageName = $packageDetail->name ?? env('APP_NAME');
                $packageDescription = $packageDetail->description ?? env('APP_NAME');
    
                Stripe::setApiKey(env('STRIPE_SECRET'));
    
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
            } catch (Exception $e) {
                DB::rollBack();
                $exceptionMessage = $e->getMessage() ?? null;
                if ($exceptionMessage) {
                    return $this->errorResponse($e->getMessage());
                }
            }
        }

        return $this->apiSuccessResponse(new PackageResource($package), 'Package has been updated successfully.');
    }

    public function destroy(Package $package)
    {
        if ($package->PackageDetail()->delete() && $package->delete()) {
            return $this->apiSuccessResponse(new PackageResource($package), 'Package has been deleted successfully.');
        }
        return $this->errorResponse();
    }

    protected function loadRelations($packages)
    {
        $defaultLang = getDefaultLanguage();
        $packages = $packages->with(['packageDetail' => function ($q) use ($defaultLang) {
            $q->where('language_id', $defaultLang->id);
        }]);
        if (isset($_GET['withPackageDetail']) && $_GET['withPackageDetail'] == '1') {
            $packages = $packages->with('packageDetail');
        }
        return $packages;
    }

    protected function sortingAndLimit($packages)
    {
        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id', 'name'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            if ($_GET['sortBy'] == 'name') {
                $packages = $packages->orderBy(function ($q) {
                    $q->select('name')
                      ->from('package_details')
                      ->whereColumn('package_details.package_id', 'packages.id')
                      ->limit(1);
                }, $_GET['sortType']);
            } else {
                $packages = $packages->OrderBy($_GET['sortBy'], $_GET['sortType']);
            }
        }

        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return $packages->paginate($limit);
    }

    protected function whereClause($packages)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $packages = $packages->whereHas('packageDetail', function ($q) {
                $q->where('name', 'LIKE', '%' . $_GET['searchParam'] . '%');
            });
        }
        return $packages;
    }
}
