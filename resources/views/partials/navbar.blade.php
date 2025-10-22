{{-- ===================== NAV ===================== --}}
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <img src="{{ asset('img/img.png') }}" class="img-fluid" style="
    max-width: 50%; border-radius: 20px " alt="FOOOD">
        </a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMain"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navMain">
            <form class="d-none d-lg-flex ms-3 flex-grow-1" role="search" action="{{ url('/') }}">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input class="form-control" type="search" placeholder="Buscar no catálogo" name="q" value="{{ request('q') }}"/>
                </div>
            </form>
            <ul class="navbar-nav ms-lg-3 align-items-lg-center">
                <li class="nav-item"><a class="btn btn-primary" href="{{route('landing')}}">Cadastrar meu negócio</a></li>
            </ul>
        </div>
    </div>
</nav>



{{-- ===================== HERO / FILTROS ENXUTOS ===================== --}}
<section class="hero py-4">
    <div class="container">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-lg-7">
                <h1 class="h3 mb-1">Encontre deliverys e restaurantes perto de você</h1>
                <p class="text-muted mb-3">Descubra pizzarias, lanchonetes e cafés da sua região em poucos toques.</p>

                {{-- Chips (escopo visual leve) --}}
                <div class="category-scroller p-2" id="catScroller" role="">
                    @php $selCat = request('category_slug') @endphp
                    @foreach(($categories ?? []) as $cat)
                        @php
                            $isActive = $selCat ? $selCat === $cat['slug'] : $loop->first;
                            $icon = $cat['icon'] ?? '🍽️';
                        @endphp
                        <button class="chip" role="tab" data-cat="{{ $cat['slug'] }}" aria-selected="{{ $isActive ? 'true':'false' }}">
                             {{ $cat['name'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="col-12 col-lg-5">
                {{-- UMA ÚNICA BARRA DE FILTROS: cidade, ordenação e “perto de mim” --}}
                <form id="filtersForm" method="get" action="{{ route('web.home') }}" class="d-flex flex-wrap gap-2">
                    <select id="selectCity" class="form-select" name="city_slug" style="min-width:220px">
                        <option value="">Selecionar cidade</option>
                        @foreach(($cities ?? []) as $c)
                            <option
                                value="{{ $c['slug'] }}"
                                data-lat="{{ $c['lat'] ?? '' }}"
                                data-lng="{{ $c['lng'] ?? '' }}"
                                {{ request('city_slug')===$c['slug']?'selected':'' }}
                            >
                                {{ $c['name'] }}
                            </option>
                        @endforeach
                    </select>


                    @php $sort = request('sort','rating') @endphp
                    <select id="selectSort" class="form-select" name="sort" style="min-width:220px">
                        <option value="rating"  {{ $sort==='rating'?'selected':'' }}>Relevância</option>
                        <option value="nearest" {{ $sort==='nearest'?'selected':'' }}>Mais próximos</option>
                        <option value="az"      {{ $sort==='az'?'selected':'' }}>A–Z</option>
                        <option value="za"      {{ $sort==='za'?'selected':'' }}>Z–A</option>
                        <option value="items"   {{ $sort==='items'?'selected':'' }}>Mais itens</option>
                    </select>

                    <button class="btn btn-outline-secondary ms-auto" id="btnNearMe" type="button">
                        <i class="bi bi-geo"></i> Perto de mim
                    </button>

                    {{-- ocultos necessários --}}
                    <input type="hidden" name="category_slug" id="category_slug" value="{{ request('category_slug') }}">
                    <input type="hidden" name="q" value="{{ request('q') }}">
                    <input type="hidden" name="lat" id="lat" value="{{ request('lat') }}">
                    <input type="hidden" name="lng" id="lng" value="{{ request('lng') }}">
                    <input type="hidden" name="radius_km" id="radius_km" value="{{ (int)request('radius_km',5) }}">
                </form>

                {{-- Raio só aparece quando “perto de mim” está ativo --}}
                <div class="radius-wrapper mt-2" id="radiusWrapper">
                    <label class="form-label small mb-1">Raio de busca: <span id="radiusValue">{{ (int)request('radius_km',5) }}</span> km</label>
                    <input type="range" class="form-range" min="1" max="25" step="1" value="{{ (int)request('radius_km',5) }}" id="radiusRange" />
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===================== CSS LOCAL ===================== --}}


{{-- ===================== JS LOCAL (apenas comportamento essencial) ===================== --}}
<script>

    // --- helpers de distância (Haversine) e seleção da cidade mais próxima
    const toRad = d => d * Math.PI / 180;
    function distKm(aLat, aLng, bLat, bLng){
        const R = 6371; // km
        const dLat = toRad(bLat - aLat);
        const dLng = toRad(bLng - aLng);
        const s1 = Math.sin(dLat/2) ** 2 +
            Math.cos(toRad(aLat)) * Math.cos(toRad(bLat)) * Math.sin(dLng/2) ** 2;
        return 2 * R * Math.asin(Math.sqrt(s1));
    }

    function selectNearestCityFrom(lat, lng){
        if (!selectCity) return;
        const opts = selectCity.querySelectorAll('option[data-lat][data-lng]');
        let best = null, bestD = Infinity;
        opts.forEach(opt=>{
            const la = parseFloat(opt.dataset.lat);
            const lo = parseFloat(opt.dataset.lng);
            if (Number.isFinite(la) && Number.isFinite(lo)) {
                const d = distKm(lat, lng, la, lo);
                if (d < bestD) { best = opt; bestD = d; }
            }
        });
        if (best && best.value !== selectCity.value) {
            selectCity.value = best.value;
        }
    }

    (function(){
        const form        = document.getElementById('filtersForm');
        const selectCity  = document.getElementById('selectCity');
        const selectSort  = document.getElementById('selectSort');
        const hiddenCat   = document.getElementById('category_slug');
        const chips       = document.querySelectorAll('#catScroller .chip');

        const btnNearMe   = document.getElementById('btnNearMe');
        const radiusWrap  = document.getElementById('radiusWrapper');
        const radiusRange = document.getElementById('radiusRange');
        const radiusValue = document.getElementById('radiusValue');

        const latEl = document.getElementById('lat');
        const lngEl = document.getElementById('lng');
        const radiusEl = document.getElementById('radius_km');

        const submit = () => form && form.submit();

        /* selects => submit */
        selectCity?.addEventListener('change', submit);
        selectSort?.addEventListener('change', submit);

        /* chips => selecionar 1 e enviar */
        chips.forEach(chip=>{
            chip.addEventListener('click', ()=>{
                const slug = chip.dataset.cat || '';
                hiddenCat && (hiddenCat.value = slug);
                chips.forEach(c=>c.setAttribute('aria-selected','false'));
                chip.setAttribute('aria-selected','true');
                submit();
            });
        });

        /* raio (apenas quando “perto de mim” estiver ativo) */
        let t;
        radiusRange?.addEventListener('input', ()=>{
            radiusValue.textContent = radiusRange.value;
            radiusEl.value = radiusRange.value;
            clearTimeout(t); t = setTimeout(submit, 300);
        });

        /* único controle de localização */
        btnNearMe?.addEventListener('click', (e)=>{
            e.preventDefault();
            if (!navigator.geolocation) { alert('Geolocalização não suportada.'); return; }
            navigator.geolocation.getCurrentPosition(pos=>{
                latEl.value = pos.coords.latitude.toFixed(6);
                lngEl.value = pos.coords.longitude.toFixed(6);
                // ordenar por distância e mostrar raio
                if (selectSort && selectSort.value !== 'nearest') selectSort.value = 'nearest';
                radiusWrap.classList.add('radius-active');
                btnNearMe.classList.remove('btn-outline-secondary');
                btnNearMe.classList.add('btn-primary');
                submit();
            }, err=>{
                alert(err.code === 1 ? 'Permissão negada para acessar localização.' : 'Não foi possível obter sua localização.');
            }, { enableHighAccuracy:true, timeout:8000, maximumAge:60000 });
        });

        /* estado inicial (se veio pela URL) */
        const hasLatLng = (latEl?.value && lngEl?.value);
        const url = new URL(window.location.href);
        if (hasLatLng || url.searchParams.get('sort') === 'nearest') {
            radiusWrap.classList.add('radius-active');
            btnNearMe?.classList.remove('btn-outline-secondary');
            btnNearMe?.classList.add('btn-primary');
        }
    })();
</script>
