<section class="hero py-4">
    <div class="container">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-lg-7">
                <h1 class="h3 mb-1">Comida perto de você</h1>
                <p class="text-muted mb-3">Descubra restaurantes e deliveries na sua região.</p>

                <div class="category-scroller" id="catScroller" role="tablist">
                    @php $selCat = request('category_slug') @endphp
                    @foreach(($categories ?? []) as $cat)
                        @php
                            $isActive = $selCat ? $selCat === $cat['slug'] : $loop->first;
                            $icon = $cat['icon'] ?? '🍽️';
                        @endphp
                        <button class="chip" role="tab" data-cat="{{ $cat['slug'] }}" aria-selected="{{ $isActive ? 'true':'false' }}">
                            {{ $icon }} {{ $cat['name'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="row g-2">
                    <div class="col-12">
                        <form id="filtersForm" method="get" action="{{ route('web.home') }}" class="d-flex gap-2">
                            <select id="selectCity" class="form-select" name="city_slug" onchange="this.form.submit()">
                                <option value="">Selecionar cidade</option>
                                @foreach(($cities ?? []) as $c)
                                    <option value="{{ $c['slug'] }}" {{ request('city_slug')===$c['slug']?'selected':'' }}>{{ $c['name'] }}</option>
                                @endforeach
                            </select>

                            @php $sort = request('sort','rating') @endphp
                            <select id="selectSort" class="form-select" name="sort" onchange="this.form.submit()">
                                <option value="rating"  {{ $sort==='rating'?'selected':'' }}>Ordenar: Relevância</option>
                                <option value="nearest" {{ $sort==='nearest'?'selected':'' }}>Mais próximos</option>
                                <option value="az"      {{ $sort==='az'?'selected':'' }}>A–Z</option>
                                <option value="za"      {{ $sort==='za'?'selected':'' }}>Z–A</option>
                                <option value="items"   {{ $sort==='items'?'selected':'' }}>Mais itens</option>
                            </select>

                            <input type="hidden" name="category_slug" id="category_slug" value="{{ request('category_slug') }}">
                            <input type="hidden" name="q" value="{{ request('q') }}">
                            <input type="hidden" name="lat" id="lat" value="{{ request('lat') }}">
                            <input type="hidden" name="lng" id="lng" value="{{ request('lng') }}">
                            <input type="hidden" name="radius_km" id="radius_km" value="{{ request('radius_km',5) }}">
                        </form>
                    </div>

                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary flex-shrink-0" id="btnNearMe"><i class="bi bi-geo"></i> Perto de mim</button>
                        </div>
                        <div class="radius-wrapper mt-2" id="radiusWrapper">
                            <label class="form-label small mb-1">Raio de busca: <span id="radiusValue">{{ (int)request('radius_km',5) }}</span> km</label>
                            <input type="range" class="form-range" min="1" max="25" step="1" value="{{ (int)request('radius_km',5) }}" id="radiusRange" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
