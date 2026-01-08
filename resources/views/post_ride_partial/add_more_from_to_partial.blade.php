<div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4 remove-row{{$index}}">

    @if(null !== old('ride_detail_ids'))
        <input type="hidden" name="ride_detail_ids[]" value="{{old('ride_detail_ids')[(int)$index-1]}}">
    @elseif ($type == "edit")
        <input type="hidden" name="ride_detail_ids[]" value="{{ isset($ride_detail) ? $ride_detail->id : "0" }}">
    @else
    <input type="hidden" name="ride_detail_ids[]" value="0">
    @endif
    
    <div class="">
        <label for="">
            @isset($postRidePage->from_label)
                {{ $postRidePage->from_label }}
            @endisset
        </label>

        <div class="relative mt-2">
            <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                <img src="{{ asset('assets/search-bar-from.png') }}" class="w-auto h-6" alt="">
            </div>

            @php
                $departure = "";
                if(null !== old('from_spot')){
                    $departure = old('from_spot')[(int)$index-1];
                }else if($type == "repost"){
                    $departure = isset($ride_detail) ? $ride_detail->destination : "";
                }else{
                    $departure = isset($ride_detail) ? $ride_detail->departure : "";
                }
            @endphp

            <input type="text" name="from_spot[]"  id="from_spot_{{$index}}" oninput="fromInput('{{$index}}')" value="{{ $departure }}"
                class="bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 mt-2"
                @isset($postRidePage->from_placeholder)
                    placeholder="{{ $postRidePage->from_placeholder }}"
                @endisset>

            <!-- Suggestions Container for 'from' field -->
            <div id="from_spot_suggestions{{$index}}" class="absolute left-0 right-0 bg-white shadow-lg mt-1 max-h-60 overflow-y-auto z-50"></div>
        </div>
        
        <div class="from_spot_error_{{$index}} {{ !empty($fromSpotError['from_spot']) && $fromSpotError['from_spot']!='' ? "" : "hidden"  }}  relative tooltip -bottom-4 group-hover:flex">
            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-primary text-gray-600 w-full md:w-1/2 rounded" >
                <p class="from_spot_error_message text-white leading-none text-sm lg:text-base">{{ $fromSpotError['from_spot'] ?? "" }}</p>
            </div>
          </div>
        @error('from_spot[]')
          <div class="from_spot_error relative tooltip -bottom-4 group-hover:flex">
            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-primary text-gray-600 w-full md:w-1/2 rounded" >
                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
            </div>
          </div>
        @enderror
    </div>
    <div class="">
        <label for="">
            @isset($postRidePage->to_label)
                {{ $postRidePage->to_label }}
            @endisset
        </label>

        <div class="relative mt-2">
            <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                <img src="{{ asset('images/new-21-search-bar-to.png') }}" class="w-4 h-6" alt="">
            </div>

            @php
                $destination = "";
                if(null !== old('to_spot')){
                    $destination = old('to_spot')[(int)$index-1];
                }elseif($type == "repost"){
                    $destination = isset($ride_detail) ? $ride_detail->departure : "";
                }else{
                    $destination = isset($ride_detail) ? $ride_detail->destination : "";
                }
            @endphp

            <input type="text" name="to_spot[]"  id="to_spot_{{$index}}" value="{{ $destination }}" oninput="toInput('{{$index}}')"
                class="bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 mt-2"
                @isset($postRidePage->to_placeholder)
                    placeholder="{{ $postRidePage->to_placeholder }}"
                @endisset>
            <!-- Suggestions Container for 'from' field -->
            <div id="to_spot_suggestions{{$index}}" class="absolute left-0 right-0 bg-white shadow-lg mt-1 max-h-60 overflow-y-auto z-50"></div>
        </div>
        <div class="to_spot_error_{{$index}} {{ !empty($toSpotError['to_spot']) && $toSpotError['to_spot']!='' ? "" : "hidden"  }}   relative tooltip -bottom-4 group-hover:flex">
            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-primary text-gray-600 w-full md:w-1/2 rounded" >
                <p class="to_spot_error_message text-white leading-none text-sm lg:text-base">{{ $toSpotError['to_spot'] ?? "" }}</p>
            </div>
          </div>
        @error('to_spot[]')
          <div class="relative tooltip -bottom-4 group-hover:flex">
            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-primary text-gray-600 w-full md:w-1/2 rounded" >
                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
            </div>
          </div>
        @enderror
    </div>
    {{-- {{ dd($fromSpotError) }} --}}
    <div class="">
        <label for="">
            @isset($postRidePage->price_per_seat_label)
                {{ $postRidePage->price_per_seat_label }}
            @endisset
        </label>
        @php
        $price ="";
            if(null !== old('price_spot')){
                    $departure = old('price_spot')[(int)$index-1];
            }else{
                $price = isset($ride_detail) ? $ride_detail->price : "";
            }
        @endphp
       <div class="relative mt-2">

           <input type="number" step="any" id="price_{{$index}}" value="{{ $price }}" name="price_spot[]" id="priceData{{$index}}" class="bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 mt-2" />
           <div class="price_{{$index}} {{ !empty($fromSpotError['price']) && $fromSpotError['price']!='' ? "" : "hidden"  }}  relative tooltip -bottom-4 group-hover:flex">
            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-primary text-gray-600 w-full md:w-1/2 rounded" >
                <p class="price_message text-white leading-none text-sm lg:text-base">{{ $fromSpotError['price'] ?? "" }}</p>
            </div>
          </div>
    </div>
    </div>
    <div class="flex items-center gap-2">
        {{-- <div class="w-full">
        <label for="">Time</label>
            <input type="text" class="bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 mt-2" />
        </div> --}}
        <div>
            @php
                $rideDetailId = 0;
                if(null !== old('ride_detail_ids')){
                    $rideDetailId = old('ride_detail_ids')[(int)$index-1];
                }else if ($type == "edit") {
                    $rideDetailId = isset($ride_detail) ? $ride_detail->id : 0;
                }
            @endphp
            <button type="button" onclick="removeRow('{{$index}}', '{{$rideDetailId}}')" class="button-exp-fill mt-8 py-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</div>