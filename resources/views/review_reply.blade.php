@extends('layouts.template')

@section('content')

<div class="container mx-auto my-14">
    <div class="w-full md:w-[70%] mx-auto px-4 md:px-0 ">
    <div class="bg-white border rounded p-4 border-gray-200 w-full shadow pb-8">
        <div class=" pb-2">
            <h1 class="mb-0">{{ $reviewSettingPage->reply_heading_label ?? "Reply"}}</h1>
        </div>
        <form method="POST" action="{{ route('review_reply.store', ['id' => $rating->id]) }}" enctype="multipart/form-data">
            @csrf
                    <div class="mt-4">
                        <label for="meeting" class="text-gray-900 font-medium text-lg mb-2"></label>
                        <textarea id="meeting" rows="5" name="reply"
                            class="block p-2.5 w-full text-gray-900 bg-white rounded border border-gray-300 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2"
                            placeholder={{ $reviewSettingPage->reply_placeholder ?? "In the 'Passenger Remarks' section, you can include specific feedback, comments, or compliments about the passenger's behavior during the ride"}}>{{ old('review') }}</textarea>
                        @error('reply')
                          <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                          </div>
                        @enderror
                    </div>
                <div class="flex justify-center mt-6">
                    <button type="submit" class="button-exp-fill w-32">{{ $reviewSettingPage->reply_submit_button_label ?? "Save"}}</button>
                </div>
        </form>
    </div>
    </div>
</div>

@endsection
