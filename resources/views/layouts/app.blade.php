<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    {{-- Título global configurável via .env --}}
    <title>@yield('title', config('app.title', 'Catálogo de Comida — Sua Cidade'))</title>

    {{-- Bootstrap e ícones --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Estilo base da marca --}}
    <link href="{{ asset('css/brand-bootstrap-overrides.css') }}" rel="stylesheet">

    {{-- CSS/Head específicos da página --}}
    @stack('head')
</head>
<body>
{{-- Navbar e conteúdo principal --}}
@include('partials.navbar')

<main>
    @yield('content')
</main>

{{-- Footer padrão --}}
@include('partials.footer')

{{-- Scripts globais --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

{{-- Scripts específicos das páginas --}}
@stack('scripts')
</body>
</html>
