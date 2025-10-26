<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>@yield('title','Admin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/brand-bootstrap-overrides.css') }}" rel="stylesheet">
    <link href="{{ asset('css/catalog-overrides.css') }}" rel="stylesheet">

    <style>
        /* ===========================
           ESTILO GLOBAL DO ADMIN
           =========================== */
        :root {
            font-size: 18px;
        }

        body {
            background-color: #f8f9fa;
        }

        :focus-visible {
            outline: 3px solid #fe3d28 !important;
            outline-offset: 2px;
        }

        /* Navbar principal */
        .navbar {
            box-shadow: 0 1px 2px rgba(0,0,0,.05);
        }

        .navbar .navbar-brand {
            font-weight: 600;
            color: #111827 !important;
        }

        .navbar .nav-link {
            font-weight: 600;
            color: #444;
            padding: .75rem .75rem;
        }

        .navbar .nav-link:hover {
            color: #fe3d28;
        }

        .navbar .nav-link.active {
            color: #fe3d28;
            border-bottom: 2px solid #fe3d28;
        }

        .btn-outline-secondary {
            border-color: #d1d5db;
            color: #374151;
        }

        .btn-outline-secondary:hover {
            background: #f3f4f6;
        }

        /* Tabs inferiores no mobile */
        .mobile-tabs {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            background: #fff;
            border-top: 1px solid rgba(0, 0, 0, .1);
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
        }

        .mobile-tabs a {
            text-decoration: none;
            color: #333;
            text-align: center;
            padding: .6rem 0;
            min-height: 54px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .15rem;
            font-weight: 700;
            font-size: .95rem;
        }

        .mobile-tabs a i {
            font-size: 1.2rem;
            line-height: 1;
        }

        .mobile-tabs a.on {
            color: #fe3d28;
        }

        /* Espaço para o conteúdo não ficar escondido pelas tabs */
        @media (max-width: 991.98px) {
            main, .container {
                padding-bottom: 74px;
            }
        }
    </style>
</head>

<body class="bg-light">
@php

    $is = fn($routes) => request()->routeIs($routes) ? 'active' : '';
    $on = fn($routes) => request()->routeIs($routes) ? 'on' : '';
@endphp

{{-- Navbar desktop e tablet --}}
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="{{ route('admin.dashboard') }}">
            Painel — {{ $biz?->name ?? '' }}
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false"
                aria-label="Alternar navegação">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link {{ $is('admin.profile.*') }}" href="{{ route('admin.profile.edit') }}">
                        <i class="bi bi-person-circle me-1" aria-hidden="true"></i> Perfil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $is('admin.locations.*') }}" href="{{ route('admin.locations.index') }}">
                        <i class="bi bi-geo-alt me-1" aria-hidden="true"></i> Endereço
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $is(['admin.menu.sections.*','admin.menu.items.*']) }}"
                       href="{{ route('admin.menu.sections.index') }}">
                        <i class="bi bi-list-ul me-1" aria-hidden="true"></i> Cardápio
                    </a>
                </li>

                {{-- BOTÃO SITE --}}
                <li class="nav-item ms-2">
                    <a class="btn btn-outline-secondary btn-sm"
                       href="{{ $biz?->slug ? route('web.business.show', $biz->slug) : route('web.home') }}"
                       target="_blank"
                       rel="noopener noreferrer">
                        <i class="bi bi-house"></i> Site
                    </a>
                </li>

                {{-- BOTÃO SAIR --}}
                <li class="nav-item ms-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center">
                            <i class="bi bi-box-arrow-right me-1"></i> Sair
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>


{{-- Tabs fixas no mobile --}}
<nav class="mobile-tabs d-lg-none" role="navigation" aria-label="Menu inferior">
    <a href="{{ route('admin.profile.edit') }}" class="{{ $on('admin.profile.*') }}">
        <i class="bi bi-person-circle" aria-hidden="true"></i>
        <span>Perfil</span>
    </a>
    <a href="{{ route('admin.locations.index') }}" class="{{ $on('admin.locations.*') }}">
        <i class="bi bi-geo-alt" aria-hidden="true"></i>
        <span>Endereço</span>
    </a>
    <a href="{{ route('admin.menu.sections.index') }}" class="{{ $on(['admin.menu.sections.*','admin.menu.items.*']) }}">
        <i class="bi bi-list-ul" aria-hidden="true"></i>
        <span>Cardápio</span>
    </a>
</nav>

<div class="container py-3">
    @if(session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
