<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">
            <img src="{{ asset('img/feira-on-sem-fundo.png') }}" alt="Logo Taki" class="img-fluid" style="max-width: 120px;">
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="list-unstyled mb-0">
            <li><a href="{{route('web.home')}}" class="d-flex align-items-center py-2 text-decoration-none text-dark">
                    <i class="bi bi-house-door me-2 fs-5"></i> Início</a></li>
        </ul>
    </div>

</div>


<nav class="navbar navbar-light bg-white border-bottom shadow-sm sticky-top">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <button class="btn border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" style="box-shadow: none;">
            <i class="bi bi-list fs-4"></i>
        </button>
        <ul class="navbar-nav ms-lg-3 align-items-lg-center">
            <li class="nav-item"><a class="btn btn-primary" href="{{ route('landing') }}">Cadastrar meu comércio</a></li>
        </ul>
    </div>
</nav>
