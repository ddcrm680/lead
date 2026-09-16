@php
    $prefix = $prefix ?? 'pagination';
@endphp

@if ($paginator->total())

<div class="pagination-row">

    <span>
        @if ($paginator->total())
            Showing {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }}
            of {{ $paginator->total() }}
        @else
            No records
        @endif
    </span>

    <div class="pagination-controls">

        <div class="pagination-per-page">

            <label for="{{ $prefix }}PerPage">
                Per page
            </label>

            <select
                id="{{ $prefix }}PerPage"
                class="form-select form-select-sm"
            >
                @foreach ([25, 50, 100] as $size)
                    <option
                        value="{{ $size }}"
                        {{ (int) $paginator->perPage() === $size ? 'selected' : '' }}
                    >
                        {{ $size }}
                    </option>
                @endforeach
            </select>

        </div>

        @if ($paginator->lastPage() > 1)

            <div class="pagination-buttons">

                <button
                    type="button"
                    data-{{ $prefix }}-page="{{ $paginator->currentPage() - 1 }}"
                    {{ $paginator->onFirstPage() ? 'disabled' : '' }}
                >
                    <i class="bi bi-chevron-left"></i>
                </button>

                @php
                    $startPage = max(
                        1,
                        min(
                            $paginator->currentPage() - 2,
                            $paginator->lastPage() - 4
                        )
                    );

                    $endPage = min(
                        $paginator->lastPage(),
                        $startPage + 4
                    );
                @endphp

                @foreach ($paginator->getUrlRange($startPage, $endPage) as $page => $url)

                    <button
                        type="button"
                        class="{{ $page === $paginator->currentPage() ? 'active' : '' }}"
                        data-{{ $prefix }}-page="{{ $page }}"
                        @if ($page === $paginator->currentPage())
                            aria-current="page"
                        @endif
                    >
                        {{ $page }}
                    </button>

                @endforeach

                <button
                    type="button"
                    data-{{ $prefix }}-page="{{ $paginator->currentPage() + 1 }}"
                    {{ !$paginator->hasMorePages() ? 'disabled' : '' }}
                >
                    <i class="bi bi-chevron-right"></i>
                </button>

            </div>

        @endif

    </div>

</div>

@endif