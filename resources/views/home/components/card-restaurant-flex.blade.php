@props(['loc'])

@php
    // Detecta se é array/Arrayable ou Model
    $isArray = is_array($loc) || $loc instanceof \Illuminate\Contracts\Support\Arrayable;

    // Business
    $b = $isArray ? ($loc['business'] ?? null) : ($loc->business ?? null);

    $businessName = $isArray ? ($b['name'] ?? '') : ($b->name ?? '');

    $businessSlug = $isArray ? ($b['slug'] ?? '') : ($b->slug ?? '');
    $itemsCount   = $isArray ? ($b['items_count'] ?? null) : ($b->items_count ?? null);

    // Localização / cidade
    $lat = $isArray ? ($loc['lat'] ?? null) : ($loc->lat ?? null);
    $lng = $isArray ? ($loc['lng'] ?? null) : ($loc->lng ?? null);

    $cityName = $isArray
        ? ($loc['city'] ?? ($loc['city_name'] ?? ''))
        : (optional($loc->city)->name ?? ($loc->city_name ?? ''));

    // Categorias (string para o data-attr)
    $catsString = $isArray
        ? implode(',', $b['categories'] ?? [])
        : (optional($b->categories)->pluck('name')->implode(',') ?? '');

    // Distância (se vier do repo)
    $distanceKm = $isArray ? ($loc['distance_km'] ?? null) : ($loc->distance_km ?? null);

    // Thumb (fallback para picsum)
   $thumb = $isArray ? ($loc['thumb_url'] ?? asset('img/img_1.png')) : ($loc->business->logo_src ?? asset('img/img_1.png'));

   $status  = $isArray ? ($loc['is_open_now'] ?? null) : ($loc->is_open_now ?? null);

    $isOpen  = ((int) $status) === 1;

@endphp

<div class="card card-restaurant h-100"
     data-slug="{{ $businessSlug }}"
     data-city="{{ $cityName }}"
     data-cats="{{ $catsString }}"
     data-lat="{{ $lat }}"
     data-lng="{{ $lng }}">
    <img src="{{ $thumb }}" class="card-img-top" alt="">
    <div class="position-absolute top-0 end-0 m-2">
        @if($isOpen)
            <span class="badge bg-success px-3 py-2 shadow-sm">Aberto agora</span>
        @else
            <span class="badge bg-secondary px-3 py-2 shadow-sm">Fechado</span>
        @endif
    </div>

    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h5 class="card-title mb-1">
                    <a class="text-decoration-none" href="{{ route('web.business.show', $businessSlug) }}">
                        {{ $businessName }}
                    </a>
                </h5>
                <div class="small text-muted">
                    {{ $cityName }}
                    @if($itemsCount) • {{ $itemsCount }} itens @endif
                </div>
            </div>
            <span class="badge badge-distance">
        @if(isset($distanceKm)) {{ number_format((float)$distanceKm, 1, ',', '.') }} km @else — km @endif
      </span>
        </div>
        <div class="mt-3 d-flex gap-2">
            <a href="{{ route('web.business.show', $businessSlug) }}" class="btn btn-primary btn-sm">Ver cardápio</a>
            @if($isOpen)
                <span class="badge badge-open align-self-center">Aberto agora</span>
            @endif
        </div>
    </div>
</div>
