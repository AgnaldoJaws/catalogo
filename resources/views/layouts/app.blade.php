<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title','Catálogo de Comida — Sua Cidade')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/css/brand-bootstrap-overrides.css" rel="stylesheet">

    @stack('head')
</head>
<body>
@include('partials.navbar')

@yield('content')

@include('partials.footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@stack('scripts')

<!-- CART FAB -->
{{--<button id="cartFab" class="btn btn-danger position-fixed"--}}
{{--        data-bs-toggle="offcanvas" data-bs-target="#cartDrawer" aria-controls="cartDrawer"--}}
{{--        style="right:16px; bottom:16px; z-index:1050;">--}}
{{--    <i class="bi bi-bag"></i> <span class="ms-1" id="cartFabCount">0</span>--}}
{{--</button>--}}

<!-- CART DRAWER (offcanvas) -->


</body>

</html>
