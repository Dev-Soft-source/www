@props(['paymentSettingDetail', 'cards'])

<div class="border rounded-md overflow-hidden divide-y">
    @php
        $paymentMethodOld = old('payment_method');
        $on_card = $on_paypal = $on_google_pay = $on_apple_pay = false;
        foreach ($cards as $card) {
            if ($card->payment_method_type == 'card') {
                $on_card = true;
            } elseif ($card->payment_method_type == 'paypal') {
                $on_paypal = true;
            } elseif ($card->payment_method_type == 'google_pay') {
                $on_google_pay = true;
            } elseif ($card->payment_method_type == 'apple_pay') {
                $on_apple_pay = true;
            }
        }
    @endphp
    @if (!$on_paypal)
        <div class="flex items-center justify-between p-3">
            <label for="paypal"
                class="relative flex justify-center w-full p-2 bg-[#fec43a] border-2 border-[#fec43a] rounded cursor-pointer peer-checked:border-red-500 peer-checked:border-2 peer-checked:text-red-500 hover:border-2 hover:border-red-500">
                <img class="h-8" src="{{ asset('assets/paypal_img.png') }}" alt="">
                <div class="absolute top-2 right-3 ">
                    <input type="radio" id="paypal" name="card_id" value="paypal"
                        {{ old('card_id') === 'paypal' ? 'checked' : '' }}
                        class="w-6 h-6 text-blue-600 cursor-pointer bg-white border-gray-500 rounded focus:ring-blue-500 focus:ring-2">
                </div>
            </label>
        </div>
    @endif
    @if (!$on_google_pay)
        <div class="flex items-center justify-between p-3 google-pay-option">
            <label for="google_pay"
                class="relative flex justify-center w-full p-2 bg-gray-800 border-2 border-gray-800 rounded cursor-pointer peer-checked:border-red-500 peer-checked:border-2 peer-checked:text-red-500 hover:border-2 hover:border-red-500">
                <img class="h-8" src="{{ asset('assets/google_pay_img.png') }}" alt="">
                <div class="absolute top-2 right-3 ">
                    <input type="radio" id="google_pay" name="card_id" value="google_pay"
                        {{ old('card_id') === 'google_pay' ? 'checked' : '' }}
                        class="w-6 h-6 text-blue-600 cursor-pointer bg-white border-gray-500 rounded focus:ring-blue-500 focus:ring-2">
                </div>
            </label>
        </div>
    @endif
    @if (!$on_apple_pay)
        <div class="flex items-center justify-between p-3 apple-pay-option">
            <label for="apple_pay"
                class="relative flex justify-center w-full p-2 bg-black border-2 border-black rounded cursor-pointer peer-checked:border-red-500 peer-checked:border-2 peer-checked:text-red-500 hover:border-2 hover:border-red-500">
                <img class="h-8" src="{{ asset('assets/apple_pay_img.png') }}" alt="">
                <div class="absolute top-2 right-3 ">
                    <input type="radio" id="apple_pay" name="card_id" value="apple_pay"
                        {{ old('card_id') === 'apple_pay' ? 'checked' : '' }}
                        class="w-6 h-6 text-blue-600 cursor-pointer bg-white border-gray-500 rounded focus:ring-blue-500 focus:ring-2">
                </div>
            </label>
        </div>
    @endif
    @if (!$on_card)
        <div class="p-3">
            <label for="credit_card"
                class="relative text-primary text-2xl flex items-center justify-center w-full p-2 bg-[#e0f2fe] border-2 border-blue-500 rounded cursor-pointer peer-checked:border-red-500 peer-checked:border-2 peer-checked:text-red-500 hover:border-2 hover:border-red-500">
                <div class="mr-2 text-primary">
                    <svg class="h-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <path d="M3.92969 15.8792L15.8797 3.9292" stroke="#2563ea" stroke-width="1.5"
                                stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M11.1013 18.2791L12.3013 17.0791" stroke="#2563ea" stroke-width="1.5"
                                stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M13.793 15.5887L16.183 13.1987" stroke="#2563ea" stroke-width="1.5"
                                stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                                d="M3.60127 10.239L10.2413 3.599C12.3613 1.479 13.4213 1.469 15.5213 3.569L20.4313 8.479C22.5313 10.579 22.5213 11.639 20.4013 13.759L13.7613 20.399C11.6413 22.519 10.5813 22.529 8.48127 20.429L3.57127 15.519C1.47127 13.419 1.47127 12.369 3.60127 10.239Z"
                                stroke="#2563ea" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                            <path d="M2 21.9985H22" stroke="#2563ea" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                        </g>
                    </svg>
                </div>Credit or Debit Card
                <div class="absolute top-1.5 right-3 ">
                    <input type="radio" id="credit_card" name="card_id" value="credit_card"
                        {{ old('card_id') === 'credit_card' ? 'checked' : '' }}
                        class="w-6 h-6 text-blue-600 cursor-pointer bg-white border-gray-500 rounded focus:ring-blue-500 focus:ring-2">
                </div>
            </label>
            <div id="credit-card-div" class="hidden mt-4 p-4 bg-white border border-b-4 border-gray-400 rounded">
                <div>
                    <label for="name_on_card">Cardholder’s name</label>
                    <input type="text" id="name_on_card" name="name_on_card" value="{{ old('name_on_card') }}"
                        class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                    
                    @error('name_on_card')
                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror

                </div>
                <div class="mt-4">
                    <label class="font-normal text-gray-700">
                        Card details
                    </label>
                    <div id="card-element" name="card_element"
                        class="block mt-1 border p-2.5 w-full rounded text-base md:text-lg border-gray-300">
                    </div>

                    @error('card_element')
                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror

                </div>
            </div>
        </div>
    @endif


    <div>
        @forelse($cards as $card)
                @php
                    $social_pay_option = '';
                    if ($card->payment_method_type == 'google_pay') {
                        $social_pay_option = 'google-pay-option';
                    }
                    if ($card->payment_method_type == 'apple_pay') {
                        $social_pay_option = 'apple-pay-option';
                    }
                @endphp
                <label for="card_id_{{ $card->id }}"
                    class="border rounded m-3 p-3 flex items-center justify-between cursor-pointer {{ $social_pay_option }}">
                    <div class="flex items-center space-x-3">
                        @if ($card->payment_method_type == 'card' && $card->paymentMethod && $card->paymentMethod->card)
                            @php
                                $brand = strtolower($card->paymentMethod->card->brand ?? '');
                            @endphp
                            @if ($brand === 'visa')
                                <div class="w-14 h-9 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="56" height="36"
                                        fill="none" viewBox="0 0 24 16" class="p-Logo p-Logo--md p-CardBrandIcon">
                                        <g clip-path="url(#clip0_4934_35103)">
                                            <path fill="#00579f"
                                                d="M22 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h20a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2">
                                            </path>
                                            <path fill="#fff"
                                                d="M10.367 10.91H8.85l.949-5.802h1.517zm5.501-5.66a3.8 3.8 0 0 0-1.36-.247c-1.5 0-2.555.79-2.561 1.92-.013.833.755 1.296 1.33 1.574.587.284.786.469.786.722-.006.389-.474.568-.91.568-.607 0-.931-.092-1.425-.309l-.2-.092-.212 1.302c.356.16 1.012.303 1.692.309 1.593 0 2.63-.778 2.642-1.982.006-.66-.4-1.166-1.274-1.58-.53-.265-.856-.444-.856-.716.006-.247.275-.5.874-.5.493-.012.856.105 1.13.222l.138.062z">
                                            </path>
                                            <path fill="#fff" fill-rule="evenodd"
                                                d="M18.584 5.108h1.174l1.224 5.802h-1.405l-.18-.87h-1.95c-.055.154-.318.87-.318.87h-1.592l2.254-5.32c.156-.377.431-.482.793-.482m-.093 2.124-.606 1.623h1.261c-.062-.29-.35-1.679-.35-1.679l-.106-.5a31 31 0 0 1-.2.556"
                                                clip-rule="evenodd">
                                            </path>
                                            <path fill="#fff"
                                                d="M7.582 5.108 6.096 9.065l-.162-.803c-.275-.926-1.136-1.931-2.098-2.432l1.361 5.074h1.605l2.385-5.796z">
                                            </path>
                                            <path fill="#fff"
                                                d="M4.716 5.108H2.275l-.025.118c1.904.481 3.166 1.641 3.684 3.036l-.53-2.666c-.088-.37-.357-.475-.688-.488">
                                            </path>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_4934_35103">
                                                <path fill="#fff" d="M0 0h24v16H0z">
                                                </path>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </div>
                            @elseif($brand === 'mastercard')
                                <div class="w-14 h-9 flex items-center justify-center p-1">
                                    <svg viewBox="0 0 24 16" fill="none" xmlns="http://www.w3.org/2000/svg"
                                        role="presentation" focusable="false"
                                        class="p-Logo p-Logo--md p-CardBrandIcon">
                                        <rect fill="#252525" height="16" rx="2" width="24">
                                        </rect>
                                        <circle cx="9" cy="8" fill="#eb001b" r="5">
                                        </circle>
                                        <circle cx="15" cy="8" fill="#f79e1b" r="5">
                                        </circle>
                                        <path
                                            d="M12 4c1.214.912 2 2.364 2 4s-.786 3.088-2 4c-1.214-.912-2-2.364-2-4s.786-3.088 2-4z"
                                            fill="#ff5f00"></path>
                                    </svg>
                                </div>
                            @elseif($brand === 'amex' || $brand === 'american express')
                                <div class="w-14 h-9 flex items-center justify-center p-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="56" height="36"
                                        fill="none" viewBox="0 0 24 16" class="p-Logo p-Logo--md p-CardBrandIcon">
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
                                                clip-rule="evenodd">
                                            </path>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_4934_35113">
                                                <path fill="#fff" d="M0 0h24v16H0z">
                                                </path>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </div>
                            @elseif($brand === 'discover')
                                <div class="w-14 h-9 flex items-center justify-center p-1">
                                    <svg viewBox="0 0 24 16" fill="none" xmlns="http://www.w3.org/2000/svg"
                                        role="presentation" focusable="false"
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
                                    class="text-lg font-semibold block">{{ ucfirst($card->paymentMethod->card->brand ?? 'Card') }}</span>
                                <span class="text-sm text-gray-600">••••
                                    {{ $card->paymentMethod->card->last4 }}</span>
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
                                    <span class="text-lg font-semibold block">Google Pay</span>
                                    <span class="text-sm capitalize">
                                        {{ $card->payment_method_details['card_type'] ?? '' }}
                                    </span>
                                    @php
                                        $details = is_array($card->payment_method_details)
                                            ? $card->payment_method_details
                                            : json_decode($card->payment_method_details, true);
                                    @endphp
                                    @if ($details['card_brand'] ?? (null && $details['last4'] ?? null))
                                        <span class="text-sm text-gray-600">••••
                                            {{ $details['last4'] }}</span>
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
                                    <span class="text-lg font-semibold block">Apple Pay</span>
                                    @php
                                        $details = is_array($card->payment_method_details)
                                            ? $card->payment_method_details
                                            : json_decode($card->payment_method_details, true);
                                    @endphp
                                    @if ($details['card_brand'] ?? (null && $details['last4'] ?? null))
                                        <span class="text-sm text-gray-600">••••
                                            {{ $details['last4'] }}</span>
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
                                    <span class="text-lg font-semibold block">PayPal</span>
                                    <span
                                        class="text-sm text-gray-600">{{ $card->paypal_email ?? 'PayPal account' }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        @php
                            $oldCardId = old('card_id');
                            $isChecked = $oldCardId !== null
                                ? (string) $oldCardId === (string) $card->id
                                : (bool) $card->primary_card;
                        @endphp
                        <input type="radio" id="card_id_{{ $card->id }}" name="card_id"
                            value="{{ $card->id }}" {{ $isChecked ? 'checked' : '' }}
                            class="w-6 h-6 text-blue-600 cursor-pointer bg-white border-gray-500 rounded focus:ring-blue-500 focus:ring-2">
                    </div>
                </label>
            @endforeach
            @error('card_id')
                <div class="tooltip-error shadow-lg" id="card-error">{{ $message }}</div>
            @enderror

    </div>
    @if($cards->count() == 0)
    <div class="flex justify-center items-center p-4">
        <a href="{{ route('my_cards', ['lang' => $selectedLanguage->abbreviation, 'redirectUrl' => url()->full()]) }}"
            class="button-exp-fill">{{ $paymentSettingDetail->add_new_card_button_text ?? 'Add Payment Method' }}</a>
    </div>
    @endif
</div>
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Hide a card error
    document.querySelectorAll('[name="card_id"]').forEach(function(radio) {
        radio.addEventListener('click', function() {
            if(document.getElementById('card-error')){
                document.getElementById('card-error').classList.remove('tooltip-error');
                document.getElementById('card-error').classList.add('hidden');
            }
        });
    });

            // Show/hide inline credit-card section like on coffee wall page:
            // when "Credit or Debit Card" (id="credit_card") is selected, show #credit-card-div,
            // otherwise hide it (when a saved card / PayPal / Google Pay / Apple Pay is selected).
            function toggleInlineCardSection() {
                var selected = document.querySelector('input[type="radio"][name="card_id"]:checked');
                var isCreditCardSelected = selected && selected.id === 'credit_card';
                var creditCardDiv = document.getElementById('credit-card-div');

                if (!creditCardDiv) {
                    return;
                }

                if (isCreditCardSelected) {
                    creditCardDiv.classList.remove('hidden');
                    // Initialize Stripe card element when the section is first shown
                    if (window.buyBalanceEnsureCardElement) {
                        window.buyBalanceEnsureCardElement();
                    }
                } else {
                    creditCardDiv.classList.add('hidden');
                }
            }

            var cardRadios = document.querySelectorAll('input[type="radio"][name="card_id"]');
            cardRadios.forEach(function(radio) {
                radio.addEventListener('change', toggleInlineCardSection);
            });
            // Run once on page load in case "Credit or Debit Card" is preselected
            toggleInlineCardSection();

            // Hide Apple Pay option on non-Apple devices
            var ua = navigator.userAgent || '';
            var isAppleDevice = /iPad|iPhone|iPod|Macintosh/.test(ua) && !window.MSStream;
            if (!isAppleDevice) {
                var appleContainers = document.querySelectorAll('.apple-pay-option');
                appleContainers.forEach(function(el) {
                    el.classList.add('hidden');
                });
                // If Apple Pay was pre-selected, clear selection
                var appleRadio = document.getElementById('apple_pay');
                if (appleRadio && appleRadio.checked) {
                    appleRadio.checked = false;
                }
            }

            // Hide Google Pay option when browser does not appear to support it
            // Heuristic: Android + Chrome + PaymentRequest API present
            var isAndroid = /Android/.test(ua);
            var isChrome = /Chrome/.test(ua) || /CriOS/.test(ua);
            var hasPaymentRequest = typeof window.PaymentRequest !== 'undefined';
            var isGooglePayCapable = isAndroid && isChrome && hasPaymentRequest;

            if (!isGooglePayCapable) {
                var googleContainers = document.querySelectorAll('.google-pay-option');
                googleContainers.forEach(function(el) {
                    el.classList.add('hidden');
                });
                // If Google Pay was pre-selected, clear selection
                var gpayRadio = document.getElementById('google_pay');
                if (gpayRadio && gpayRadio.checked) {
                    gpayRadio.checked = false;
                }
            }
        });

        var stripePk = '{{ env('STRIPE_KEY') ?? '' }}';
        if (stripePk && stripePk.length >= 10) {
            const stripe = Stripe(stripePk);

            // Inline card element (mirrors coffee_wall behavior) for the "Credit or Debit Card" option
            let inlineCardElement = null;
            window.buyBalanceEnsureCardElement = function() {
                if (inlineCardElement) {
                    return;
                }
                const elements = stripe.elements();
                inlineCardElement = elements.create('card', {
                    style: {
                        base: {
                            fontStyle: 'italic',
                        },
                    },
                });
                inlineCardElement.mount('#card-element');
            };
        }
</script>

