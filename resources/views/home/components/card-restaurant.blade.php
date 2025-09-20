@props(['loc'])
<div class="card card-restaurant h-100"
     data-slug="{{ $loc['business']['slug'] }}"
     data-city="{{ $loc['city'] ?? '' }}"
     data-cats="{{ implode(',', $loc['business']['categories'] ?? []) }}"
     data-lat="{{ $loc['lat'] ?? '' }}"
     data-lng="{{ $loc['lng'] ?? '' }}">
    <img src="{{ $loc['thumb_url'] ?? 'https://picsum.photos/600/400?random='.($loc['location_id'] ?? 1) }}" class="card-img-top" alt="">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h5 class="card-title mb-1">
                    <a class="text-decoration-none" href="{{ route('web.business.show', $loc['business']['slug']) }}">
                        {{ $loc['business']['name'] }}
                    </a>
                </h5>
                <div class="small text-muted">
                    {{ $loc['address'] ?? ($loc['city'] ?? '') }}
                    @if(!empty($loc['business']['items_count'])) • {{ $loc['business']['items_count'] }} itens @endif
                </div>
            </div>
            <span class="badge badge-distance">
        @if(!empty($loc['distance_km'])) {{ number_format($loc['distance_km'],1,',','.') }} km @else — km @endif
      </span>
        </div>
        <div class="mt-3 d-flex gap-2">
            <a href="{{ route('web.business.show', $loc['business']['slug']) }}" class="btn btn-primary btn-sm">Ver cardápio</a>
            @if(!empty($loc['is_open_now'])) <span class="badge badge-open align-self-center">Aberto agora</span> @endif
        </div>
    </div>
</div>
