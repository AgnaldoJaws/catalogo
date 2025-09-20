{{-- resources/views/business/partials/menu-section-array.blade.php --}}
@php
    /** @var array $section */
    /** @var string|null $whatsapp */
    $secName = $section['name'] ?? 'Seção';
    $items   = $section['items'] ?? [];

    $anchor  = 'cat-'.Str::slug($secName);

    // helper preço centavos → "R$ 0,00"
    $fmt = function($cents){
        $n = (int) $cents;
        return number_format($n/100, 2, ',', '.');
    };
@endphp

@if(!empty($items))
    <h2 id="{{ $anchor }}" class="h5 mb-3 section-title">{{ $secName }}</h2>

    <div class="row row-cols-1 row-cols-md-2 g-3 mb-4 menu-row">
        @foreach($items as $it)
            @php
                $title    = $it['name'] ?? 'Item';
                $desc     = $it['desc'] ?? '';
                $priceNum = (int)($it['price'] ?? 0);       // em centavos
                $priceStr = $fmt($priceNum);               // "0,00"
                $tags     = collect($it['tags'] ?? [])->map(fn($t)=>Str::slug($t))->implode(',');
                $prep     = (int)($it['prep_min'] ?? 0);
                $img      = $it['img'] ?? 'https://picsum.photos/600/450?random=9';
            @endphp
            <div class="col">
                <div class="card menu-card h-100"
                     data-title="{{ Str::lower($title) }}"
                     data-desc="{{ Str::lower($desc) }}"
                     data-price="{{ number_format($priceNum/100, 2, '.', '') }}"
                     data-tags="{{ $tags }}"
                     data-prep="{{ $prep }}">
                    <img src="{{ $img }}" class="card-img-top" alt="">
                    <div class="card-body">
                        <h3 class="h6 mb-1">{{ $title }}</h3>
                        @if($desc)<p class="text-muted small mb-2">{{ $desc }}</p>@endif
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="price price-badge">R$ {{ $priceStr }}</div>
                            @if(!empty($whatsapp))
                                <a class="btn btn-sm btn-primary" target="_blank"
                                   href="https://wa.me/{{ $whatsapp }}?text=Quero%20{{ urlencode($title) }}">
                                    Pedir no WhatsApp
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
