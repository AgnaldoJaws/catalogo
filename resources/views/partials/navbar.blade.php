<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="">
            <span class="logo-mark">FOOOD</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <form class="d-none d-lg-flex ms-3 flex-grow-1" method="get" action="">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input class="form-control" name="q" value="{{ request('q') }}" type="search" placeholder="Buscar por prato, restaurante ou palavra-chave"/>
                </div>
            </form>
            <ul class="navbar-nav ms-lg-3 align-items-lg-center">
                <li class="nav-item me-lg-2"><a class="nav-link" href="#">Entrar</a></li>
                <li class="nav-item"><a class="btn btn-primary" href="#">Cadastrar meu negócio</a></li>
            </ul>
        </div>
    </div>
</nav>

{{-- Barra cidade atual --}}
@php
    $activeCity = collect($cities ?? [])->firstWhere('slug', request('city_slug'));
    $activeCityName = $activeCity['name'] ?? (request('city_slug') ?: 'Sua cidade');
@endphp
<div class="py-2 border-bottom bg-white">
    <div class="container d-flex flex-wrap align-items-center gap-2">
        <span class="text-muted small">📍 Cidade atual:</span>
        <button class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-geo-alt"></i> <span id="cityCurrent">{{ $activeCityName }}</span>
        </button>
        <span class="ms-2 small"><a href="#" class="link-like" id="btnUseLocation"><i class="bi bi-crosshair"></i> Usar minha localização</a></span>
    </div>
</div>
