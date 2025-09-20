@php $total = method_exists($page,'total') ? $page->total() : count($page ?? []); @endphp
<div class="py-2 bg-white border-top border-bottom">
    <div class="container d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="small text-muted">
            <span id="resultCount">{{ $total }}</span>
            resultados
            @if(request('category_slug'))
                para <strong id="queryLabel">“{{ optional(collect($categories)->firstWhere('slug', request('category_slug')))['name'] ?? request('category_slug') }}”</strong>
            @else
                <strong id="queryLabel">“tudo”</strong>
            @endif
            em <strong id="cityLabel">{{ $activeCityName ?? (request('city_slug') ?: 'Sua cidade') }}</strong>
        </div>
        <a class="small link-like" href="{{ route('web.home') }}" id="btnClear">Limpar filtros</a>
    </div>
</div>
