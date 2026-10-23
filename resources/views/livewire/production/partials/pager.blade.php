@if ($paginator->hasPages())
    <nav class="pager" aria-label="Pagination" style="display:flex;align-items:center;justify-content:space-between;gap:12px">
        <span class="dim" style="font-size:12.5px">
            Showing <b class="muted">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</b> of <b class="muted">{{ $paginator->total() }}</b>
        </span>
        <div style="display:flex;gap:6px">
            <button type="button" class="icon-btn" wire:click="previousPage" @disabled($paginator->onFirstPage()) aria-label="Previous page">
                <i class="bi bi-chevron-left"></i>
            </button>
            @foreach ($elements as $element)
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <button type="button" class="icon-btn" wire:click="gotoPage({{ $page }})"
                            @if ($page == $paginator->currentPage()) style="background:var(--gold-grad);color:#17130a;border-color:transparent" aria-current="page" @endif>
                            {{ $page }}
                        </button>
                    @endforeach
                @endif
            @endforeach
            <button type="button" class="icon-btn" wire:click="nextPage" @disabled(!$paginator->hasMorePages()) aria-label="Next page">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </nav>
@endif
