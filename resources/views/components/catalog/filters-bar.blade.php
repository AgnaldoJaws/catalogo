@props([
  'cities' => [],          // [['id'=>1,'name'=>'Santos'], ...]
  'currentCity' => null,   // id atual
  'order' => 'relevance',  // 'relevance' | 'price-asc' | 'price-desc' | 'az'
  'nearMe' => false,
])

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body d-flex flex-wrap gap-2 align-items-center">
        <div class="me-2 small text-muted">Cidade:</div>
        <select id="filterCity" class="form-select form-select-sm" style="max-width:220px">
            @foreach($cities as $c)
                <option value="{{ $c['id'] }}" @selected($c['id'] == $currentCity)>{{ $c['name'] }}</option>
            @endforeach
        </select>

        <div class="vr mx-2 d-none d-md-block"></div>

        <div class="me-2 small text-muted">Ordenar:</div>
        <select id="filterOrder" class="form-select form-select-sm" style="max-width:220px">
            <option value="relevance" @selected($order==='relevance')>Relevância</option>
            <option value="price-asc"  @selected($order==='price-asc')>Preço: menor → maior</option>
            <option value="price-desc" @selected($order==='price-desc')>Preço: maior → menor</option>
            <option value="az"         @selected($order==='az')>A–Z</option>
        </select>

        <div class="ms-auto"></div>

        <button id="btnNearMe" type="button"
                class="btn btn-sm {{ $nearMe ? 'btn-primary' : 'btn-outline-secondary' }}">
            <i class="bi bi-geo-alt{{ $nearMe ? '-fill' : '' }}"></i> Perto de mim
        </button>
    </div>
</div>
