@extends('layouts.template')

@section('content')

<div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
    @include('layouts.inc.profile_sidebar')

    <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 lg:col-span-9 shadow">
        <div id="myModal" class="relative z-50 {{ session('message') ? '' : 'hidden' }}" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button type="button" onclick="closeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <div class="mt-2 w-full">
                                    <p id="successPopupMessage" class="can-exp-p text-center">{{ session('message', '') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                            <a href="#" onclick="closeNotificationModal()" class="button-exp-fill w-auto">{{ $siteText['close_btn_text'] }} </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="my-4 pb-2 flex justify-between items-center">
            <h1 class="mb-0">{{$paymentSettingDetail->main_heading ?? "Payment List"}}</h1>
        </div>

        <div class="">
            @forelse($cards as $card)
                <div class="border rounded p-3 mb-3 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        @if($card->payment_method_type == 'card' && $card->paymentMethod && $card->paymentMethod->card)
                            @php
                                $brand = strtolower($card->paymentMethod->card->brand ?? '');
                            @endphp
                            @if ($brand === 'visa')
                                <div class="w-14 h-9 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="56" height="36"
                                        fill="none" viewBox="0 0 24 16"
                                        class="p-Logo p-Logo--md p-CardBrandIcon">
                                        <g clip-path="url(#clip0_4934_35103)">
                                            <path fill="#00579f"
                                                d="M22 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h20a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2">
                                            </path>
                                            <path fill="#fff"
                                                d="M10.367 10.91H8.85l.949-5.802h1.517zm5.501-5.66a3.8 3.8 0 0 0-1.36-.247c-1.5 0-2.555.79-2.561 1.92-.013.833.755 1.296 1.33 1.574.587.284.786.469.786.722-.006.389-.474.568-.91.568-.607 0-.931-.092-1.425-.309l-.2-.092-.212 1.302c.356.16 1.012.303 1.692.309 1.593 0 2.63-.778 2.642-1.982.006-.66-.4-1.166-1.274-1.58-.53-.265-.856-.444-.856-.716.006-.247.275-.5.874-.5.493-.012.856.105 1.13.222l.138.062z">
                                            </path>
                                            <path fill="#fff" fill-rule="evenodd"
                                                d="M18.584 5.108h1.174l1.224 5.802h-1.405l-.18-.87h-1.95c-.055.154-.318.87-.318.87h-1.592l2.254-5.32c.156-.377.431-.482.793-.482m-.093 2.124-.606 1.623h1.261c-.062-.29-.35-1.679-.35-1.679l-.106-.5a31 31 0 0 1-.2.556"
                                                clip-rule="evenodd"></path>
                                            <path fill="#fff"
                                                d="M7.582 5.108 6.096 9.065l-.162-.803c-.275-.926-1.136-1.931-2.098-2.432l1.361 5.074h1.605l2.385-5.796z">
                                            </path>
                                            <path fill="#fff"
                                                d="M4.716 5.108H2.275l-.025.118c1.904.481 3.166 1.641 3.684 3.036l-.53-2.666c-.088-.37-.357-.475-.688-.488">
                                            </path>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_4934_35103">
                                                <path fill="#fff" d="M0 0h24v16H0z"></path>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </div>
                            @elseif($brand === 'mastercard')
                                <div class="w-14 h-9 flex items-center justify-center p-1">
                                    <svg viewBox="0 0 24 16" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" role="presentation"
                                        focusable="false" class="p-Logo p-Logo--md p-CardBrandIcon">
                                        <rect fill="#252525" height="16" rx="2" width="24">
                                        </rect>
                                        <circle cx="9" cy="8" fill="#eb001b" r="5"></circle>
                                        <circle cx="15" cy="8" fill="#f79e1b" r="5"></circle>
                                        <path
                                            d="M12 4c1.214.912 2 2.364 2 4s-.786 3.088-2 4c-1.214-.912-2-2.364-2-4s.786-3.088 2-4z"
                                            fill="#ff5f00"></path>
                                    </svg>
                                </div>
                            @elseif($brand === 'amex' || $brand === 'american express')
                                <div class="w-14 h-9 flex items-center justify-center p-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="56" height="36"
                                        fill="none" viewBox="0 0 24 16"
                                        class="p-Logo p-Logo--md p-CardBrandIcon">
                                        <g clip-path="url(#clip0_4934_35113)">
                                            <path fill="#0193ce"
                                                d="M22 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h20a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2">
                                            </path>
                                            <path fill="#fff"
                                                d="m19.127 8.063 2.278-2.333h-3.037l-.823.883-.696-.883h-3.505v.63h3.252l.949 1.135L18.62 6.36h1.139l-1.646 1.703 1.646 1.575h-1.14l-1.075-1.133-.986 1.133h-3.215v.632h3.505l.696-.883.823.883h3.037z">
                                            </path>
                                            <path fill="#fff"
                                                d="M14.19 9.009h1.9l.885-.946-.76-.946h-2.024v.63h1.772v.631H14.19z">
                                            </path>
                                            <path fill="#fff" fill-rule="evenodd"
                                                d="m5.478 9.514-.262.756H2.595l2.228-4.54h2.102l.258.504V5.73h2.621l.525 1.261.524-1.261h2.49v4.54h-1.972v-.63l-.256.63H9.542l-.262-.63v.63H6.396l-.262-.756zm6.424.126h.782l.004-3.28h-1.31l-1.05 2.27L9.28 6.36H7.97v3.027L6.395 6.36H5.347L3.774 9.64h.918l.262-.757h1.704l.262.757h1.836V7.117l1.18 2.523h.786l1.18-2.523zM6.396 8.252l-.524-1.387-.656 1.387z"
                                                clip-rule="evenodd"></path>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_4934_35113">
                                                <path fill="#fff" d="M0 0h24v16H0z"></path>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </div>
                            @elseif($brand === 'discover')
                                <div class="w-14 h-9 flex items-center justify-center p-1">
                                    <svg viewBox="0 0 24 16" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" role="presentation"
                                        focusable="false"
                                        class="p-Logo p-Logo--md p-CardBrandIcon p-CardBrandIcon--visible">
                                        <path
                                            d="M21.997 15.75H22c.955.008 1.74-.773 1.751-1.746V2.006a1.789 1.789 0 0 0-.52-1.25A1.72 1.72 0 0 0 21.997.25H2.001A1.718 1.718 0 0 0 .77.757c-.33.33-.517.779-.521 1.247v11.99c.004.47.191.92.52 1.25.329.328.771.51 1.233.506h19.994Zm0 .5h-.002.002Z"
                                            stroke="#ddd" fill="#fff"></path>
                                        <path
                                            d="M12.612 16h9.385A1.986 1.986 0 0 0 24 14.03v-2.358A38.74 38.74 0 0 1 12.612 16Z"
                                            fill="#F27712"></path>
                                        <path
                                            d="M23.172 9.296h-.852l-.96-1.266h-.091v1.266h-.695V6.152H21.6c.803 0 1.266.33 1.266.927 0 .488-.29.802-.81.902l1.116 1.315Zm-1.026-2.193c0-.306-.232-.463-.662-.463h-.215v.952h.199c.446 0 .678-.166.678-.489Zm-4.005-.951h1.97v.53h-1.275v.703h1.225v.538h-1.225v.852h1.274v.53h-1.97V6.152Zm-2.235 3.227L14.4 6.143h.761l.952 2.119.96-2.119h.745L16.295 9.38h-.389Zm-6.298-.008c-1.059 0-1.887-.72-1.887-1.655 0-.91.845-1.647 1.904-1.647.298 0 .546.058.852.19v.729a1.241 1.241 0 0 0-.869-.356c-.662 0-1.167.48-1.167 1.084 0 .637.497 1.092 1.2 1.092.315 0 .555-.1.836-.347v.728a2.13 2.13 0 0 1-.869.182ZM7.506 8.336c0 .613-.505 1.035-1.233 1.035-.53 0-.91-.182-1.233-.596l.455-.389c.157.282.422.422.753.422.315 0 .538-.19.538-.438 0-.141-.066-.249-.207-.331a2.88 2.88 0 0 0-.48-.183c-.653-.206-.877-.43-.877-.868 0-.514.48-.903 1.109-.903.397 0 .753.125 1.051.356l-.364.414a.761.761 0 0 0-.563-.248c-.298 0-.513.149-.513.347 0 .166.124.257.538.398.794.248 1.026.48 1.026.993v-.009ZM4.088 6.152h.695v3.153h-.695V6.152ZM1.854 9.305H.828V6.152h1.026c1.125 0 1.903.645 1.903 1.572 0 .472-.231.919-.637 1.217-.348.248-.737.364-1.274.364h.008Zm.81-2.367c-.23-.182-.496-.248-.95-.248h-.191v2.085h.19c.447 0 .728-.083.952-.248.24-.199.38-.497.38-.803 0-.306-.14-.596-.38-.786Z"
                                            fill="#000"></path>
                                        <path
                                            d="M12.414 6.069c-.91 0-1.655.728-1.655 1.63 0 .96.711 1.68 1.655 1.68a1.64 1.64 0 0 0 1.655-1.655c0-.927-.72-1.655-1.655-1.655Z"
                                            fill="#F27712"></path>
                                    </svg>
                                </div>
                            @else
                                <div
                                    class="w-14 h-9 bg-gray-100 border border-gray-200 rounded flex items-center justify-center">
                                    <span
                                        class="text-xs font-semibold text-gray-600">{{ strtoupper(substr($card->paymentMethod->card->brand ?? 'CARD', 0, 4)) }}</span>
                                </div>
                            @endif
                            <div>
                                <span
                                    class="text-sm font-semibold block">{{ ucfirst($card->paymentMethod->card->brand ?? 'Card') }}</span>
                                <span class="text-xs text-gray-600">•••• {{ $card->paymentMethod->card->last4 }}</span>
                            </div>
                        @elseif($card->payment_method_type == 'google_pay')
                            <div class="flex items-center space-x-3">
                                <div class="w-14 h-9 bg-black rounded flex items-center justify-center p-1">
                                    <svg class="gpay-logo" xmlns="http://www.w3.org/2000/svg" width="243.67"
                                        height="95.6" viewBox="0 0 243.67 95.6">
                                        <g id="G_Pay_Lockup" data-name="G Pay Lockup">
                                            <g id="Pay_Typeface" data-name="Pay Typeface">
                                                <path id="Letter_p" data-name="Letter p"
                                                    d="M375.89,382.7v28.19h-8.95V341.28h23.71a21.39,21.39,0,0,1,15.33,6,20.07,20.07,0,0,1,0,29.45,21.35,21.35,0,0,1-15.33,5.92H375.89Zm0-32.85v24.27h15a11.94,11.94,0,0,0,8.85-3.59,11.73,11.73,0,0,0,3.59-8.53,12.41,12.41,0,0,0-12.44-12.11h-15Z"
                                                    transform="translate(-262.17 -336.2)" style="fill:#eeeeee" />
                                                <path id="Letter_a" data-name="Letter a"
                                                    d="M435.81,361.68q9.92,0,15.65,5.31t5.73,14.54v29.35h-8.53v-6.62h-.37c-3.68,5.45-8.62,8.15-14.77,8.15a19.17,19.17,0,0,1-13.19-4.66A14.88,14.88,0,0,1,415,396.11a14.07,14.07,0,0,1,5.59-11.74c3.73-2.94,8.71-4.38,14.91-4.38,5.31,0,9.69,1,13.09,2.94v-2a10.15,10.15,0,0,0-3.68-7.92,12.63,12.63,0,0,0-8.67-3.26A13.59,13.59,0,0,0,424.44,376l-7.87-4.94Q423.07,361.66,435.81,361.68Zm-11.55,34.57a7.05,7.05,0,0,0,3,5.82,11,11,0,0,0,6.94,2.33,14.19,14.19,0,0,0,10.06-4.19,13.16,13.16,0,0,0,4.43-9.83c-2.8-2.19-6.66-3.31-11.65-3.31a15.29,15.29,0,0,0-9.09,2.61C425.47,391.5,424.25,393.69,424.25,396.25Z"
                                                    transform="translate(-262.17 -336.2)" style="fill:#eeeeee" />
                                                <path id="Letter_y" data-name="Letter y"
                                                    d="M505.83,363.22,476,431.8h-9.22l11.09-24-19.66-44.59H468l14.16,34.2h.19l13.79-34.2Z"
                                                    transform="translate(-262.17 -336.2)" style="fill:#eeeeee" />
                                            </g>
                                            <g id="G_Mark" data-name="G Mark">
                                                <path id="Blue_500" data-name="Blue 500"
                                                    d="M340.31,377a47.08,47.08,0,0,0-.75-8.39h-37.5V384h21.59a18.5,18.5,0,0,1-8,12.38v10h12.85C336,399.41,340.31,389.14,340.31,377Z"
                                                    transform="translate(-262.17 -336.2)" style="fill:#4285f4" />
                                                <path id="Green_500" data-name="Green 500"
                                                    d="M315.66,396.38a24.18,24.18,0,0,1-36-12.65H266.41V394a39.92,39.92,0,0,0,35.67,22c10.78,0,19.84-3.55,26.43-9.65Z"
                                                    transform="translate(-262.17 -336.2)" style="fill:#34a853" />
                                                <path id="Yellow_500" data-name="Yellow 500"
                                                    d="M278.42,376.1a24.07,24.07,0,0,1,1.25-7.64V358.18H266.41a40,40,0,0,0,0,35.85l13.26-10.29A24.07,24.07,0,0,1,278.42,376.1Z"
                                                    transform="translate(-262.17 -336.2)" style="fill:#fabb05" />
                                                <path id="Red_500" data-name="Red 500"
                                                    d="M302.08,352a21.69,21.69,0,0,1,15.31,6l11.39-11.38a38.34,38.34,0,0,0-26.71-10.4,39.92,39.92,0,0,0-35.67,22l13.26,10.29A23.88,23.88,0,0,1,302.08,352Z"
                                                    transform="translate(-262.17 -336.2)" style="fill:#e94235" />
                                            </g>
                                        </g>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold block">Google Pay</span>
                                    <span class="text-sm capitalize"> {{ $card->payment_method_details['card_type'] ?? '' }} </span>
                                    @php
                                        $details = is_array($card->payment_method_details) ? $card->payment_method_details : json_decode($card->payment_method_details, true);
                                    @endphp
                                    @if ($details['card_brand'] ?? null && $details['last4'] ?? null)
                                        <span class="text-xs text-gray-600">•••• {{ $details['last4'] }}</span>
                                    @endif
                                </div>
                            </div>
                        @elseif($card->payment_method_type == 'apple_pay')
                            <div class="flex items-center space-x-3">
                                <div class="w-14 h-9 bg-black rounded flex items-center justify-center">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="white"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M17.05 20.28c-.98.95-2.05.88-3.08.4-1.09-.5-2.08-.48-3.24 0-1.44.62-2.2.44-3.06-.4C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold block">Apple Pay</span>
                                    @php
                                        $details = is_array($card->payment_method_details) ? $card->payment_method_details : json_decode($card->payment_method_details, true);
                                    @endphp
                                    @if ($details['card_brand'] ?? null && $details['last4'] ?? null)
                                        <span class="text-xs text-gray-600">•••• {{ $details['last4'] }}</span>
                                    @endif
                                </div>
                            </div>
                        @elseif($card->payment_method_type == 'paypal')
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-8 flex items-center justify-center p-1">
                                    <svg id="uuid-b27e1cd4-82a8-41c1-8e0b-cc5053329b51"
                                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        viewBox="0 0 37.35 45">
                                        <defs>
                                            <clipPath id="uuid-a44e4e19-5cfa-4d0b-904a-d3baa99153ff">
                                                <rect width="37.35" height="45" style="fill:none;" />
                                            </clipPath>
                                        </defs>
                                        <g style="clip-path:url(#uuid-a44e4e19-5cfa-4d0b-904a-d3baa99153ff);">
                                            <path
                                                d="M31.86,10.35c0,5.57-5.14,12.15-12.93,12.15h-7.5l-.37,2.32-1.75,11.18H0L5.61,0h15.1c5.08,0,9.08,2.83,10.56,6.77.42,1.14.63,2.36.6,3.58Z"
                                                style="fill:#002991;" />
                                            <path
                                                d="M37.23,20.7c-1.03,6.24-6.43,10.82-12.75,10.8h-5.21l-2.17,13.5H7.83l1.48-9,1.75-11.18.37-2.32h7.5c7.77,0,12.93-6.58,12.93-12.15,3.83,1.97,6.06,5.96,5.37,10.35Z"
                                                style="fill:#60cdff;" />
                                            <path
                                                d="M31.86,10.35c-1.6-.84-3.55-1.35-5.67-1.35h-12.64l-2.12,13.5h7.5c7.77,0,12.93-6.58,12.93-12.15Z"
                                                style="fill:#008cff;" />
                                        </g>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold block">PayPal</span>
                                    <span class="text-xs text-gray-600">{{ $card->paypal_email ?? 'PayPal account' }}</span>
                                </div>
                            </div>
                        @endif
                        @if ($card->primary_card == 1 || $card->primary_card === '1')
                            <span
                                class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ $paymentSettingDetail->mobile_default_card_tab ?? 'Primary' }}</span>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        @if ($card->primary_card === '0' || $card->primary_card == 0)
                            <form action="{{ route('my_cards.set_primary', $card->id) }}" method="POST"
                                class="inline">
                                @csrf
                                <button type="submit" class="bg-greenXS hover:bg-greenXS text-white text-sm rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-3 hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS">{{$paymentSettingDetail->set_primary_card_label ?? "Make Primary"}}</button>
                            </form>
                        @endif
                        <button type="button" onclick="toggleModalCard('card-modal', {{ $card->id }})" class="bg-red-500 hover:bg-red-500 text-white text-sm rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-3 hover:text-white text-center focus:bg-red-500 focus:text-white active:text-white active:bg-red-500">{{$paymentSettingDetail->delete_card_button_text}}</button>
                    </div>
                </div>
            @empty
                <div class="text-center pt-8">
                    <p class="text-gray-600 text-center py-4">
                        @if(isset($paymentSettingDetail->no_payment_methods_text_label))
                            {{ $paymentSettingDetail->no_payment_methods_text_label }}
                        @endif
                    </p>
                </div>
            @endforelse
            <div class="text-center pt-4">
                <button type="button" onclick="openAddPaymentMethodModal()" class="button-exp-fill">{{$paymentSettingDetail->add_new_card_button_text ?? "Add Payment Method"}}</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Method Modal -->
<div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center" id="add-payment-method-modal">
    <div class="relative h-screen my-6 mx-auto flex items-center justify-center w-full">
        <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border max-h-[90vh] overflow-y-auto custom-scrollbar">
            <button type="button" onclick="closeAddPaymentMethodModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500 z-10">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <h3 class="text-2xl font-FuturaMdCnBT font-medium text-gray-900 mb-6">{{$paymentSettingDetail->add_new_card_button_text ?? "Add Payment Method"}}</h3>
                
                <!-- Payment Method Selection -->
                <div id="payment-method-selection" class="space-y-4">
                    <!-- Apple Pay (only on Apple devices) -->
                    <div id="apple-pay-button-container" class="hidden">
                        <div id="apple-pay-button" style="width: 100%; height: 50px; cursor: pointer; -webkit-appearance: -apple-pay-button; -apple-pay-button-type: plain; -apple-pay-button-style: black;"></div>
                    </div>
                    
                    <!-- Google Pay -->
                    <div id="google-pay-button-container">
                        <div id="google-pay-button" style="width: 100%; height: 50px;"></div>
                    </div>
                    
                    <!-- PayPal -->
                    <div id="paypal-button-container" style="width: 100%;"></div>
                    
                    <!-- Divider -->
                    <div class="relative flex items-center my-6">
                        <div class="flex-grow border-t border-gray-300"></div>
                        <span class="flex-shrink mx-4 text-gray-500">OR</span>
                        <div class="flex-grow border-t border-gray-300"></div>
                    </div>
                    
                    <!-- Credit/Debit Card Button -->
                    <button type="button" onclick="showCardForm()" class="button-exp-fill w-full">
                        Credit or Debit Card
                    </button>
                </div>
                
                <!-- Card Form (hidden by default) -->
                <div id="card-form-container" class="hidden mt-6">
                    <!-- Loading State -->
                    <div id="stripe-loading" class="text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <p class="mt-2 text-sm text-gray-600">Loading payment form...</p>
                    </div>
                    <form id="payment-form" class="hidden">
                        <div id="payment-element" style="min-height: 200px;"></div>
                        <div id="payment-element-errors" class="text-red-500 text-sm mt-1"></div>
                        <div class="mt-4 flex space-x-3">
                            <button type="button" onclick="resetCardForm()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors">
                                {{ $siteText['back_btn_text'] ?? 'Back' }}
                            </button>
                            <button type="submit" id="submit-card-button" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                {{ $siteText['add_btn_text'] ?? 'Add' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="add-payment-method-modal-backdrop"></div>

<!-- Delete Confirmation Modal -->
<div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center" id="card-modal">
    <div class="relative h-screen my-6 mx-auto flex items-center justify-center w-full">
        <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border1">
            <button type="button" onclick="toggleModalCard('card-modal')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                    <div class="sm:flex sm:items-start justify-center">
                        <svg width="64px" height="64px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M12 10V13" stroke="#db0000" stroke-width="2" stroke-linecap="round"></path> <path d="M12 16V15.9888" stroke="#db0000" stroke-width="2" stroke-linecap="round"></path> <path d="M10.2518 5.147L3.6508 17.0287C2.91021 18.3618 3.87415 20 5.39912 20H18.6011C20.126 20 21.09 18.3618 20.3494 17.0287L13.7484 5.147C12.9864 3.77538 11.0138 3.77538 10.2518 5.147Z" stroke="#db0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                    </div>
                    <div class="mt-2 w-full">
                        <p class="can-exp-p text-center">{{$paymentSettingDetail->delete_card_message ?? "Are you sure you want to delete this payment method?"}}</p>
                    </div>
                </div>
            </div>
            <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                <a id="delete-card-link" href="#" class="inline-flex w-auto justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3">{{ $successMessage->yes_remove_it_button_text ?? "Yes, remove it" }}</a>
                <button type="button" onclick="toggleModalCard('card-modal')" class="button-exp-fill w-42">{{ $successMessage->no_go_back_button_text ?? "No, go back" }}</button>
            </div>
        </div>
    </div>
</div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="card-modal-backdrop"></div>

@endsection

@section('script')

<script src="https://js.stripe.com/v3/"></script>
<script src="https://pay.google.com/gp/p/js/pay.js"></script>

<script>
    var processingText = {!! json_encode(getTranslatedText('processing_text', isset($selectedLanguage) ? $selectedLanguage : getDefaultLanguage(true), [], 'Processing...')) !!};
    const stripe = Stripe('{{ env('STRIPE_KEY') }}');
    let elements;
    let paymentElement;
    let isAppleDevice = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    let paypalSDKLoaded = false;
    const paypalClientId = '{{ env('PAYPAL_CLIENT_ID') }}';
    
    function openAddPaymentMethodModal() {
        const modal = document.getElementById('add-payment-method-modal');
        const backdrop = document.getElementById('add-payment-method-modal-backdrop');
        modal.classList.remove('hidden');
        backdrop.classList.remove('hidden');
        
        // Check Google Pay availability and hide container if not available
        checkAndShowGooglePay();
        
        // Initialize other payment methods first (don't wait for PayPal)
        initializeApplePay();
        initializeGooglePay();
        
        // Load PayPal SDK if not already loaded
        // if (!paypalSDKLoaded && paypalClientId && paypalClientId !== '') {
        //     loadPayPalSDK();
        // } else if (typeof paypal !== 'undefined' && paypal.Buttons) {
        //     // PayPal is already loaded, initialize it
        //     initializePayPal();
        // } else {
        //     // PayPal is not configured or not available
        //     const paypalContainer = document.getElementById('paypal-button-container');
        //     if (paypalContainer && (!paypalClientId || paypalClientId === '')) {
        //         paypalContainer.innerHTML = '<p class="text-gray-500 text-sm">PayPal is not available</p>';
        //     }
        // }
    }
    
    function loadPayPalSDK() {
        if (paypalSDKLoaded || typeof paypal !== 'undefined') {
            paypalSDKLoaded = true;
            initializePaymentMethods();
            return;
        }
        
        if (!paypalClientId || paypalClientId === '') {
            console.error('PayPal Client ID is not configured');
            const paypalContainer = document.getElementById('paypal-button-container');
            if (paypalContainer) {
                paypalContainer.innerHTML = '<p class="text-red-500 text-sm">PayPal is not configured. Please contact support.</p>';
            }
            // Continue with other payment methods
            initializePaymentMethods();
            return;
        }
        
        // Check if script already exists
        const existingScript = document.querySelector('script[src*="paypal.com/sdk/js"]');
        if (existingScript) {
            // Script already exists, wait for it to be ready
            waitForPayPalSDK();
            return;
        }
        
        // Dynamically load PayPal SDK - disable credit and card funding sources
        const script = document.createElement('script');
        script.src = `https://www.paypal.com/sdk/js?client-id=${paypalClientId}&currency=USD&intent=capture&vault=true&disable-funding=credit,card`;
        script.setAttribute('data-sdk-integration-source', 'button-factory');
        script.onload = function() {
            console.log('PayPal SDK script loaded, waiting for PayPal object...');
            // Wait a bit for PayPal object to be available
            waitForPayPalSDK();
        };
        script.onerror = function() {
            console.error('Failed to load PayPal SDK script');
            const paypalContainer = document.getElementById('paypal-button-container');
            if (paypalContainer) {
                paypalContainer.innerHTML = '<p class="text-red-500 text-sm">Failed to load PayPal. Please refresh the page.</p>';
            }
            // Continue with other payment methods
            initializePaymentMethods();
        };
        document.head.appendChild(script);
    }
    
    function waitForPayPalSDK() {
        let attempts = 0;
        const maxAttempts = 20; // 10 seconds max wait
        
        const checkInterval = setInterval(function() {
            attempts++;
            if (typeof paypal !== 'undefined' && paypal.Buttons) {
                clearInterval(checkInterval);
                console.log('PayPal SDK is ready');
                paypalSDKLoaded = true;
                // Only initialize PayPal, not all payment methods (to avoid duplicates)
                initializePayPal();
            } else if (attempts >= maxAttempts) {
                clearInterval(checkInterval);
                console.error('PayPal SDK object not available after loading script');
                const paypalContainer = document.getElementById('paypal-button-container');
                if (paypalContainer) {
                    paypalContainer.innerHTML = '<p class="text-red-500 text-sm">PayPal is taking too long to load. Please refresh the page.</p>';
                }
            }
        }, 500);
    }
    
    function closeAddPaymentMethodModal() {
        const modal = document.getElementById('add-payment-method-modal');
        const backdrop = document.getElementById('add-payment-method-modal-backdrop');
        modal.classList.add('hidden');
        backdrop.classList.add('hidden');
        document.getElementById('payment-method-selection').classList.remove('hidden');
        document.getElementById('card-form-container').classList.add('hidden');
        
        // Reset form state
        const paymentForm = document.getElementById('payment-form');
        const loadingEl = document.getElementById('stripe-loading');
        if (paymentForm) {
            paymentForm.classList.add('hidden');
            paymentForm.style.visibility = '';
            paymentForm.style.position = '';
            paymentForm.style.opacity = '';
            paymentForm.style.display = '';
        }
        if (loadingEl) {
            loadingEl.classList.remove('hidden');
        }
        
        // Reset form shown flag
        if (window.stripeFormShown !== undefined) {
            window.stripeFormShown = false;
        }
    }
    
    function showCardForm() {
        document.getElementById('payment-method-selection').classList.add('hidden');
        document.getElementById('card-form-container').classList.remove('hidden');
        
        // Reset form state
        const paymentForm = document.getElementById('payment-form');
        const loadingEl = document.getElementById('stripe-loading');
        if (paymentForm) {
            paymentForm.classList.add('hidden');
        }
        if (loadingEl) {
            loadingEl.classList.remove('hidden');
        }
        
        // Reset form shown flag
        if (window.stripeFormShown !== undefined) {
            window.stripeFormShown = false;
        }
        
        // Initialize Stripe Payment Element after ensuring container is ready
        setTimeout(() => {
            initializeStripePaymentElement();
        }, 150);
    }
    
    function resetCardForm() {
        // Hide card form container
        document.getElementById('card-form-container').classList.add('hidden');
        
        // Show payment method selection
        document.getElementById('payment-method-selection').classList.remove('hidden');
        
        // Reset form state
        const paymentForm = document.getElementById('payment-form');
        const loadingEl = document.getElementById('stripe-loading');
        if (paymentForm) {
            paymentForm.classList.add('hidden');
            paymentForm.style.visibility = '';
            paymentForm.style.position = '';
            paymentForm.style.opacity = '';
            paymentForm.style.display = '';
        }
        if (loadingEl) {
            loadingEl.classList.remove('hidden');
        }
        
        // Unmount Stripe elements if mounted
        if (window.paymentElement) {
            try {
                window.paymentElement.unmount();
            } catch (e) {
                console.log('Element already unmounted');
            }
            window.paymentElement = null;
        }
        
        // Reset form shown flag
        if (window.stripeFormShown !== undefined) {
            window.stripeFormShown = false;
        }
        
        // Reset stripe initialized flag
        stripeInitialized = false;
    }
    
    function initializePaymentMethods() {
        // Show/hide Apple Pay based on device
        if (isAppleDevice && window.ApplePaySession && ApplePaySession.canMakePayments()) {
            document.getElementById('apple-pay-button-container').classList.remove('hidden');
            initializeApplePay();
        } else {
            document.getElementById('apple-pay-button-container').classList.add('hidden');
        }
        
        // Check Google Pay availability before initializing
        checkAndShowGooglePay();
        initializeGooglePay();
        initializePayPal();
    }
    
    let setupIntentClientSecret = null;
    let stripeInitialized = false;
    
    async function initializeStripePaymentElement() {
        const paymentForm = document.getElementById('payment-form');
        const loadingEl = document.getElementById('stripe-loading');

        // Check if already initialized and mounted
        if (stripeInitialized && paymentElement) {
            // Show form and hide loading
            if (loadingEl) loadingEl.classList.add('hidden');
            if (paymentForm) paymentForm.classList.remove('hidden');

            // Try to re-mount if not already mounted
            try {
                const existingElement = document.querySelector('#payment-element > div');
                if (!existingElement) {
                    paymentElement.mount("#payment-element");
                }
            } catch (e) {
                console.log('Element already mounted or error:', e);
            }
            return;
        }

        try {
            // Create SetupIntent
            const response = await fetch('{{ route("my_cards.create_setup_intent") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const data = await response.json();
            
            if (!data.clientSecret) {
                throw new Error('Failed to create setup intent');
            }
            
            setupIntentClientSecret = data.clientSecret;
            
            elements = stripe.elements({
                clientSecret: setupIntentClientSecret,
                appearance: {
                    theme: 'stripe',
                    variables: {
                        colorPrimary: '#0570de',
                    }
                }
            });
            
            paymentElement = elements.create('payment', {
                wallets: {
                    applePay: 'auto',
                    googlePay: 'auto'
                },
                layout: 'tabs'
            });

            // Mount the element - ensure form is visible first
            const paymentElementContainer = document.getElementById('payment-element');
            if (!paymentElementContainer) {
                console.error('Payment element container not found');
                if (loadingEl) loadingEl.classList.add('hidden');
                if (paymentForm) paymentForm.classList.remove('hidden');
                document.getElementById('payment-element-errors').textContent = 'Error: Payment form container not found.';
                return;
            }

            // Show the form FIRST so Stripe can properly measure and render
            if (paymentForm) {
                paymentForm.classList.remove('hidden');
                // Remove any inline styles that might interfere
                paymentForm.style.visibility = '';
                paymentForm.style.position = '';
                paymentForm.style.opacity = '';
                paymentForm.style.display = '';
            }

            // Ensure container has proper dimensions
            paymentElementContainer.style.display = 'block';
            paymentElementContainer.style.minHeight = '200px';
            paymentElementContainer.style.width = '100%';

            // Small delay to ensure form is fully visible in DOM
            setTimeout(() => {
                try {
                    console.log('Mounting Payment Element to:', paymentElementContainer);
                    paymentElement.mount("#payment-element");
                    console.log('Payment Element mounted successfully');
                } catch (error) {
                    console.error('Error mounting Payment Element:', error);
                    // Show form anyway if mount fails
                    if (loadingEl) loadingEl.classList.add('hidden');
                    if (paymentForm) paymentForm.classList.remove('hidden');
                    document.getElementById('payment-element-errors').textContent = 'Error loading payment form. Please try again.';
                    return;
                }
            }, 50);

            // Function to hide loading and show form
            if (!window.stripeFormShown) {
                window.stripeFormShown = false;
            }
            const showPaymentForm = () => {
                if (window.stripeFormShown) {
                    console.log('Form already shown, skipping');
                    return; // Prevent multiple calls
                }
                window.stripeFormShown = true;
                console.log('Showing payment form', {
                    loadingEl,
                    paymentForm
                });

                // Get fresh references to elements
                const currentLoadingEl = document.getElementById('stripe-loading');
                const currentPaymentForm = document.getElementById('payment-form');

                if (currentLoadingEl) {
                    currentLoadingEl.classList.add('hidden');
                    console.log('Loading hidden');
                } else {
                    console.error('Loading element not found');
                }

                if (currentPaymentForm) {
                    currentPaymentForm.classList.remove('hidden');
                    // Ensure form is fully visible
                    currentPaymentForm.style.visibility = '';
                    currentPaymentForm.style.position = '';
                    currentPaymentForm.style.opacity = '';
                    currentPaymentForm.style.display = '';
                    console.log('Payment form shown');

                    // Force iframe to expand if it's still collapsed
                    setTimeout(() => {
                        const stripeIframe = document.querySelector('#payment-element iframe');
                        if (stripeIframe) {
                            const currentHeight = stripeIframe.style.height || window.getComputedStyle(stripeIframe).height;
                            console.log('Iframe height check:', currentHeight);
                            if (currentHeight === '2px' || parseInt(currentHeight) < 10) {
                                console.log('Forcing Stripe iframe to expand');
                                // Try multiple approaches
                                stripeIframe.style.setProperty('height', 'auto', 'important');
                                stripeIframe.style.setProperty('min-height', '200px', 'important');
                                stripeIframe.style.setProperty('opacity', '1', 'important');

                                // Also check parent container
                                const parentDiv = stripeIframe.closest('div');
                                if (parentDiv) {
                                    parentDiv.style.setProperty('min-height', '200px', 'important');
                                }
                            }
                        }
                    }, 500);

                    // Additional check after 1 second
                    setTimeout(() => {
                        const stripeIframe = document.querySelector('#payment-element iframe');
                        if (stripeIframe) {
                            const currentHeight = window.getComputedStyle(stripeIframe).height;
                            if (parseInt(currentHeight) < 50) {
                                console.log('Iframe still collapsed after 1s, forcing expansion');
                                stripeIframe.style.setProperty('height', '300px', 'important');
                                stripeIframe.style.setProperty('opacity', '1', 'important');
                            }
                        }
                    }, 1000);
                } else {
                    console.error('Payment form element not found');
                }
            };

            // Fallback: Hide loading after 2 seconds even if ready event doesn't fire
            const fallbackTimeout = setTimeout(() => {
                console.log('Payment Element ready timeout - showing form anyway');
                showPaymentForm();
            }, 2000);

            // Wait for element to be ready, then show form
            paymentElement.on('ready', () => {
                console.log('Payment Element ready event fired');
                // Verify element is actually rendered
                const renderedElement = document.querySelector('#payment-element > div');
                const stripeIframe = document.querySelector('#payment-element iframe');
                console.log('Rendered element check:', renderedElement ? 'Found' : 'Not found');
                console.log('Stripe iframe check:', stripeIframe ? 'Found' : 'Not found');

                // Ensure iframe is visible and has proper height
                if (stripeIframe) {
                    console.log('Iframe current height:', stripeIframe.style.height);
                    // Force iframe to be visible
                    stripeIframe.style.opacity = '1';
                    stripeIframe.style.height = 'auto';
                    stripeIframe.style.minHeight = '200px';
                }

                clearTimeout(fallbackTimeout);
                showPaymentForm();
                document.getElementById('submit-card-button').disabled = false;
            });

            // Also listen for load event as backup
            paymentElement.on('loaderror', (event) => {
                console.error('Payment Element load error:', event);
                clearTimeout(fallbackTimeout);
                showPaymentForm(); // Show form anyway
            });

            // Check immediately after mount if element rendered
            setTimeout(() => {
                const renderedElement = document.querySelector('#payment-element > div');
                if (renderedElement) {
                    console.log('Payment Element rendered immediately after mount');
                    clearTimeout(fallbackTimeout);
                    showPaymentForm();
                } else {
                    console.log('Payment Element not yet rendered, waiting...');
                }
            }, 100);

            // Check if element is already rendered (sometimes ready fires before we attach listener)
            setTimeout(() => {
                const elementExists = document.querySelector('#payment-element > div');
                if (elementExists) {
                    console.log('Payment Element found in DOM after 500ms - showing form');
                    clearTimeout(fallbackTimeout);
                    showPaymentForm();
                } else {
                    console.log('Payment Element not found in DOM yet');
                }
            }, 500);

            // Additional check after 1 second
            setTimeout(() => {
                const elementExists = document.querySelector('#payment-element > div');
                const currentLoadingEl = document.getElementById('stripe-loading');
                if (elementExists || (currentLoadingEl && !currentLoadingEl.classList.contains('hidden'))) {
                    console.log('Force showing form after 1 second');
                    clearTimeout(fallbackTimeout);
                    showPaymentForm();
                }
            }, 1000);

            paymentElement.on('change', function(event) {
                const displayError = document.getElementById('payment-element-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });

            stripeInitialized = true;
        } catch (error) {
            console.error('Error initializing payment element:', error);
            if (loadingEl) loadingEl.classList.add('hidden');
            if (paymentForm) paymentForm.classList.remove('hidden');
            document.getElementById('payment-element-errors').textContent = 'Failed to initialize payment form. Please refresh and try again.';
        }
    }
    
    // Handle card form submission
    const form = document.getElementById('payment-form');
    if (form) {
        // Remove existing listeners to prevent duplicates
        const newForm = form.cloneNode(true);
        form.parentNode.replaceChild(newForm, form);

        document.getElementById('payment-form').addEventListener('submit', async function(event) {
            event.preventDefault();
            const submitButton = document.getElementById('submit-card-button');
            submitButton.disabled = true;
            submitButton.textContent = processingText;
            
            try {
                // First, submit the elements to validate the form
                const {error: submitError} = await elements.submit();
                if (submitError) {
                    document.getElementById('payment-element-errors').textContent = submitError.message;
                    submitButton.disabled = false;
                    submitButton.textContent = 'Add';
                    return;
                }
                
                // Then confirm the setup
                const {
                    setupIntent,
                    error
                } = await stripe.confirmSetup({
                    elements,
                    clientSecret: setupIntentClientSecret,
                    confirmParams: {
                        return_url: window.location.href
                    },
                    redirect: 'if_required'
                });
                
                if (error) {
                    document.getElementById('payment-element-errors').textContent = 'Error: ' + error.message;
                    submitButton.disabled = false;
                    submitButton.textContent = 'Add';
                } else {
                    // Send payment_method to backend
                    fetch('{{ route("my_cards.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify((() => {
                            const params = new URLSearchParams(window.location.search);
                            const redirectUrl = params.get('redirectUrl') || null;
                            return {
                                payment_method_type: 'card',
                                stripeToken: setupIntent.payment_method,
                                redirectUrl: redirectUrl,
                            };
                        })())
                    })
                    .then(async response => {
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            return response.json();
                        } else {
                            // If it's a redirect (HTML response), reload the page
                            window.location.reload();
                            return;
                        }
                    })
                    .then(data => {
                        if (data && data.success) {
                            // Store message for success popup
                            if (data.message) sessionStorage.setItem('cardAddSuccess', data.message);
                            closeAddPaymentMethodModal();
                            const params = new URLSearchParams(window.location.search);
                            const redirectUrl = params.get('redirectUrl') || null;
                            if (redirectUrl) {
                                window.location.href = redirectUrl;
                            } else {
                                window.location.reload();
                            }
                        } else if (data && data.message) {
                            document.getElementById('payment-element-errors').textContent = data.message;
                            submitButton.disabled = false;
                            submitButton.textContent = 'Add';
                        } else {
                            // Fallback: close and reload
                            closeAddPaymentMethodModal();
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error saving card:', error);
                        document.getElementById('payment-element-errors').textContent = 'An error occurred while saving the card. Please try again.';
                        submitButton.disabled = false;
                        submitButton.textContent = 'Add';
                    });
                }
            } catch (err) {
                console.error('Payment confirmation error:', err);
                document.getElementById('payment-element-errors').textContent = 'An error occurred. Please try again.';
                submitButton.disabled = false;
                submitButton.textContent = 'Add';
            }
        });
    }
    
    let applePayClickHandler = null;
    
    function initializeApplePay() {
        const applePayButton = document.getElementById('apple-pay-button');
        
        // Remove existing event listener if any
        if (applePayClickHandler) {
            applePayButton.removeEventListener('click', applePayClickHandler);
        }
        
        // Create new handler
        applePayClickHandler = function() {
            if (!window.ApplePaySession || !ApplePaySession.canMakePayments()) {
                alert('Apple Pay is not available on this device.');
                return;
            }
            
            const request = {
                countryCode: 'CA',
                currencyCode: 'CAD',
                supportedNetworks: ['visa', 'masterCard', 'amex', 'discover'],
                merchantCapabilities: ['supports3DS'],
                total: {
                    label: 'ProximaRide',
                    amount: '0.00'
                }
            };
            
            // Close the modal when Apple Pay is clicked
            closeAddPaymentMethodModal();
            
            const session = new ApplePaySession(3, request);
            
            session.onvalidatemerchant = function(event) {
                // In production, validate with your server
                fetch('/api/apple-pay/validate', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({validationURL: event.validationURL})
                })
                .then(response => response.json())
                .then(data => {
                    session.completeMerchantValidation(data);
                })
                .catch(() => {
                    session.abort();
                });
            };
            
            session.onpaymentauthorized = function(event) {
                // Tokenize the payment method
                const payment = event.payment;
                saveApplePayPaymentMethod(payment);
                session.completePayment(ApplePaySession.STATUS_SUCCESS);
            };
            
            session.begin();
        };
        
        // Add the event listener
        applePayButton.addEventListener('click', applePayClickHandler);
    }
    
    function saveApplePayPaymentMethod(payment) {
        // Extract card details from Apple Pay payment
        const paymentMethod = payment.token.paymentMethod || {};
        const cardNetwork = paymentMethod.network || ''; // Visa, MasterCard, AmEx, Discover, etc.
        const displayName = paymentMethod.displayName || '';
        
        // Normalize card brand to lowercase for consistency
        let cardBrand = cardNetwork.toLowerCase();
        // Handle special cases
        if (cardBrand === 'mastercard') {
            cardBrand = 'mastercard';
        } else if (cardBrand === 'amex' || cardBrand === 'american express') {
            cardBrand = 'amex';
        } else if (cardBrand === 'visa') {
            cardBrand = 'visa';
        } else if (cardBrand === 'discover') {
            cardBrand = 'discover';
        }
        
        // Extract last4 digits from displayName (format can be "Visa ••••1234", "•••• 1234", or just "1234")
        let last4 = '';
        if (displayName) {
            // Try multiple patterns to extract last 4 digits
            // Pattern 1: "••••1234" or "•••• 1234"
            let last4Match = displayName.match(/[•*]{4}\s*(\d{4})/);
            if (!last4Match) {
                // Pattern 2: Any 4 consecutive digits at the end
                last4Match = displayName.match(/(\d{4})(?!\d)/);
            }
            if (!last4Match) {
                // Pattern 3: Any 4 digits anywhere
                last4Match = displayName.match(/(\d{4})/);
            }
            
            if (last4Match) {
                last4 = last4Match[1];
            } else {
                // If no digits found, try to extract from the end of the string
                const digitsOnly = displayName.replace(/\D/g, '');
                if (digitsOnly.length >= 4) {
                    last4 = digitsOnly.slice(-4);
                } else {
                    // Fallback: use displayName as is
                    last4 = displayName;
                }
            }
        }
        
        console.log('Processing Apple Pay payment:', {
            cardNetwork: cardNetwork,
            cardBrand: cardBrand,
            displayName: displayName,
            last4: last4
        });
        
        fetch('{{ route("my_cards.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify((() => {
                const params = new URLSearchParams(window.location.search);
                const redirectUrl = params.get('redirectUrl') || null;
                return {
                    payment_method_type: 'apple_pay',
                    payment_method_details: {
                        card_brand: cardBrand || cardNetwork,
                        last4: last4
                    },
                    apple_pay_token: payment.token,
                    redirectUrl: redirectUrl,
                };
            })())
        })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Server returned non-JSON response:', text.substring(0, 200));
                throw new Error('Server returned an invalid response. Please try again.');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (data.message) sessionStorage.setItem('cardAddSuccess', data.message);
                const params = new URLSearchParams(window.location.search);
                const redirectUrl = params.get('redirectUrl') || null;
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                } else {
                    window.location.reload();
                }
            } else {
                console.error('Apple Pay error:', data.message || 'Unknown error');
                alert(data.message || 'Failed to add Apple Pay. Please try again.');
            }
        })
        .catch(error => {
            console.error('Apple Pay processing error:', error);
            alert('An error occurred while processing Apple Pay. Please try again.');
        });
    }
    
    function checkAndShowGooglePay() {
        const googlePayContainer = document.getElementById('google-pay-button-container');
        if (!googlePayContainer) {
            return;
        }
        
        // Check if browser supports Google Pay
        // Heuristic: Android + Chrome + PaymentRequest API present
        const ua = navigator.userAgent || '';
        const isAndroid = /Android/.test(ua);
        const isChrome = /Chrome/.test(ua) || /CriOS/.test(ua);
        const hasPaymentRequest = typeof window.PaymentRequest !== 'undefined';
        const isGooglePayCapable = isAndroid && isChrome && hasPaymentRequest;
        
        if (!isGooglePayCapable) {
            googlePayContainer.classList.add('hidden');
        } else {
            googlePayContainer.classList.remove('hidden');
        }
    }
    
    function initializeGooglePay() {
        // Check if Google Pay container is visible before initializing
        const googlePayContainer = document.getElementById('google-pay-button-container');
        if (!googlePayContainer || googlePayContainer.classList.contains('hidden')) {
            return;
        }
        
        // Clear any existing buttons first
        const googlePayButton = document.getElementById('google-pay-button');
        if (!googlePayButton) {
            return;
        }
        googlePayButton.innerHTML = '';
        
        // Check if google.payments API is available
        if (typeof google === 'undefined' || !google.payments || !google.payments.api) {
            console.warn('Google Pay API not loaded');
            return;
        }
        
        const paymentsClient = new google.payments.api.PaymentsClient({
            environment: '{{ env("APP_ENV") === "production" ? "PRODUCTION" : "TEST" }}'
        });
        
        const button = paymentsClient.createButton({
            onClick: onGooglePayButtonClicked,
            buttonColor: 'default',
            buttonType: 'pay',
            buttonSizeMode: 'fill'
        });
        
        googlePayButton.appendChild(button);
    }
    
    function onGooglePayButtonClicked() {
        // Close the modal when Google Pay is clicked
        closeAddPaymentMethodModal();
        
        const paymentDataRequest = {
            apiVersion: 2,
            apiVersionMinor: 0,
            allowedPaymentMethods: [{
                type: 'CARD',
                parameters: {
                    allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
                    allowedCardNetworks: ['AMEX', 'DISCOVER', 'JCB', 'MASTERCARD', 'VISA']
                },
                tokenizationSpecification: {
                    type: 'PAYMENT_GATEWAY',
                    parameters: {
                        gateway: 'stripe',
                        'stripe:version': '2018-10-31',
                        'stripe:publishableKey': '{{ env("STRIPE_KEY") }}'
                    }
                }
            }],
            merchantInfo: {
                merchantId: '{{ env("GOOGLE_MERCHANT_ID") }}',
                merchantName: 'ProximaRide'
            },
            transactionInfo: {
                totalPriceStatus: 'NOT_CURRENTLY_KNOWN',
                currencyCode: 'CAD'
            }
        };
        
        const paymentsClient = new google.payments.api.PaymentsClient({
            environment: '{{ env("APP_ENV") === "production" ? "PRODUCTION" : "TEST" }}'
        });
        
        paymentsClient.loadPaymentData(paymentDataRequest)
            .then(function(paymentData) {
                processGooglePayPayment(paymentData);
            })
            .catch(function(err) {
                console.error('Google Pay error:', err);
            });
    }
    
    function processGooglePayPayment(paymentData) {
        // Google Pay token can be a string or object - ensure it's stringified correctly
        let token = paymentData.paymentMethodData.tokenizationData.token;
        if (typeof token === 'object') {
            token = JSON.stringify(token);
        }
        
        // Extract card details safely
        const cardInfo = paymentData.paymentMethodData.info || {};
        const cardNetwork = cardInfo.cardNetwork || ''; // VISA, MASTERCARD, AMEX, DISCOVER, JCB
        const cardDetails = cardInfo.cardDetails || ''; // Last 4 digits
        const cardDescription = paymentData.paymentMethodData.description || '';
        
        // Normalize card brand to lowercase for consistency
        let cardBrand = cardNetwork.toLowerCase();
        // Handle special cases
        if (cardBrand === 'american express') {
            cardBrand = 'amex';
        }
        
        // If cardNetwork is not available, try to extract from description
        if (!cardBrand && cardDescription) {
            const descLower = cardDescription.toLowerCase();
            if (descLower.includes('visa')) {
                cardBrand = 'visa';
            } else if (descLower.includes('mastercard') || descLower.includes('master')) {
                cardBrand = 'mastercard';
            } else if (descLower.includes('amex') || descLower.includes('american express')) {
                cardBrand = 'amex';
            } else if (descLower.includes('discover')) {
                cardBrand = 'discover';
            }
        }
        
        console.log('Processing Google Pay payment:', {
            tokenType: typeof token,
            cardNetwork: cardNetwork,
            cardBrand: cardBrand,
            cardDetails: cardDetails,
            description: cardDescription
        });
        
        fetch('{{ route("my_cards.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify((() => {
                const params = new URLSearchParams(window.location.search);
                const redirectUrl = params.get('redirectUrl') || null;
                return {
                    payment_method_type: 'google_pay',
                    payment_method_details: {
                        card_brand: cardDescription,
                        card_type: cardBrand,
                        last4: cardDetails
                    },
                    google_pay_token: token,
                    redirectUrl: redirectUrl,
                };
            })())
        })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Server returned non-JSON response:', text.substring(0, 200));
                throw new Error('Server returned an invalid response. Please try again.');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (data.message) sessionStorage.setItem('cardAddSuccess', data.message);
                const params = new URLSearchParams(window.location.search);
                const redirectUrl = params.get('redirectUrl') || null;
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                } else {
                    window.location.reload();
                }
            } else {
                console.error('Google Pay error:', data.message || 'Unknown error');
                alert(data.message || 'Failed to add Google Pay. Please try again.');
            }
        })
        .catch(error => {
            console.error('Google Pay processing error:', error);
            alert('An error occurred while processing Google Pay. Please try again.');
        });
    }
    
    function initializePayPal() {
        // Clear any existing PayPal buttons first
        const paypalContainer = document.getElementById('paypal-button-container');
        if (!paypalContainer) {
            console.error('PayPal container not found');
            return;
        }
        
        // Check if PayPal SDK is loaded and ready
        if (typeof paypal === 'undefined' || !paypal.Buttons) {
            console.warn('PayPal SDK not ready yet, waiting...');
            if (!paypalSDKLoaded && paypalClientId && paypalClientId !== '') {
                // SDK is being loaded, wait a bit more
                setTimeout(function() {
                    initializePayPal();
                }, 500);
            } else if (!paypalClientId || paypalClientId === '') {
                paypalContainer.innerHTML = '<p class="text-gray-500 text-sm">PayPal is not available</p>';
            } else {
                paypalContainer.innerHTML = '<p class="text-gray-500 text-sm">Loading PayPal...</p>';
                // Wait a bit and try again
                setTimeout(function() {
                    initializePayPal();
                }, 500);
            }
            return;
        }
        
        // PayPal SDK is ready, initialize buttons
        initializePayPalButtons();
    }
    
    function initializePayPalButtons() {
        const paypalContainer = document.getElementById('paypal-button-container');
        if (!paypalContainer) return;
        
        paypalContainer.innerHTML = '';
        
        try {
            paypal.Buttons({
                style: {
                    layout: 'vertical',
                    color: 'gold',
                    shape: 'rect',
                    label: 'paypal'
                },
                // Only show PayPal button (credit and card are disabled in SDK URL)
                createOrder: function(data, actions) {
                    // PayPal requires minimum amount of 0.01 for vaulting
                    // Using 0.01 as the minimum amount to satisfy PayPal's validation
                    const amountValue = '0.01';
                    
                    const orderData = {
                        purchase_units: [{
                            amount: {
                                value: amountValue,
                                currency_code: 'USD'
                            },
                            description: 'ProximaRide - Save PayPal account for future payments'
                        }],
                        application_context: {
                            brand_name: 'ProximaRide',
                            landing_page: 'NO_PREFERENCE',
                            user_action: 'PAY_NOW',
                            return_url: window.location.origin + '/paypal/return',
                            cancel_url: window.location.origin + '/paypal/cancel'
                        }
                    };
                    
                    console.log('Creating PayPal order with amount:', amountValue);
                    console.log('Full order data:', JSON.stringify(orderData, null, 2));
                    
                    return actions.order.create(orderData).catch(function(error) {
                        console.error('PayPal order creation error:', error);
                        throw error;
                    });
                },
                onApprove: function(data, actions) {
                    // Close the modal when PayPal payment is approved
                    closeAddPaymentMethodModal();
                    return actions.order.capture().then(function(details) {
                        savePayPalPaymentMethod(details);
                    });
                },
                onError: function(err) {
                    console.error('PayPal error:', err);
                    paypalContainer.innerHTML = '<p class="text-red-500 text-sm">An error occurred with PayPal. Please try again.</p>';
                },
                onCancel: function(data) {
                    console.log('PayPal payment cancelled');
                }
            }).render('#paypal-button-container');
            
            console.log('PayPal buttons initialized successfully');
        } catch (error) {
            console.error('Error initializing PayPal buttons:', error);
            paypalContainer.innerHTML = '<p class="text-red-500 text-sm">Failed to initialize PayPal buttons. Please refresh the page.</p>';
        }
    }
    
    function savePayPalPaymentMethod(details) {
        fetch('{{ route("my_cards.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify((() => {
                const params = new URLSearchParams(window.location.search);
                const redirectUrl = params.get('redirectUrl') || null;
                return {
                    payment_method_type: 'paypal',
                    paypal_email: details.payer.email_address,
                    paypal_payer_id: details.payer.payer_id,
                    redirectUrl: redirectUrl,
                };
            })())
        })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Server returned non-JSON response:', text.substring(0, 200));
                throw new Error('Server returned an invalid response. Please try again.');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (data.message) sessionStorage.setItem('cardAddSuccess', data.message);
                const params = new URLSearchParams(window.location.search);
                const redirectUrl = params.get('redirectUrl') || null;
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                } else {
                    window.location.reload();
                }
            } else {
                console.error('PayPal error:', data.message || 'Unknown error');
                alert(data.message || 'Failed to add PayPal. Please try again.');
            }
        })
        .catch(error => {
            console.error('PayPal processing error:', error);
            alert('An error occurred while processing PayPal. Please try again.');
        });
    }
    
    function toggleModalCard(modalId, cardId = null) {
        let modal = document.getElementById(modalId);
        let backdrop = document.getElementById(modalId + "-backdrop");

        if (modal.classList.contains("hidden")) {
            modal.classList.remove("hidden");
            backdrop.classList.remove("hidden");

            if (cardId) {
                let deleteLink = document.getElementById("delete-card-link");
                deleteLink.href = `/delete-card/${cardId}`;
            }
        } else {
            modal.classList.add("hidden");
            backdrop.classList.add("hidden");
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Check Google Pay availability on page load
        checkAndShowGooglePay();
        
        var months = {
            1: 'January', 2: 'February', 3: 'March', 4: 'April', 5: 'May', 6: 'June',
            7: 'July', 8: 'August', 9: 'September', 10: 'October', 11: 'November', 12: 'December'
        };

        @foreach ($cards as $card)
            @if ($card->paymentMethod && $card->paymentMethod->card)
                var exp_month = {{ $card->paymentMethod->card->exp_month }};
                var exp_year = {{ $card->paymentMethod->card->exp_year }};
                var exp_date = months[exp_month] + ' ' + exp_year;
                var expElement = document.getElementById("exp_date_{{ $card->id }}");
                if (expElement) {
                    expElement.textContent = 'Expires ' + exp_date;
                }
            @endif
        @endforeach

        // Load PayPal SDK if not already loaded
        if (!paypalSDKLoaded && paypalClientId && paypalClientId !== '') {
            loadPayPalSDK();
        } else if (typeof paypal !== 'undefined' && paypal.Buttons) {
            // PayPal is already loaded, initialize it
            initializePayPal();
        } else {
            // PayPal is not configured or not available
            const paypalContainer = document.getElementById('paypal-button-container');
            if (paypalContainer && (!paypalClientId || paypalClientId === '')) {
                paypalContainer.innerHTML = '<p class="text-gray-500 text-sm">PayPal is not available</p>';
            }
        }
    });

    function closeModal() {
        const modal = document.getElementById('myModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }
    
    function closeNotificationModal() {
        closeModal();
    }

    // Show success popup when message comes from sessionStorage (AJAX add card flow)
    document.addEventListener('DOMContentLoaded', function() {
        const msg = sessionStorage.getItem('cardAddSuccess');
        if (msg) {
            const popup = document.getElementById('myModal');
            const messageEl = document.getElementById('successPopupMessage');
            if (popup && messageEl) {
                messageEl.textContent = msg;
                popup.classList.remove('hidden');
            }
            sessionStorage.removeItem('cardAddSuccess');
        }
    });

    
</script>

<style>
    .gpay-logo {
        height: 32px;
        width: auto;
    }
</style>

@endsection
