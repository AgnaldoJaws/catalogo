@php
    use Illuminate\Support\Str;

    // número WA do negócio atual (fallback). Deixe vazio se quiser só por item/loja.
    $waNumber = '13981927262'; //preg_replace('/\D+/', '', $business['whatsapp'] ?? '');

    // helper preço em R$
    $fmt = fn($cents) => 'R$ '.number_format(($cents ?? 0)/100, 2, ',', '.');

    // lista única de categorias (opcional – usado na sidebar)
    $cats = collect($sections ?? [])->pluck('name')->filter()->unique()->values();
@endphp

    <!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $business['name'] ?? 'Empresa' }} — Cardápio</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/catalog-overrides.css') }}" rel="stylesheet">
    <link href="/css/brand-bootstrap-overrides.css" rel="stylesheet">


</head>
<body>

{{-- NAV --}}
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <img src="{{ asset('img/img.png') }}" class="img-fluid" style="
    max-width: 50%; border-radius: 20px " alt="FOOOD">
        </a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMain"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navMain">
            <form class="d-none d-lg-flex ms-3 flex-grow-1" role="search" action="{{ url('/') }}">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input class="form-control" type="search" placeholder="Buscar no catálogo" name="q" value="{{ request('q') }}"/>
                </div>
            </form>
            <ul class="navbar-nav ms-lg-3 align-items-lg-center">
                <li class="nav-item"><a class="btn btn-primary" href="{{route('landing')}}">Cadastrar meu negócio</a></li>
            </ul>
        </div>
    </div>
</nav>

{{-- HEADER DA LOJA --}}
<div class="container mt-3">
    <div class="brand-card p-3 p-md-4 rounded-4">
        <div class="d-flex align-items-start gap-3 flex-wrap">
            <img class="brand-logo" src="https://placehold.co/200x200?text=Sua Logo" alt="Logo {{ $business['name'] ?? '' }}">
            <div class="flex-grow-1">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h1 class="h4 mb-1">{{ $business['name'] ?? 'Empresa' }}</h1>
                        <div class="text-muted small">
                            {{ ($business['items_count'] ?? 0) }} itens no cardápio
                            @if(!empty($business['avg_rating'])) • Nota média {{ number_format($business['avg_rating'],1,',','.') }} @endif
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary">
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
            {{-- SIDEBAR (opcional) --}}
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
                                    <img src="{{ asset('img/img_1.png') }}"
                                         class="card-img-top" alt="{{ $it['name'] ?? 'Item' }}">
                                    <div class="card-body">
                                        <h3 class="h6 mb-1">{{ $it['name'] }}</h3>
                                        @if(!empty($it['desc']))
                                            <p class="text-muted small mb-2">{{ $it['desc'] }}</p>
                                        @endif
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="price">{{ $fmt($it['price'] ?? 0) }}</div>

                                            {{-- IMPORTANTE: loja e whatsapp por item (multi-loja) --}}
                                            <button
                                                class="btn btn-sm btn-outline-primary js-open-wa"
                                                data-bs-toggle="modal"
                                                data-bs-target="#waModal"
                                                data-item-id="{{ $it['id'] }}"
                                                data-item-name="{{ $it['name'] }}"
                                                data-item-price="{{ $fmt($it['price'] ?? 0) }}"
                                                data-price-cents="{{ (int) ($it['price'] ?? 0) }}"
                                                data-store="{{ $business['name'] ?? 'Estabelecimento' }}"
                                                data-wa="{{ preg_replace('/\D+/', '', $business['whatsapp'] ?? $waNumber) }}"
                                            >
                                                <i class="bi bi-plus-circle"></i> Adicionar
                                            </button>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12"><div class="alert alert-light border">Sem itens nesta seção.</div></div>
                        @endforelse
                    </div>
                @empty
                    <div class="alert alert-light border">Este estabelecimento ainda não cadastrou itens no cardápio.</div>
                @endforelse
            </section>
        </div>
    </div>
</main>

<footer class="footer py-4">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span class="text-muted small">V-Ribiera Food</span>
        <ul class="nav small">
            <li class="nav-item"><a class="nav-link text-muted" href="#">Termos</a></li>
            <li class="nav-item"><a class="nav-link text-muted" href="#">Privacidade</a></li>
            <li class="nav-item"><a class="nav-link text-muted" href="#">Contato</a></li>
        </ul>
    </div>
</footer>

{{-- MODAL ADICIONAR --}}
<div class="modal fade" id="waModal" tabindex="-1" aria-hidden="true">
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

{{-- FAB --}}
<button id="cartFab" class="btn  position-fixed"
        data-bs-toggle="offcanvas" data-bs-target="#cartDrawer" aria-controls="cartDrawer"
        style="right:16px; bottom:16px; z-index:1050;">
    <i class="bi bi-bag"></i> <span class="ms-1" id="cartFabCount">0</span>
</button>

{{-- OFFCANVAS CARRINHO --}}
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

            {{-- quando for multi-loja, JS injeta os botões aqui acima --}}
            <a id="cartCheckout" href="#" target="_blank" class="btn btn-success w-100">
                <i class="bi bi-whatsapp"></i> Finalizar no WhatsApp
            </a>

            <button id="cartClear" class="btn btn-outline-secondary w-100 mt-2">
                <i class="bi bi-trash"></i> Limpar carrinho
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    (function(){
        // ========= Helpers =========
        const $  = (s,ctx=document)=>ctx.querySelector(s);
        const $$ = (s,ctx=document)=>Array.from(ctx.querySelectorAll(s));
        const fmtBRL = v => (Number(v||0)/100).toLocaleString('pt-BR',{style:'currency',currency:'BRL'});
        const esc = s => (s||'').replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;', "'":'&#39;' }[m]));

        // ========= Filtros simples (buscar/ordenar por preço/nome) =========
        const sectionsEl = document.getElementById('menuSections');
        function getCards(){ return $$('.menu-card', sectionsEl).map(c=>c.closest('.col')); }
        function applyMenuFilters(){
            const q = ($('#menuSearch')?.value||'').trim().toLowerCase();
            getCards().forEach(col=>{
                const card = $('.menu-card', col);
                const title = (card.querySelector('.h6')?.textContent || '').toLowerCase();
                const desc  = (card.querySelector('.text-muted')?.textContent || '').toLowerCase();
                const ok = !q || title.includes(q) || desc.includes(q);
                col.style.display = ok ? '' : 'none';
            });
        }
        $('#menuSearch')?.addEventListener('input', applyMenuFilters);

        // ========= Carrinho multi-loja =========
        const els = {
            list:     $('#cartItems'),
            total:    $('#cartTotal'),
            fabCount: $('#cartFabCount'),
            clear:    $('#cartClear'),
            obs:      $('#cartObs'),
            checkout: $('#cartCheckout'),
            drawer:   $('#cartDrawer')
        };

        // container onde os botões por loja serão inseridos (antes do botão único)
        let multiWrap = document.getElementById('cartCheckoutMulti');
        if (!multiWrap) {
            multiWrap = document.createElement('div');
            multiWrap.id = 'cartCheckoutMulti';
            multiWrap.className = 'd-grid gap-2';
            els.checkout?.parentNode?.insertBefore(multiWrap, els.checkout);
        }

        function getGlobalWa(){
            const meta = document.querySelector('meta[name="wa-number"]')?.content || '';
            return String(meta).replace(/\D+/g,'');
        }

        const STORAGE_KEY = 'simpleCartMulti';
        const cart = {
            // item: {id,name,priceCents,qty,obs, store, wa}
            items: [],
            load(){ try{ this.items = JSON.parse(localStorage.getItem(STORAGE_KEY)||'[]'); } catch{ this.items=[]; } },
            save(){ localStorage.setItem(STORAGE_KEY, JSON.stringify(this.items)); },
            add(it){
                const k = this.items.findIndex(x =>
                    x.id===it.id && (x.obs||'')===(it.obs||'') && (x.wa||'')===(it.wa||''));
                if (k>=0) this.items[k].qty += it.qty; else this.items.push(it);
                this.save(); render(); openDrawer();
            },
            remove(i){ this.items.splice(i,1); this.save(); render(); },
            clear(){ this.items=[]; this.save(); render(); },
            totalAll(){ return this.items.reduce((s,i)=>s + i.priceCents*i.qty, 0); },
            count(){ return this.items.reduce((s,i)=>s + i.qty, 0); },
            groups(){
                const map = new Map();
                for (const it of this.items) {
                    const key = (it.wa && it.wa.length) ? `wa:${it.wa}` : `store:${it.store||'Sem loja'}`;
                    if (!map.has(key)) map.set(key, { wa: it.wa||'', store: it.store||'Sem loja', items: [], subtotal:0 });
                    const g = map.get(key);
                    g.items.push(it);
                    g.subtotal += it.priceCents * it.qty;
                }
                return Array.from(map.values());
            }
        };

        function render(){
            const groups = cart.groups();

            els.list.innerHTML = groups.length
                ? groups.map(g => `
        <div class="mb-3">
          <div class="fw-bold mb-1">${esc(g.store)}</div>
          ${g.items.map(it=>`
            <div class="d-flex align-items-start justify-content-between border rounded p-2 mb-2">
              <div class="me-2">
                <div class="fw-semibold">${esc(it.name)} <span class="text-muted">× ${it.qty}</span></div>
                <div class="small text-muted">${fmtBRL(it.priceCents)} cada${it.obs?` • ${esc(it.obs)}`:''}</div>
              </div>
              <div class="text-end">
                <div class="fw-bold">${fmtBRL(it.priceCents*it.qty)}</div>
                <button class="btn btn-sm btn-outline-secondary mt-1" data-remove-index="${cart.items.indexOf(it)}">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
          `).join('')}
          <div class="d-flex justify-content-between small text-muted">
            <span>Subtotal</span><span>${fmtBRL(g.subtotal)}</span>
          </div>
        </div>
      `).join('')
                : '<div class="text-center text-muted py-3">Seu carrinho está vazio.</div>';

            // remover
            els.list.querySelectorAll('[data-remove-index]').forEach(b=>{
                b.addEventListener('click', e=>{
                    const i = parseInt(e.currentTarget.getAttribute('data-remove-index'),10);
                    cart.remove(i);
                });
            });

            // total geral + contador
            els.total.textContent = fmtBRL(cart.totalAll());
            if (els.fabCount) els.fabCount.textContent = String(cart.count());

            updateCheckoutButtons(groups);
        }

        function updateCheckoutButtons(groups){
            if (groups.length <= 1) {
                multiWrap.innerHTML = '';
                els.checkout?.classList.remove('d-none');

                const g = groups[0];
                const wa = (g && g.wa) ? g.wa : getGlobalWa();
                const href = buildWaHref(g ? g.items : cart.items, g ? g.store : (cart.items[0]?.store||'Loja'), wa);
                const disabled = (!wa || (g ? g.items.length===0 : cart.items.length===0));

                els.checkout?.classList.toggle('disabled', disabled);
                els.checkout?.setAttribute('aria-disabled', disabled ? 'true' : 'false');
                els.checkout?.setAttribute('href', disabled ? '#' : href);
                return;
            }

            // várias lojas: cria 1 botão por loja
            els.checkout?.classList.add('d-none');
            multiWrap.innerHTML = groups.map(g=>{
                const wa = g.wa || getGlobalWa();
                const href = buildWaHref(g.items, g.store, wa);
                const disabled = !wa || g.items.length===0;
                const classes = `btn btn-success w-100 ${disabled?'disabled':''}`;
                const attr = disabled ? 'aria-disabled="true" href="#"' : `href="${href}" target="_blank"`;
                return `<a ${attr} class="${classes}"><i class="bi bi-whatsapp"></i> Finalizar na ${esc(g.store)}</a>`;
            }).join('');
        }

        function buildWaHref(items, storeName, wa){
            const lines = items.map(i => `• ${i.name} x ${i.qty} — ${fmtBRL(i.priceCents)}`);
            const total = fmtBRL(items.reduce((s,i)=>s + i.priceCents*i.qty, 0));
            const obs   = (els.obs?.value || '').trim() || '-';
            const txt =
                `Olá! Pedido para ${storeName}:%0A%0A${encodeURIComponent(lines.join('\n'))}%0A%0A`+
                `Subtotal: ${encodeURIComponent(total)}%0A`+
                `Observações: ${encodeURIComponent(obs)}%0A%0A(Enviado via catálogo)`;
            const number = String(wa||'').replace(/\D+/g,'');
            return number ? `https://wa.me/${number}?text=${txt}` : '#';
        }

        function openDrawer(){
            const oc = bootstrap.Offcanvas.getOrCreateInstance(els.drawer);
            oc.show();
        }

        // ========= Modal (preenche e adiciona com loja/wa) =========
        const waModal   = $('#waModal');
        const waTitle   = $('#waTitle');
        const waQty     = $('#waQty');
        const waObs     = $('#waObs');
        const waAddCart = $('#waAddCart');

        $$('.js-open-wa').forEach(btn=>{
            btn.addEventListener('click', ()=>{
                const name  = btn.dataset.itemName  || 'Item';
                const price = btn.dataset.itemPrice || '';
                const pc    = parseInt(btn.dataset.priceCents || '0', 10);
                const wa    = (btn.dataset.wa || getGlobalWa()).replace(/\D+/g,'');
                const store = btn.dataset.store || 'Estabelecimento';

                waTitle.textContent = price ? `Adicionar: ${name} — ${price}` : `Adicionar: ${name}`;
                waQty.value = 1; waObs.value = '';

                waAddCart.dataset.itemId = btn.dataset.itemId || Math.random().toString(36).slice(2);
                waAddCart.dataset.itemName = name;
                waAddCart.dataset.priceCents = String(pc);
                waAddCart.dataset.wa = wa;
                waAddCart.dataset.store = store;
            });
        });

        waAddCart?.addEventListener('click', ()=>{
            const id    = waAddCart.dataset.itemId;
            const name  = waAddCart.dataset.itemName || 'Item';
            const pc    = parseInt(waAddCart.dataset.priceCents || '0',10);
            const qty   = Math.max(1, parseInt(waQty.value||'1',10));
            const obs   = (waObs.value||'').trim();
            const wa    = waAddCart.dataset.wa || '';
            const store = waAddCart.dataset.store || 'Estabelecimento';

            cart.add({ id, name, priceCents: pc, qty, obs, wa, store });

            const m = bootstrap.Modal.getInstance(waModal);
            m?.hide();
        });

        // extras
        els.clear?.addEventListener('click', e=>{ e.preventDefault(); cart.clear(); });
        els.obs?.addEventListener('input', ()=> render());

        // init
        cart.load();
        render();
        applyMenuFilters();
    })();
</script>
</body>
</html>
