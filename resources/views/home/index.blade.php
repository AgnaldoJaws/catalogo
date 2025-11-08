@extends('layouts.app')

@section('title', 'FeiraON.app — Conectando clientes a produtos frescos da Agricultura Familiar')

@section('content')
    <style>
        /* Hero (desktop) */
        @media (min-width: 992px) {
            .hero-logo {
                max-width: 220px !important;
                margin-top: 2rem !important;
                margin-bottom: 1rem !important;
            }

            section.text-center.py-4 {
                padding-top: 2rem !important;
                padding-bottom: 2rem !important;
            }

            section.text-center.py-4 p {
                font-size: 1.1rem !important;
                color: #6c757d;
            }
        }

        .categories-wrapper::-webkit-scrollbar,
        .filters-wrapper::-webkit-scrollbar {
            height: 6px;
        }

        .categories-wrapper::-webkit-scrollbar-thumb,
        .filters-wrapper::-webkit-scrollbar-thumb {
            background-color: #ddd;
            border-radius: 10px;
        }

        /* Categoria ativa */
        .btn-filter-category.active {
            background-color: #2ad56f !important;
            color: #fff !important;
            border-color: #2ad56f !important;
        }
    </style>

    <!-- Hero -->
    <section class="container text-center py-4">
        <img src="{{ asset('img/feira-on-sem-fundo.png') }}" alt="Logo FeiraON"
             class="img-fluid mb-2 hero-logo" style="max-width:70%; height:auto;">
        <p class="text-muted mb-0 fs-6">
            Conectando clientes a produtos frescos da Agricultura Familiar
        </p>
    </section>

    <!-- Conteúdo -->
    <main class="container-lg pb-5">
        <section class="container my-3">
            <!-- Categorias -->
            <div class="d-flex flex-nowrap overflow-auto pb-2 categories-wrapper">
                @foreach($categories as $cat)
                    @php $isActive = request('category_slug') === $cat['slug']; @endphp
                    <button class="btn btn-outline-primary rounded-pill me-2 flex-shrink-0 px-3 fw-semibold btn-filter-category {{ $isActive ? 'active' : '' }}"
                            data-category="{{ $cat['slug'] }}">
                        {{ $cat['icon'] ?? '' }} {{ $cat['name'] }}
                    </button>
                @endforeach
            </div>

            <!-- Filtros -->
            <div class="d-flex flex-nowrap overflow-auto gap-2 mt-3 pb-2 filters-wrapper align-items-center">
                <!-- Cidade -->
                <div class="flex-shrink-0">
                    <select id="filter-city"
                            class="form-select rounded-pill fw-semibold border-secondary text-secondary"
                            style="min-width: 170px;">
                        @foreach($cities as $city)
                            <option value="{{ $city['slug'] }}"
                                {{ request('city_slug', 'cananeia') === $city['slug'] ? 'selected' : '' }}>
                                {{ $city['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Busca -->
                <div class="flex-shrink-0">
                    <input type="text" id="filter-search"
                           class="form-control rounded-pill border-secondary"
                           placeholder="Buscar produtos..." value="{{ request('q') }}">
                </div>

                <!-- Ordenação -->
                <button id="filter-sort" data-sort="{{ request('sort', 'az') }}"
                        class="btn btn-outline-secondary rounded-pill fw-semibold flex-shrink-0">
                    <i class="bi bi-sort-alpha-down me-1"></i> A-Z
                </button>

                <!-- Aberto agora -->
                <button id="filter-open"
                        class="btn btn-outline-secondary rounded-pill fw-semibold flex-shrink-0 {{ request('open_now') ? 'btn-success text-white' : '' }}">
                    <i class="bi bi-clock me-1"></i> Aberto agora
                </button>

                <!-- Limpar -->
                <button id="filter-clear"
                        class="btn btn-outline-primary rounded-pill fw-semibold flex-shrink-0">
                    <i class="bi bi-x-circle me-1"></i> Limpar filtros
                </button>
            </div>
        </section>

        <!-- Cabeçalho dinâmico -->
        @php
            $citySlug = request('city_slug', 'cananeia');
            $currentCity = collect($cities)->firstWhere('slug', $citySlug);
            $cityName = $currentCity ? $currentCity['name'] : 'Cananéia';

            $selectedCategory = null;
            $selectedSlug = request('category_slug');
            if ($selectedSlug) {
                $found = collect($categories)->firstWhere('slug', $selectedSlug);
                if ($found) $selectedCategory = $found['name'];
            }
        @endphp

        <div class="d-flex justify-content-between align-items-center mt-4 mb-3 results-header">
            <h4 class="fw-bold mb-0">
                {{ $selectedCategory ? $selectedCategory : 'Produtos' }} em {{ $cityName }}
            </h4>
            <span class="text-muted small">{{ $page->total() }} resultados</span>
        </div>

        <!-- Cards -->
        <div id="pratos" class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-3 mb-4">
            @foreach($page as $biz)

                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="{{$biz['thumb_url']}}"
                             class="card-img-top img-fluid" alt="{{ $biz['business']['name'] }}">
                        <div class="card-body p-2">
                            <h6 class="card-title fw-semibold mb-1 text-truncate">{{ $biz['business']['name'] }}</h6>
                            <p class="card-text text-muted small mb-0">{{ $cityName }}</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-center">
                            <a href="{{ route('web.business.show', $biz['business']['slug']) }}"
                               class="btn btn-outline-primary rounded-pill fw-semibold w-100">
                                Conhecer
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginação -->
        <div class="d-flex justify-content-center mt-3">
            {{ $page->links('pagination::bootstrap-5') }}
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        $(function() {
            function applyFilters(params = {}) {
                let url = new URL(window.location.href);
                Object.entries(params).forEach(([key, value]) => {
                    if (value) url.searchParams.set(key, value);
                    else url.searchParams.delete(key);
                });

                $('#pratos').html(`
            <div class="text-center py-5 w-100">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        `);

                $.get(url.toString(), function(response) {
                    const html = $('<div>').html(response);
                    $('.results-header').html(html.find('.results-header').html());
                    $('#pratos').html(html.find('#pratos').html());
                    window.history.pushState({}, '', url);
                });
            }

            // Cidade
            $('#filter-city').on('change', function() {
                applyFilters({ city_slug: $(this).val() });
            });

            // Busca (com debounce + limpa filtros)
            let typingTimer;
            $('#filter-search').on('keyup', function() {
                clearTimeout(typingTimer);
                const value = $(this).val();

                typingTimer = setTimeout(() => {
                    // limpa outros filtros ao buscar
                    $('.btn-filter-category').removeClass('active btn-success text-white').addClass('btn-outline-primary');
                    $('#filter-city').val('cananeia');
                    $('#filter-open').removeClass('btn-success text-white');
                    $('#filter-sort').data('sort', 'az');
                    applyFilters({ q: value, city_slug: '', category_slug: '', open_now: '', sort: '' });
                }, 500);
            });

            // Categoria
            $('.btn-filter-category').on('click', function() {
                $('.btn-filter-category').removeClass('active btn-success text-white').addClass('btn-outline-primary');
                $(this).removeClass('btn-outline-primary').addClass('active btn-success text-white');
                applyFilters({ category_slug: $(this).data('category') });
            });

            // Ordenação
            $('#filter-sort').on('click', function() {
                const current = $(this).data('sort');
                const next = current === 'az' ? 'za' : 'az';
                $(this).data('sort', next);
                applyFilters({ sort: next });
            });

            // Aberto agora
            $('#filter-open').on('click', function() {
                $(this).toggleClass('btn-success text-white');
                const active = $(this).hasClass('btn-success');
                applyFilters({ open_now: active ? 1 : '' });
            });

            // Limpar filtros
            $('#filter-clear').on('click', function() {
                $('#filter-city').val('cananeia');
                $('#filter-search').val('');
                $('#filter-open').removeClass('btn-success text-white');
                $('.btn-filter-category').removeClass('active btn-success text-white').addClass('btn-outline-primary');
                $('#filter-sort').data('sort', 'az');
                applyFilters({ city_slug: '', category_slug: '', q: '', open_now: '', sort: '' });
            });
        });
    </script>
@endpush
