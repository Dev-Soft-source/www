@if ($paginator->hasPages())
    <nav class="inline-flex items-center space-x-2" role="navigation" aria-label="Pagination">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-300 text-gray-600 cursor-default">«</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 text-gray-700 hover:bg-gray-100">
                «
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="w-8 h-8 flex items-center justify-center text-gray-500">…</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-800 text-white">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 text-gray-700 hover:bg-gray-100">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 text-gray-700 hover:bg-gray-100">
                »
            </a>
        @else
            <span class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-300 text-gray-600 cursor-default">»</span>
        @endif
    </nav>
@endif

