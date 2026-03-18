@php
    $lang = getDefaultLanguage(1);
@endphp
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-center">
        <div class="flex items-center gap-2">
            <span class="relative z-0 inline-flex items-center gap-2">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="relative inline-flex items-center justify-center w-10 h-10 text-gray-500 bg-white border border-gray-300 cursor-default rounded-full leading-5" aria-hidden="true">
                                @if ($lang->direction == 'ltr')
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center justify-center w-10 h-10 text-gray-500 bg-white border border-gray-300 rounded-full leading-5 hover:text-gray-700 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition ease-in-out duration-150" aria-label="{{ __('pagination.previous') }}">
                            @if ($lang->direction == 'ltr')
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @php
                        $current = $paginator->currentPage();
                        $last = $paginator->lastPage();
                        $showDots = $last > 7; // Show dots if more than 7 pages
                    @endphp

                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center justify-center w-10 h-10 font-medium text-gray-700 bg-white border border-gray-300 cursor-default rounded-full leading-5">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($showDots)
                                    {{-- Show first 3, last 3, and current page --}}
                                    @if ($page == 1 || $page == 2 || $page == 3 || $page == $last || $page == $last-1 || $page == $last-2 || $page == $current)
                                        @if ($page == $current)
                                            <span aria-current="page">
                                                <span class="relative inline-flex items-center justify-center w-10 h-10 font-medium border border-transparent cursor-default rounded-full leading-5 bg-blue-600 text-white">{{ $page }}</span>
                                            </span>
                                        @else
                                            <a href="{{ $url }}" class="relative inline-flex items-center justify-center w-10 h-10 font-medium text-gray-700 bg-white border border-gray-300 rounded-full leading-5 hover:text-gray-900 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition ease-in-out duration-150" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                                {{ $page }}
                                            </a>
                                        @endif
                                    @elseif (($page == 4 && $current > 5) || ($page == $last-3 && $current < $last-4))
                                        <span aria-disabled="true">
                                            <span class="relative inline-flex items-center justify-center w-10 h-10 font-medium text-gray-700 bg-white border border-gray-300 cursor-default rounded-full leading-5">...</span>
                                        </span>
                                    @endif
                                @else
                                    {{-- Show all pages if less than 7 --}}
                                    @if ($page == $paginator->currentPage())
                                        <span aria-current="page">
                                            <span class="relative inline-flex items-center justify-center w-10 h-10 font-medium border border-transparent cursor-default rounded-full leading-5 bg-blue-600 text-white">{{ $page }}</span>
                                        </span>
                                    @else
                                        <a href="{{ $url }}" class="relative inline-flex items-center justify-center w-10 h-10 font-medium text-gray-700 bg-white border border-gray-300 rounded-full leading-5 hover:text-gray-900 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition ease-in-out duration-150" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center justify-center w-10 h-10 text-gray-500 bg-white border border-gray-300 rounded-full leading-5 hover:text-gray-700 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition ease-in-out duration-150" aria-label="{{ __('pagination.next') }}">
                            @if ($lang->direction == 'ltr')
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="relative inline-flex items-center justify-center w-10 h-10 text-gray-500 bg-white border border-gray-300 cursor-default rounded-full leading-5" aria-hidden="true">
                                @if ($lang->direction == 'ltr')
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </span>
                        </span>
                    @endif
                </span>
        </div>
    </nav>
@endif
