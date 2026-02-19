<div class="flex items-center gap-2 mb-3 stop-row" data-stop-index="{{ $index }}">
    <div class="relative flex-1">
        <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
            <img src="{{ asset('assets/search-bar-from.png') }}" class="w-auto h-6" alt="">
        </div>
        <input type="text" name="stop_spot_display[]" data-stop-index="{{ $index }}" id="stop_spot_{{$index}}" value="{{ $value }}" oninput="stopInput('{{$index}}')"
            class="bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
            placeholder="">
        <div id="stop_spot_suggestions{{$index}}" class="absolute left-0 right-0 bg-white shadow-lg mt-1 max-h-60 overflow-y-auto z-50"></div>
    </div>
    <button type="button" class="stop-delete-btn flex-shrink-0 p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded focus:outline-none focus:ring-2 focus:ring-red-400" onclick="confirmDeleteStop(this)" title="Delete stop" aria-label="Delete stop">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>
