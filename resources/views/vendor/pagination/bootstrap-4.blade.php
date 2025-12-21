@if ($paginator->hasPages())
  <nav role="navigation" aria-label="Pagination Navigation">
    {{-- ===== Mobile (xs) — compact ===== --}}
    <ul class="pagination pagination-sm d-flex d-sm-none justify-content-between align-items-center mb-0">

      {{-- Prev --}}
      @if ($paginator->onFirstPage())
        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
          <span class="page-link" aria-hidden="true">&lsaquo;</span>
        </li>
      @else
        <li class="page-item">
          <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
            &lsaquo;
          </a>
        </li>
      @endif

      {{-- Current / Total --}}
      <li class="page-item disabled" aria-disabled="true">
        <span class="page-link">
          {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
        </span>
      </li>

      {{-- Next --}}
      @if ($paginator->hasMorePages())
        <li class="page-item">
          <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
            &rsaquo;
          </a>
        </li>
      @else
        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
          <span class="page-link" aria-hidden="true">&rsaquo;</span>
        </li>
      @endif
    </ul>

    {{-- ===== Tablet/Desktop (sm+) — full numbered ===== --}}
    <ul class="pagination d-none d-sm-flex justify-content-center flex-wrap mb-0">
      {{-- Previous Page Link --}}
      @if ($paginator->onFirstPage())
        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
          <span class="page-link" aria-hidden="true">&lsaquo;</span>
        </li>
      @else
        <li class="page-item">
          <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
        </li>
      @endif

      {{-- Pagination Elements --}}
      @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
          <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
              <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
            @else
              <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
            @endif
          @endforeach
        @endif
      @endforeach

      {{-- Next Page Link --}}
      @if ($paginator->hasMorePages())
        <li class="page-item">
          <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
        </li>
      @else
        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
          <span class="page-link" aria-hidden="true">&rsaquo;</span>
        </li>
      @endif
    </ul>
  </nav>

  {{-- Small tap-target boost on phones --}}
  <style>
    @media (max-width: 575.98px) {
      .pagination.pagination-sm .page-link {
        min-width: 44px; /* better thumb target */
        text-align: center;
      }
    }
  </style>
@endif
