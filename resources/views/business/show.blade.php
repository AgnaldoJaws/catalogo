@php
    use Illuminate\Support\Str;

    // Sanitiza número de WhatsApp (só dígitos)
    $waNumber = '13981927262';//preg_replace('/\D+/', '', $business['whatsapp'] ?? '');

    // Lista única de categorias (nomes de seções)
    $cats = collect($sections ?? [])->pluck('name')->filter()->unique()->values();
    // Helper para preço em R$
    $fmt = fn($cents) => 'R$ '.number_format(($cents ?? 0)/100, 2, ',', '.');
@endphp

    <!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $business['name'] ?? 'Empresa' }} — Cardápio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root{
            --ifood-red:#EA1D2C; --ifood-red-600:#d21927;
            --ifood-ink:#2b2b2b; --ifood-muted:#6f6f6f;
            --ifood-bg:#f7f7f7; --ifood-border:#ececec;
        }
        body{ background:var(--ifood-bg); color:var(--ifood-ink); }
        .navbar{ background:#fff; border-bottom:1px solid var(--ifood-border); }
        .btn-primary{ background:var(--ifood-red); border-color:var(--ifood-red); }
        .btn-primary:hover{ background:var(--ifood-red-600); border-color:var(--ifood-red-600); }
        .logo-mark{ width:108px; height:28px; background:var(--ifood-red); border-radius:6px; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; letter-spacing:.5px; }
        .brand-card{background:#fff;border:1px solid var(--ifood-border);box-shadow:0 4px 20px rgba(0,0,0,.06);border-radius:16px}
        .brand-logo{width:80px;height:80px;border-radius:12px;object-fit:cover;border:1px solid var(--ifood-border)}
        .sidebar{position:sticky;top:84px}
        .menu-card{border:1px solid var(--ifood-border);box-shadow:0 2px 10px rgba(0,0,0,.04)}
        .menu-card img{aspect-ratio:4/3;object-fit:cover}
        .section-title{scroll-margin-top:100px}
        .price{font-weight:700}
        .footer{border-top:1px solid var(--ifood-border);background:#fff}
        .filter-pill{border-radius:999px;border:1px solid var(--ifood-border);padding:.35rem .7rem;background:#fff}
        .filter-pill.active{background:#ffe9eb;border-color:var(--ifood-red);color:var(--ifood-red);font-weight:700}
    </style>
</head>
<body data-biz-whatsapp="{{ $business['whatsapp'] ?? '' }}">

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('web.home') }}">
            <span class="logo-mark">FOOOD</span>
        </a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMain"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navMain">
            <form class="d-none d-lg-flex ms-3 flex-grow-1" role="search" action="{{ route('web.home') }}">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input class="form-control" type="search" placeholder="Buscar no catálogo" />
                </div>
            </form>
            <ul class="navbar-nav ms-lg-3 align-items-lg-center">
                <li class="nav-item me-lg-2"><a class="nav-link" href="#">Entrar</a></li>
                <li class="nav-item"><a class="btn btn-primary" href="#">Cadastrar meu negócio</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-3">
    <div class="brand-card p-3 p-md-4 rounded-4">
        <div class="d-flex align-items-start gap-3 flex-wrap">
            <img class="brand-logo" src="{{ $business['logo_url'] ?? 'https://via.placeholder.com/200x200?text=Logo' }}" alt="Logo {{ $business['name'] ?? '' }}">
            <div class="flex-grow-1">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h1 class="h4 mb-1">{{ $business['name'] ?? 'Empresa' }}</h1>
                        <div class="text-muted small">
                            {{ ($business['items_count'] ?? 0) }} itens no cardápio
                            @if(!empty($business['avg_rating']))
                                • Nota média {{ number_format($business['avg_rating'],1,',','.') }}
                            @endif
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('web.home') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar ao catálogo
                        </a>
                        @if($waNumber)
                            <a id="btnWhatsAll" href="https://wa.me/{{ $waNumber }}" target="_blank" class="btn btn-success">
                                <i class="bi bi-whatsapp"></i> WhatsApp
                            </a>
                        @endif
                    </div>
                </div>
                @if(!empty($business['about']))
                    <div class="mt-2 text-muted">{{ $business['about'] }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<main class="py-4">
    <div class="container">
        <div class="row g-4">
            {{-- SIDEBAR --}}
            <aside class="col-lg-3">
                <div class="sidebar">
                    <div class="card mb-3">
                        <div class="card-body">
                            <label class="form-label mb-2">Buscar no cardápio</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                <input id="menuSearch" class="form-control" placeholder="Ex.: calabresa, coca..." />
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="mb-2 fw-semibold">Categorias</div>
                            <nav class="nav flex-column small">
                                @forelse($cats as $cat)
                                    <a class="nav-link py-1" href="#cat-{{ Str::slug($cat) }}">{{ $cat }}</a>
                                @empty
                                    <span class="text-muted small">Sem categorias.</span>
                                @endforelse
                            </nav>
                        </div>
                    </div>

                    <div class="card mb-3">
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
                </div>
            </aside>

            {{-- CONTEÚDO / SEÇÕES --}}
            <section class="col-lg-9" id="menuSections">
                @forelse($sections as $section)
                    <h2 id="cat-{{ Str::slug($section['name']) }}" class="h5 mb-3 section-title">{{ $section['name'] }}</h2>
                    <div class="row row-cols-1 row-cols-md-2 g-3 mb-4">
                        @forelse($section['items'] as $it)
                            <div class="col">
                                <div class="card menu-card h-100">
                                    <img src="{{ $it['img'] ?? 'https://via.placeholder.com/600x400?text=Sem+imagem' }}"
                                         class="card-img-top" alt="{{ $it['name'] ?? 'Item' }}">
                                    <div class="card-body">
                                        <h3 class="h6 mb-1">{{ $it['name'] }}</h3>
                                        @if(!empty($it['desc']))
                                            <p class="text-muted small mb-2">{{ $it['desc'] }}</p>
                                        @endif
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="price">{{ $fmt($it['price'] ?? 0) }}</div>
                                            @if($waNumber)
                                                <button
                                                    class="btn btn-sm btn-outline-primary js-open-wa"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#waModal"
                                                    data-item-id="{{ $it['id'] }}"
                                                    data-item-name="{{ $it['name'] }}"
                                                    data-item-price="{{ $fmt($it['price'] ?? 0) }}"
                                                    data-price-cents="{{ (int) ($it['price'] ?? 0) }}"
                                                >
                                                    <i class="bi bi-plus-circle"></i> Adicionar
                                                </button>

                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12"><div class="alert alert-light border">Sem itens nesta seção.</div></div>
                        @endforelse
                    </div>
                @empty
                    <div class="alert alert-light border">
                        Este estabelecimento ainda não cadastrou itens no cardápio.
                    </div>
                @endforelse
            </section>
        </div>
    </div>
</main>

<footer class="footer py-4">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span class="text-muted small">© 2025 Catálogo</span>
        <ul class="nav small">
            <li class="nav-item"><a class="nav-link text-muted" href="#">Termos</a></li>
            <li class="nav-item"><a class="nav-link text-muted" href="#">Privacidade</a></li>
            <li class="nav-item"><a class="nav-link text-muted" href="#">Contato</a></li>
        </ul>
    </div>
</footer>

{{-- MODAL WHATSAPP (único, reusado para todos os itens) --}}
<div class="modal fade" id="waModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="waTitle" class="modal-title">Adicionar item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label small">Quantidade</label>
                    <input id="waQty" type="number" min="1" value="1" class="form-control">
                </div>
                <div class="mb-0">
                    <label class="form-label small">Observações deste item</label>
                    <textarea id="waObs" class="form-control" rows="2" placeholder="Ex.: sem maionese"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button id="waAddCart" class="btn btn-primary">
                    <i class="bi bi-cart-plus"></i> Adicionar ao carrinho
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/business-cart.js') }}"></script>
@endpush


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function(){
        const $root     = document.body;
        const waNumber  = $root.dataset.wa || "";
        const $sections = document.getElementById('menuSections');

        // ====== FILTROS (front-only) ======
        const getCards = () => [...$sections.querySelectorAll('.menu-card')];

        function applyMenuFilters(){
            const q     = (document.getElementById('menuSearch').value||'').trim().toLowerCase();
            const pMin  = parseFloat((document.getElementById('priceMin').value||'').replace(',','.'));
            const pMax  = parseFloat((document.getElementById('priceMax').value||'').replace(',','.'));
            const sort  = document.getElementById('menuSort').value;
            const tags  = [...document.querySelectorAll('.filter-pill.active')].map(b=>b.dataset.tag);

            getCards().forEach(card=>{
                const title = (card.querySelector('.h6')?.textContent || '').toLowerCase();
                const desc  = (card.querySelector('.text-muted')?.textContent || '').toLowerCase();
                const priceText = card.querySelector('.price')?.textContent || 'R$ 0,00';
                const price = parseFloat(priceText.replace(/[R$\s.]/g,'').replace(',','.')) || 0;

                let ok = true;
                if (q) ok = title.includes(q) || desc.includes(q);
                if (ok && !Number.isNaN(pMin)) ok = price >= pMin;
                if (ok && !Number.isNaN(pMax)) ok = price <= pMax;
                if (ok && tags.length) {
                    // se quiser tags reais, inclua em data-attributes no card
                    ok = true;
                }
                card.closest('.col').style.display = ok ? '' : 'none';
            });

            if (sort) {
                const rows = [...$sections.querySelectorAll('.row')];
                rows.forEach(row=>{
                    const cols = [...row.children];
                    cols.sort((a,b)=>{
                        const ca=a.querySelector('.menu-card'), cb=b.querySelector('.menu-card');
                        if (!ca || !cb) return 0;
                        const ta=(ca.querySelector('.h6')?.textContent || '').toLowerCase();
                        const tb=(cb.querySelector('.h6')?.textContent || '').toLowerCase();
                        const pa=parseFloat((ca.querySelector('.price')?.textContent||'').replace(/[R$\s.]/g,'').replace(',','.'))||0;
                        const pb=parseFloat((cb.querySelector('.price')?.textContent||'').replace(/[R$\s.]/g,'').replace(',','.'))||0;
                        if (sort==='price-asc')  return pa-pb;
                        if (sort==='price-desc') return pb-pa;
                        if (sort==='az') return ta>tb?1:-1;
                        return 0;
                    });
                    cols.forEach(c=>row.appendChild(c));
                });
            }
        }

        document.getElementById('applyFilters')?.addEventListener('click', applyMenuFilters);
        document.getElementById('clearFilters')?.addEventListener('click', ()=>{
            document.getElementById('menuSearch').value='';
            document.getElementById('priceMin').value='';
            document.getElementById('priceMax').value='';
            document.getElementById('menuSort').value='';
            document.querySelectorAll('.filter-pill.active').forEach(b=>b.classList.remove('active'));
            applyMenuFilters();
        });
        document.querySelectorAll('.filter-pill').forEach(b=>{
            b.addEventListener('click', ()=> b.classList.toggle('active'));
        });
        document.getElementById('menuSearch')?.addEventListener('input', applyMenuFilters);

        // ====== MODAL WHATSAPP ======
        const waModal     = document.getElementById('waModal');
        const waItemName  = document.getElementById('waItemName');
        const waItemPrice = document.getElementById('waItemPrice');
        const waQty       = document.getElementById('waQty');
        const waObs       = document.getElementById('waObs');
        const waSendBtn   = document.getElementById('waSendBtn');

        // Preenche modal quando clica no botão "Pedir"
        document.querySelectorAll('.js-open-wa').forEach(btn=>{
            btn.addEventListener('click', ()=>{
                const name  = btn.dataset.itemName || 'Item';
                const price = btn.dataset.itemPrice || '';
                waItemName.textContent  = name;
                waItemPrice.textContent = price ? `• ${price}` : '';
                waQty.value = 1;
                waObs.value = '';
                // prepara link final quando o modal é aberto
                const updateHref = ()=>{
                    const qty = Math.max(1, parseInt(waQty.value||'1',10));
                    const obs = waObs.value?.trim() || '-';
                    const text = `Olá! Quero fazer um pedido:%0A%0A• Item: ${encodeURIComponent(name)}%0A• Quantidade: ${qty}%0A• Observações: ${encodeURIComponent(obs)}%0A%0A(Enviado via catálogo)`;
                    const href = waNumber ? `https://wa.me/${waNumber}?text=${text}` : '#';
                    waSendBtn.href = href;
                };
                updateHref();
                waQty.addEventListener('input', updateHref, { once:false });
                waObs.addEventListener('input', updateHref, { once:false });
            });
        });

        // primeira aplicação dos filtros
        applyMenuFilters();
    })();
</script>

<!-- CART FAB -->
<button id="cartFab" class="btn btn-danger position-fixed"
        style="right:16px; bottom:16px; z-index:1050; border-radius:999px; box-shadow:0 6px 24px rgba(0,0,0,.2)">
    <i class="bi bi-bag"></i> <span class="ms-1" id="cartFabCount">0</span>
</button>

<!-- CART DRAWER -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartDrawer">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Seu pedido</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
        <div id="cartItems" class="vstack gap-2"></div>

        <div class="mt-auto border-top pt-3">
            <div class="d-flex justify-content-between mb-2">
                <strong>Total</strong>
                <strong id="cartTotal">R$ 0,00</strong>
            </div>
            <div class="mb-2">
                <label class="form-label small">Observações gerais</label>
                <textarea id="cartObs" class="form-control" rows="2" placeholder="Ex.: tirar cebola, ponto da carne..."></textarea>
            </div>
            <button id="cartClear" class="btn btn-outline-secondary w-100 mb-2">
                <i class="bi bi-trash"></i> Limpar carrinho
            </button>
            <a id="cartCheckout" href="#" target="_blank" class="btn btn-success w-100">
                <i class="bi bi-whatsapp"></i> Finalizar no WhatsApp
            </a>
        </div>
    </div>
</div>

</body>
</html>
