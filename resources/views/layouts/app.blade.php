<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title','Catálogo de Comida — Sua Cidade')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root{--ifood-red:#EA1D2C;--ifood-red-600:#d21927;--ifood-ink:#2b2b2b;--ifood-muted:#6f6f6f;
            --ifood-bg:#f7f7f7;--ifood-card:#fff;--ifood-chip:#ffe9eb;--ifood-border:#ececec;}
        body{background:var(--ifood-bg);color:var(--ifood-ink);}
        .navbar{background:#fff;border-bottom:1px solid var(--ifood-border);}
        .btn-primary{background:var(--ifood-red);border-color:var(--ifood-red);}
        .btn-primary:hover{background:var(--ifood-red-600);border-color:var(--ifood-red-600);}
        .form-control:focus,.form-select:focus{border-color:var(--ifood-red);box-shadow:0 0 0 .2rem rgba(234,29,44,.15);}
        .chip{background:var(--ifood-chip);color:var(--ifood-red);border:none;padding:.45rem .9rem;border-radius:999px;font-weight:700;}
        .chip[aria-selected="true"]{background:var(--ifood-red);color:#fff;}
        .hero{background:#fff;border-bottom:1px solid var(--ifood-border);}
        .card-restaurant{border:1px solid var(--ifood-border);box-shadow:0 2px 8px rgba(0,0,0,.04);}
        .card-restaurant .card-img-top{aspect-ratio:16/9;object-fit:cover;}
        .badge-distance{background:var(--ifood-chip);color:var(--ifood-red);font-weight:600;white-space:nowrap;display:none;}
        .badge-open{background:#eaffea;color:#1b7a1b;font-weight:600;}
        .footer{border-top:1px solid var(--ifood-border);background:#fff;}
        .radius-wrapper{display:none;} .radius-active .radius-wrapper{display:block;}
        .category-scroller{overflow:auto hidden;white-space:nowrap;padding-bottom:.25rem;}
        .category-scroller .chip{margin-right:.5rem;}
        .logo-mark{width:108px;height:28px;background:var(--ifood-red);border-radius:6px;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;letter-spacing:.5px;}
    </style>
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
<button id="cartFab" type="button" class="btn btn-primary position-fixed"
        style="right:16px; bottom:16px; z-index:1040;">
    <i class="bi bi-cart3"></i> <span id="cartCount" class="badge bg-light text-dark ms-1">0</span>
</button>

<!-- CART DRAWER (offcanvas) -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartDrawer">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title"><i class="bi bi-cart3 me-2"></i>Seu pedido</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
        <div id="cartList" class="list-group list-group-flush mb-3"></div>

        <div class="mb-3">
            <label class="form-label">Observação geral</label>
            <input id="cartNote" class="form-control" placeholder="Ex.: entregar no portão, sem pimenta...">
        </div>

        <div class="mb-3">
            <label class="form-label">Endereço</label>
            <input id="cartAddress" class="form-control" placeholder="Rua, nº, bairro">
        </div>

        <div class="d-flex justify-content-between align-items-center border-top pt-3">
            <div class="fw-bold">Total</div>
            <div id="cartTotal" class="fs-5 fw-bold">R$ 0,00</div>
        </div>

        <div class="mt-3 d-grid gap-2">
            <button id="cartClear" class="btn btn-outline-secondary"><i class="bi bi-trash"></i> Limpar</button>
            <a id="cartCheckout" href="#" target="_blank" class="btn btn-success">
                <i class="bi bi-whatsapp"></i> Enviar pedido no WhatsApp
            </a>
        </div>
    </div>
</div>

</body>

</html>
