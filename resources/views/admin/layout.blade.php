<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>@yield('title','Admin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('admin.dashboard',['business'=>$biz->id]) }}">Painel — {{ $biz->name }}</a>
        <div class="ms-auto">
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('web.home') }}"><i class="bi bi-house"></i> Site</a>
        </div>
    </div>
</nav>

<div class="container py-3">
    @if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
