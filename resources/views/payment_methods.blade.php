@extends('layouts.template')

@section('content')
    <div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
        @include('layouts.inc.profile_sidebar')

        <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 lg:col-span-9 shadow">
            @if (session('message') || session('success'))
                <div id="myModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div
                            class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                            <div
                                class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                                <button type="button" onclick="closeModal()"
                                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                    <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                        <div class="mt-2 w-full">
                                            <p class="can-exp-p text-center">{{ session('message') ?? session('success') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                                    <a href="#" onclick="closeNotificationModal()"
                                        class="button-exp-fill w-auto">{{ $siteText['close_btn_text'] }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="my-4 pb-2 flex justify-between items-center">
                <h1 class="mb-0">{{ $paymentSettingDetail->main_heading ?? 'Payment List' }}</h1>
            </div>

            <div class="">
                @forelse($methods as $method)
                    <div class="border rounded p-3 mb-3 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            @if ($method->gateway === 'stripe')
                                @if ($method->type === 'google_pay')
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
                                            @if ($method->brand && $method->last4)
                                                <span class="text-xs text-gray-600">•••• {{ $method->last4 }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @elseif ($method->type === 'apple_pay')
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
                                            @if ($method->brand && $method->last4)
                                                <span class="text-xs text-gray-600">•••• {{ $method->last4 }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center space-x-3">
                                        @php
                                            $brand = strtolower($method->brand ?? '');
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
                                                    class="text-xs font-semibold text-gray-600">{{ strtoupper(substr($method->brand ?? 'CARD', 0, 4)) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <span
                                                class="text-sm font-semibold block">{{ ucfirst($method->brand ?? 'Card') }}</span>
                                            <span class="text-xs text-gray-600">•••• {{ $method->last4 }}</span>
                                        </div>
                                    </div>
                                @endif
                            @elseif($method->gateway === 'paypal')
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
                                        <span class="text-xs text-gray-600">{{ $method->email }}</span>
                                    </div>
                                </div>
                            @endif
                            @if ($method->is_default)
                                <span
                                    class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Primary</span>
                            @endif
                        </div>
                        <div class="flex items-center space-x-2">
                            @if (!$method->is_default)
                                <form action="{{ route('payment.methods.default', $method->id) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="bg-greenXS hover:bg-greenXS text-white text-sm rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS">Make
                                        Primary</button>
                                </form>
                            @endif
                            <form id="delete-payment-form-{{ $method->id }}"
                                action="{{ route('payment.methods.delete', $method->id) }}" method="POST"
                                class="inline">
                                @csrf
                                @method('DELETE')
                            </form>
                            <button type="button" onclick="showDeleteConfirmation({{ $method->id }})"
                                class="bg-red-500 hover:bg-red-500 text-white text-sm rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 hover:text-white text-center focus:bg-red-500 focus:text-white active:text-white active:bg-red-500">Delete</button>
                        </div>
                    </div>
                @empty
                    <div class="text-center pt-8">
                        <h2>
                            @if (isset($paymentSettingDetail->no_payment_methods_title_label))
                                {{ $paymentSettingDetail->no_payment_methods_title_label }}
                            @endif
                        </h2>
                        <p class="text-gray-600 text-center py-4">
                            @if (isset($paymentSettingDetail->no_payment_methods_text_label))
                                {{ $paymentSettingDetail->no_payment_methods_text_label }}
                            @endif
                        </p>
                    </div>
                @endforelse
                <div class="text-center pt-4">
                    <button type="button" onclick="openAddPaymentMethodModal()"
                        class="button-exp-fill">{{ $paymentSettingDetail->add_new_card_button_text ?? 'Add Payment Method' }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmationModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                onclick="closeDeleteConfirmationModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 modal-border1">
                <!-- Modal Header -->
                <div class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                    <button type="button" onclick="closeDeleteConfirmationModal()"
                        class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="mt-6 mb-6">
                    <p class="text-gray-700">Are you sure you want to remove this payment method?</p>
                </div>

                <!-- Modal Footer -->
                <div class="px-4 py-2 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                    <button type="button" onclick="closeDeleteConfirmationModal()" class="button-exp-fill">
                        Cancel
                    </button>
                    <button type="button" id="confirmDeleteButton" onclick="confirmDelete()"
                        class="button-exp-red-fill">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Payment Method Modal -->
    <div id="addPaymentMethodModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                onclick="closeAddPaymentMethodModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Add Payment Method</h3>
                    <button type="button" onclick="closeAddPaymentMethodModal()"
                        class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Notification Message -->
                <div id="payment-method-notification" class="hidden mb-4 p-4 rounded-lg border">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg id="notification-icon" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p id="notification-message" class="text-sm font-medium"></p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button type="button" onclick="hideNotification()"
                                class="inline-flex text-gray-400 hover:text-gray-500">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Buttons -->
                <div id="payment-method-buttons" class="space-y-3">
                    <!-- Apple Pay Button (only on macOS) -->
                    <button type="button" id="apple-pay-button"
                        onclick="hideNotification(); showPaymentForm('applepay')"
                        class="hidden w-full bg-black hover:bg-gray-900 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center space-x-2 transition-colors">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="white"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M17.05 20.28c-.98.95-2.05.88-3.08.4-1.09-.5-2.08-.48-3.24 0-1.44.62-2.2.44-3.06-.4C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z" />
                        </svg>
                        <span>Apple Pay</span>
                    </button>

                    <!-- Google Pay Button -->
                    <button type="button" id="google-pay-button"
                        onclick="hideNotification(); checkGooglePayAndShowForm()"
                        class="w-full bg-black hover:bg-gray-900 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center space-x-2 transition-colors">
                        <span class="text-white"></span>
                        <div class="flex items-center">
                            <svg class="gpay-logo" xmlns="http://www.w3.org/2000/svg" width="243.67" height="95.6"
                                viewBox="0 0 243.67 95.6">
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
                        <span class="text-gray-300"></span>
                    </button>

                    <!-- PayPal Button -->
                    <button type="button" onclick="hideNotification(); showPaymentForm('paypal')"
                        class="w-full bg-yellow-400 hover:bg-yellow-500 text-blue-900 font-medium py-3 px-4 rounded-lg flex items-center justify-center transition-colors">
                        <div class="w-8 mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 48 48">
                                <g clip-path="url(#a)">
                                    <path fill="#002991"
                                        d="M38.914 13.35c0 5.574-5.144 12.15-12.927 12.15H18.49l-.368 2.322L16.373 39H7.056l5.605-36h15.095c5.083 0 9.082 2.833 10.555 6.77a9.687 9.687 0 0 1 .603 3.58z" />
                                    <path fill="#60CDFF"
                                        d="M44.284 23.7A12.894 12.894 0 0 1 31.53 34.5h-5.206L24.157 48H14.89l1.483-9 1.75-11.178.367-2.322h7.497c7.773 0 12.927-6.576 12.927-12.15 3.825 1.974 6.055 5.963 5.37 10.35z" />
                                    <path fill="#008CFF"
                                        d="M38.914 13.35C37.31 12.511 35.365 12 33.248 12h-12.64L18.49 25.5h7.497c7.773 0 12.927-6.576 12.927-12.15z" />
                                </g>
                                <defs>
                                    <clipPath id="a">
                                        <path fill="#fff" d="M7.056 3h37.35v45H7.056z" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>
                        <span>PayPal Pay</span>
                    </button>

                    <!-- OR Separator -->
                    <div class="relative flex items-center my-4">
                        <div class="flex-grow border-t border-gray-300"></div>
                        <span class="flex-shrink mx-4 text-gray-500 text-sm">OR</span>
                        <div class="flex-grow border-t border-gray-300"></div>
                    </div>

                    <!-- Credit or Debit Card Button -->
                    <button type="button" onclick="hideNotification(); showPaymentForm('card')"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                        Credit or Debit Card
                    </button>
                </div>

                <!-- Payment Form Container (shown when button is clicked) -->
                <div id="payment-form-container" class="hidden mt-6">
                    <!-- Stripe Payment Element (for Card, Google Pay, Apple Pay) -->
                    <div id="stripe-payment-container" class="hidden">
                        <!-- Loading State -->
                        <div id="stripe-loading" class="text-center py-8">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            <p class="mt-2 text-sm text-gray-600">Loading payment form...</p>
                        </div>
                        <form id="payment-form" class="hidden">
                            <div id="payment-element" style="min-height: 200px;"></div>
                            <div class="mt-4 flex space-x-3">
                                <button type="button" onclick="resetPaymentForm()"
                                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors">
                                    Back
                                </button>
                                <button type="submit" id="submit-payment"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                    Add
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- PayPal Container -->
                    <div id="paypal-payment-container" class="hidden">
                        <div id="paypal-button-container"></div>
                        <div class="mt-4">
                            <button type="button" onclick="resetPaymentForm()"
                                class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors">
                                Back
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .gpay-logo {
            height: 32px;
            width: auto;
        }
    </style>
@endsection

@section('script')
    <script src="https://js.stripe.com/v3/"></script>
    <script
        src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.' . config('paypal.mode') . '.client_id') }}&currency=USD&intent=capture&vault=true&disable-funding=credit,card">
    </script>

    <script>
        // Modal functions
        function closeModal() {
            document.getElementById('myModal').style.display = 'none';
        }

        function closeNotificationModal() {
            document.getElementById('myModal').style.display = 'none';
        }

        function openAddPaymentMethodModal() {
            document.getElementById('addPaymentMethodModal').classList.remove('hidden');
            hideNotification();
            checkAndShowApplePay();
            // Optionally pre-check Google Pay availability (non-blocking)
            checkGooglePayAvailability().then(result => {
                googlePayAvailable = result.available;
                const googlePayButton = document.getElementById('google-pay-button');
                if (googlePayButton && !result.available && result.reason === 'account') {
                    // Optionally add a tooltip or visual indicator
                    googlePayButton.title =
                        'Google Pay requires a Google account with payment methods saved in Google Wallet';
                }
            });
        }

        function checkAndShowApplePay() {
            // Detect macOS
            const isMacOS = navigator.platform.toUpperCase().indexOf('MAC') >= 0 ||
                navigator.userAgent.toUpperCase().indexOf('MAC') >= 0;

            const applePayButton = document.getElementById('apple-pay-button');
            if (applePayButton) {
                if (isMacOS) {
                    applePayButton.classList.remove('hidden');
                } else {
                    applePayButton.classList.add('hidden');
                }
            }
        }

        function closeAddPaymentMethodModal() {
            document.getElementById('addPaymentMethodModal').classList.add('hidden');
            resetPaymentForm();
            hideNotification();
        }

        function showNotification(message, type = 'warning') {
            const notification = document.getElementById('payment-method-notification');
            const messageEl = document.getElementById('notification-message');
            const iconEl = document.getElementById('notification-icon');

            if (!notification || !messageEl) return;

            messageEl.textContent = message;

            // Remove all type classes
            notification.className = 'mb-4 p-4 rounded-lg border flex items-start';

            // Add type-specific styling
            if (type === 'warning') {
                notification.classList.add('bg-yellow-50', 'border-yellow-200');
                messageEl.classList.add('text-yellow-800');
                iconEl.classList.add('text-yellow-400');
            } else if (type === 'error') {
                notification.classList.add('bg-red-50', 'border-red-200');
                messageEl.classList.add('text-red-800');
                iconEl.classList.add('text-red-400');
            } else if (type === 'success') {
                notification.classList.add('bg-green-50', 'border-green-200');
                messageEl.classList.add('text-green-800');
                iconEl.classList.add('text-green-400');
                // Change icon to checkmark for success
                iconEl.innerHTML =
                    '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />';
            } else {
                notification.classList.add('bg-blue-50', 'border-blue-200');
                messageEl.classList.add('text-blue-800');
                iconEl.classList.add('text-blue-400');
            }

            notification.classList.remove('hidden');

            // Auto-hide after 5 seconds for non-warning types
            // if (type !== 'warning') {
            //     setTimeout(() => {
            //         hideNotification();
            //     }, 5000);
            // }
        }

        function hideNotification() {
            const notification = document.getElementById('payment-method-notification');
            if (notification) {
                notification.classList.add('hidden');
            }
        }

        function resetPaymentForm() {
            // Clear and close PayPal buttons first if they exist
            if (window.paypalButtons) {
                try {
                    window.paypalButtons.close();
                } catch (e) {
                    console.log('PayPal buttons already closed');
                }
                window.paypalButtons = null;
            }

            // Clear PayPal container
            const paypalContainer = document.getElementById('paypal-button-container');
            if (paypalContainer) {
                paypalContainer.innerHTML = '';
            }

            // Hide all payment form containers
            document.getElementById('payment-form-container').classList.add('hidden');
            document.getElementById('stripe-payment-container').classList.add('hidden');
            document.getElementById('paypal-payment-container').classList.add('hidden');

            // Show payment method buttons
            document.getElementById('payment-method-buttons').classList.remove('hidden');

            // Unmount Stripe elements if mounted
            if (window.paymentElement) {
                try {
                    window.paymentElement.unmount();
                } catch (e) {
                    console.log('Element already unmounted');
                }
                window.paymentElement = null;
            }

            // Reset loading state
            const loadingEl = document.getElementById('stripe-loading');
            const paymentForm = document.getElementById('payment-form');
            if (loadingEl) loadingEl.classList.remove('hidden');
            if (paymentForm) {
                paymentForm.classList.add('hidden');
                // Reset inline styles
                paymentForm.style.visibility = '';
                paymentForm.style.position = '';
                paymentForm.style.opacity = '';
            }

            // Reset form shown flag
            if (window.stripeFormShown !== undefined) {
                window.stripeFormShown = false;
            }
        }

        // Check Google Pay availability
        let googlePayAvailable = null;
        let stripe = null;

        async function checkGooglePayAvailability() {
            // Check browser support first
            const isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
            const isEdge = /Edg/.test(navigator.userAgent);
            const isOpera = /OPR/.test(navigator.userAgent);

            if (!isChrome && !isEdge && !isOpera) {
                return {
                    available: false,
                    reason: 'browser'
                };
            }

            // Check if Payment Request API is available
            if (!window.PaymentRequest) {
                return {
                    available: false,
                    reason: 'api'
                };
            }

            // Check if Stripe is loaded
            if (typeof Stripe === 'undefined') {
                return {
                    available: false,
                    reason: 'stripe'
                };
            }

            try {
                // Initialize Stripe if not already done
                if (!stripe) {
                    stripe = Stripe('{{ env('STRIPE_KEY') }}');
                }

                // Create a test Payment Request to check Google Pay availability
                const paymentRequest = stripe.paymentRequest({
                    country: 'US',
                    currency: 'usd',
                    total: {
                        label: 'Test',
                        amount: 0,
                    },
                });

                const canMakePayment = await paymentRequest.canMakePayment();
                if (canMakePayment && canMakePayment.googlePay === true) {
                    return {
                        available: true
                    };
                } else {
                    return {
                        available: false,
                        reason: 'account'
                    };
                }
            } catch (error) {
                console.error('Error checking Google Pay availability:', error);
                return {
                    available: false,
                    reason: 'error'
                };
            }
        }

        async function checkGooglePayAndShowForm() {
            // Show loading notification
            showNotification('Checking Google Pay availability...', 'info');

            const result = await checkGooglePayAvailability();

            if (result.available) {
                hideNotification();
                showPaymentForm('googlepay');
            } else {
                // Show specific error message based on reason
                let errorMessage = 'Google Pay is not available. ';

                if (result.reason === 'browser') {
                    errorMessage += 'Please use Google Chrome, Microsoft Edge, or Opera browser.';
                } else if (result.reason === 'api') {
                    errorMessage += 'Your browser does not support Payment Request API.';
                } else if (result.reason === 'account') {
                    errorMessage +=
                        'Please make sure you have a Google account with a payment method saved in Google Wallet (wallet.google.com), and that you are signed in to Chrome.';
                } else if (result.reason === 'stripe') {
                    errorMessage += 'Payment system is loading. Please try again in a moment.';
                } else {
                    errorMessage += 'Unable to verify Google Pay availability. Please try again.';
                }

                showNotification(errorMessage, 'error');
            }
        }

        function showPaymentForm(method) {
            // Hide payment method buttons
            document.getElementById('payment-method-buttons').classList.add('hidden');

            // Show payment form container
            document.getElementById('payment-form-container').classList.remove('hidden');

            if (method === 'card' || method === 'googlepay' || method === 'applepay') {
                // Show Stripe Payment Element container
                const stripeContainer = document.getElementById('stripe-payment-container');
                stripeContainer.classList.remove('hidden');

                // Reset form shown flag
                window.stripeFormShown = false;

                // Show loading state
                const loadingEl = document.getElementById('stripe-loading');
                const paymentForm = document.getElementById('payment-form');
                if (loadingEl) loadingEl.classList.remove('hidden');
                if (paymentForm) {
                    // Keep form hidden initially, but make container visible for Stripe
                    paymentForm.classList.add('hidden');
                }

                // Initialize Stripe Payment Element after ensuring container is ready
                setTimeout(() => {
                    initializeStripePayment();
                }, 150);
            } else if (method === 'paypal') {
                // Show PayPal container
                document.getElementById('paypal-payment-container').classList.remove('hidden');
                initializePayPal();
            }
        }

        // Stripe initialization
        let elements, paymentElement;
        let stripeInitialized = false;

        function initializeStripePayment() {
            const paymentForm = document.getElementById('payment-form');
            const loadingEl = document.getElementById('stripe-loading');

            // Check if already initialized and mounted
            if (stripeInitialized && window.paymentElement) {
                // Show form and hide loading
                if (loadingEl) loadingEl.classList.add('hidden');
                if (paymentForm) paymentForm.classList.remove('hidden');

                // Try to re-mount if not already mounted
                try {
                    const existingElement = document.querySelector('#payment-element > div');
                    if (!existingElement) {
                        window.paymentElement.mount("#payment-element");
                    }
                } catch (e) {
                    console.log('Element already mounted or error:', e);
                }
                return;
            }

            // Initialize Stripe
            stripe = Stripe('{{ env('STRIPE_KEY') }}');
            const options = {
                clientSecret: "{{ $clientSecret }}",
                appearance: {
                    variables: {
                        colorPrimary: '#0570de',
                    }
                }
            };

            elements = stripe.elements(options);
            window.paymentElement = elements.create("payment", {
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
                showNotification('Error: Payment form container not found.', 'error');
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
                    window.paymentElement.mount("#payment-element");
                    console.log('Payment Element mounted successfully');
                } catch (error) {
                    console.error('Error mounting Payment Element:', error);
                    // Show form anyway if mount fails
                    if (loadingEl) loadingEl.classList.add('hidden');
                    if (paymentForm) paymentForm.classList.remove('hidden');
                    showNotification('Error loading payment form. Please try again.', 'error');
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
                            const currentHeight = stripeIframe.style.height || window.getComputedStyle(
                                stripeIframe).height;
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
            window.paymentElement.on('ready', () => {
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
            });

            // Also listen for load event as backup
            window.paymentElement.on('loaderror', (event) => {
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

            stripeInitialized = true;

            // Handle form submission
            const form = document.getElementById("payment-form");
            if (form) {
                // Remove existing listeners to prevent duplicates
                const newForm = form.cloneNode(true);
                form.parentNode.replaceChild(newForm, form);

                document.getElementById("payment-form").addEventListener("submit", async (e) => {
                    e.preventDefault();
                    const submitButton = document.getElementById("submit-payment");
                    submitButton.disabled = true;
                    submitButton.textContent = "Processing...";

                    const {
                        setupIntent,
                        error
                    } = await stripe.confirmSetup({
                        elements,
                        confirmParams: {
                            return_url: "{{ route('payment.methods', ['lang' => request()->route('lang') ?? app()->getLocale()]) }}"
                        },
                        redirect: "if_required"
                    });

                    if (error) {
                        showNotification('Error: ' + error.message, 'error');
                        submitButton.disabled = false;
                        submitButton.textContent = "Add";
                    } else {
                        // Detect payment method type
                        const paymentMethod = await stripe.retrieveSetupIntent(setupIntent.client_secret);
                        const pmType = paymentMethod.setupIntent.payment_method_types[0];

                        // Send payment_method to backend
                        fetch("{{ route('payment.methods.stripe.store') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                payment_method_id: setupIntent.payment_method
                            })
                        }).then(() => {
                            window.location.href =
                                "{{ route('payment.methods', ['lang' => request()->route('lang') ?? app()->getLocale()]) }}";
                        });
                    }
                });
            }
        }

        // PayPal initialization
        window.paypalButtons = null;

        function initializePayPal() {
            const paypalContainer = document.getElementById('paypal-button-container');
            if (!paypalContainer) return;

            // Clear container
            paypalContainer.innerHTML = '';

            if (typeof paypal === 'undefined' || !paypal.Buttons) {
                paypalContainer.innerHTML = '<p class="text-gray-500 text-sm">Loading PayPal...</p>';
                setTimeout(initializePayPal, 500);
                return;
            }

            try {
                if (window.paypalButtons) {
                    window.paypalButtons.close();
                }

                window.paypalButtons = paypal.Buttons({
                    style: {
                        layout: 'vertical',
                        color: 'gold',
                        shape: 'rect',
                        label: 'paypal'
                    },
                    createOrder: function(data, actions) {
                        return actions.order.create({
                            purchase_units: [{
                                amount: {
                                    value: '0.01',
                                    currency_code: 'USD'
                                },
                                description: 'Save PayPal account for future payments'
                            }],
                            application_context: {
                                brand_name: '{{ config('app.name') }}',
                                landing_page: 'NO_PREFERENCE',
                                user_action: 'PAY_NOW'
                            }
                        });
                    },
                    onApprove: function(data, actions) {
                        return actions.order.capture().then(function(details) {
                            console.log('PayPal capture details:', details);

                            // Extract payer information
                            const payerId = details.payer?.payer_id || details.payer_id;
                            const email = details.payer?.email_address || details.payer?.email ||
                                details.email;

                            console.log('Payer ID:', payerId, 'Email:', email);

                            if (!payerId || !email) {
                                console.error('Missing PayPal payer information:', details);
                                showNotification(
                                    'Error: Could not retrieve PayPal account information. Please try again.',
                                    'error');
                                return;
                            }

                            // Save PayPal payment method
                            fetch("{{ route('payment.methods.paypal.store') }}", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                        "Accept": "application/json"
                                    },
                                    body: JSON.stringify({
                                        payer_id: payerId,
                                        email: email
                                    })
                                })
                                .then(response => {
                                    console.log('Response status:', response.status);
                                    if (!response.ok) {
                                        return response.json().then(err => {
                                            // Check if it's a duplicate error
                                            if (err.duplicate) {
                                                throw {
                                                    message: err.message,
                                                    duplicate: true
                                                };
                                            }
                                            throw new Error(err.message ||
                                                'Failed to save PayPal payment method');
                                        });
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.success) {
                                        console.log('PayPal payment method saved:', data);
                                        window.location.href =
                                            "{{ route('payment.methods', ['lang' => request()->route('lang') ?? app()->getLocale()]) }}";
                                    }
                                })
                                .catch(error => {
                                    console.error('Error saving PayPal payment method:', error);

                                    // Show warning for duplicate
                                    if (error.duplicate) {
                                        // Reset the payment form immediately
                                        resetPaymentForm();

                                        // Show warning notification in modal
                                        setTimeout(() => {
                                            showNotification(error.message +
                                                ' Please close the PayPal window and try a different payment method.',
                                                'warning');
                                        }, 100);
                                    } else {
                                        showNotification('Error saving PayPal payment method: ' +
                                            error.message, 'error');
                                    }
                                });
                        });
                    },
                    onError: function(err) {
                        console.error('PayPal error:', err);
                        paypalContainer.innerHTML =
                            '<p class="text-red-500 text-sm">An error occurred with PayPal. Please try again.</p>';
                    },
                    onCancel: function(data) {
                        console.log('PayPal payment cancelled');
                    }
                });

                window.paypalButtons.render('#paypal-button-container');
            } catch (error) {
                console.error('Error initializing PayPal:', error);
                paypalContainer.innerHTML =
                    '<p class="text-red-500 text-sm">Failed to initialize PayPal. Please refresh the page.</p>';
            }
        }

        // Close modal when clicking outside
        document.getElementById('addPaymentMethodModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddPaymentMethodModal();
            }
        });

        // Check for Apple Pay on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkAndShowApplePay();
        });

        // Delete Confirmation Modal Functions
        let deleteFormId = null;

        function showDeleteConfirmation(methodId) {
            deleteFormId = methodId;
            document.getElementById('deleteConfirmationModal').classList.remove('hidden');
        }

        function closeDeleteConfirmationModal() {
            document.getElementById('deleteConfirmationModal').classList.add('hidden');
            deleteFormId = null;
        }

        function confirmDelete() {
            if (deleteFormId) {
                const form = document.getElementById('delete-payment-form-' + deleteFormId);
                if (form) {
                    form.submit();
                }
            }
            closeDeleteConfirmationModal();
        }

        // Close modal when clicking outside
        document.getElementById('deleteConfirmationModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteConfirmationModal();
            }
        });
    </script>
@endsection
