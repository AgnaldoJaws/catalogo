{{-- resources/views/business/partials/sidebar-filters-array.blade.php --}}
@php
    /** @var array $sections */
@endphp

<div class="card mb-3 filter-card">
    <div class="card-body">
        <label class="form-label mb-2">Buscar no cardápio</label>
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input id="menuSearch" class="form-control" placeholder="Ex.: calabresa, coca..." />
        </div>
    </div>
</div>

@if(!empty($sections))
    <div class="card mb-3">
        <div class="card-body">
            <div class="mb-2 fw-semibold">Categorias</div>
            <nav class="nav flex-column small">
                @foreach($sections as $section)
                    @php
                        $secName = $section['name'] ?? 'Seção';
                        $anchor  = 'cat-'.Str::slug($secName);
                    @endphp
                    <a class="nav-link py-1" href="#{{ $anchor }}">{{ $secName }}</a>
                @endforeach
            </nav>
        </div>
    </div>
@endif

<div class="card mb-3 filter-card">
    <div class="card-body">
        <div class="form-label">Preço (R$)</div>
        <div class="row g-2 align-items-center mb-2">
            <div class="col"><input id="priceMin" type="number" class="form-control form-control-sm" min="0" step="1" placeholder="mín." /></div>
            <div class="col-auto">—</div>
            <div class="col"><input id="priceMax" type="number" class="form-control form-control-sm" min="0" step="1" placeholder="máx." /></div>
        </div>

        <div class="form-label mt-3">Dietas</div>
        <div class="d-flex flex-wrap gap-2 mb-2">
            <button type="button" class="filter-pill" data-tag="veg">Vegetariano</button>
            <button type="button" class="filter-pill" data-tag="vegan">Vegano</button>
            <button type="button" class="filter-pill" data-tag="gluten-free">Sem glúten</button>
        </div>

        <label class="form-label mt-3">Tempo de preparo</label>
        <select id="prepTime" class="form-select form-select-sm">
            <option value="">Qualquer</option>
            <option value="15">Até 15 min</option>
            <option value="30">Até 30 min</option>
            <option value="45">Até 45 min</option>
        </select>

        <label class="form-label mt-3">Ordenar</label>
        <select id="menuSort" class="form-select form-select-sm">
            <option value="">Relevância</option>
            <option value="price-asc">Preço: menor → maior</option>
            <option value="price-desc">Preço: maior → menor</option>
            <option value="az">A–Z</option>
        </select>

        <div class="d-grid gap-2 mt-3">
            <button id="applyFilters" class="btn btn-sm btn-primary">Aplicar filtros</button>
            <button id="clearFilters" class="btn btn-sm btn-outline-secondary">Limpar</button>
        </div>
    </div>
</div>
