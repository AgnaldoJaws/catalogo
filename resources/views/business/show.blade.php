@php
    use Illuminate\Support\Str;

        $waNumber = preg_replace('/\D+/', '', $business['whatsapp'] ?? '');
        $fmt = fn($cents) => 'R$ '.number_format(($cents ?? 0)/100, 2, ',', '.');
        $cats = collect($sections ?? [])->pluck('name')->filter()->unique()->values();
        $img = $business['logo_url'] ?? asset('img/img_1.png');
    @endphp

    <!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $business['name'] ?? 'Empresa' }} — Cardápio</title>

    <meta name="wa-number" content="{{ $waNumber }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="order-endpoint" content="{{ url('/orders/snapshot') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/catalog-overrides.css') }}" rel="stylesheet">
    <link href="/css/brand-bootstrap-overrides.css" rel="stylesheet">
</head>
<body>

@include('partials.navbar')

<div class="container mt-3">
    <div class="brand-card p-3 p-md-4 rounded-4">
        <div class="d-flex align-items-start gap-3 flex-wrap">
            <img src="{{$business['logo_url']}}" class="img-fluid" style="max-width:20%; border-radius:20px" alt="Logo">
            <div class="flex-grow-1">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h1 class="h4 mb-1">{{ $business['name'] ?? 'Empresa' }}</h1>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
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

            <section class="col-lg-9" id="menuSections">
                @forelse($sections as $section)
                    <h2 id="cat-{{ Str::slug($section['name']) }}" class="h5 mb-3 section-title">{{ $section['name'] }}</h2>
                    <div class="row row-cols-1 row-cols-md-2 g-3 mb-4">
                        @forelse($section['items'] as $it)

                            <div class="col">
                                <div class="card menu-card h-100">
                                    <img src="{{$it['img']}}" class="card-img-top" alt="{{ $it['name'] ?? 'Item' }}">
                                    <div class="card-body">
                                        <h3 class="h6 mb-1">{{ $it['name'] }}</h3>
                                        @if(!empty($it['desc']))
                                            <p class="text-muted small mb-2">{{ $it['desc'] }}</p>
                                        @endif
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="price">{{ $fmt($it['price'] ?? 0) }}</div>
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
        <span class="text-muted small">FeiraON.app</span>
{{--        <ul class="nav small">--}}
{{--            <li class="nav-item"><a class="nav-link text-muted" href="#">Termos 11</a></li>--}}
{{--            <li class="nav-item"><a class="nav-link text-muted" href="#">Privacidade</a></li>--}}
{{--            <li class="nav-item"><a class="nav-link text-muted" href="#">Contato</a></li>--}}
{{--        </ul>--}}
    </div>
</footer>

{{-- Modal adicionar item --}}
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

{{-- FAB carrinho --}}
<button id="cartFab" class="btn position-fixed"
        data-bs-toggle="offcanvas" data-bs-target="#cartDrawer" aria-controls="cartDrawer"
        style="right:16px; bottom:16px; z-index:1050;">
    <i class="bi bi-bag"></i> <span class="ms-1" id="cartFabCount">0</span>
</button>

{{-- Offcanvas carrinho --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartDrawer">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Seu pedido</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
        <div id="cartItems" class="vstack gap-2"></div>

        <div class="mt-3">
{{--            <div class="mb-2">--}}
{{--                <label class="form-label small">Seu nome</label>--}}
{{--                <input id="cartCustomerName" class="form-control" placeholder="Ex.: João da Silva" required>--}}
{{--                <div class="invalid-feedback">Informe seu nome.</div>--}}
{{--            </div>--}}

            <div class="mb-2">
                <label class="form-label small">Forma de pagamento</label>
                <select id="cartPayment" class="form-select" required>
                    <option value="">Selecione…</option>
                    <option value="pix">Pix</option>
                    <option value="cartao">Cartão</option>
                    <option value="dinheiro">Dinheiro</option>
                </select>
                <div class="invalid-feedback">Informe a forma de pagamento.</div>
            </div>

            <div class="mb-2">
                <label class="form-label small">Endereço para entrega</label>
                <textarea id="cartAddress" class="form-control" rows="2" placeholder="Rua, número, bairro, cidade" required></textarea>
                <div class="invalid-feedback">Informe o endereço de entrega.</div>
            </div>

            <div class="mb-2">
                <label class="form-label small">WhatsApp</label>
                <input id="cartCustomerWa" class="form-control" inputmode="numeric" pattern="\d{10,13}" required>
                <div class="form-text">Usamos para contato sobre o pedido.</div>

            </div>

            <div class="mb-2">
                <label class="form-label small">Observações gerais</label>
                <textarea id="cartObs" class="form-control" rows="2" placeholder="Ex.: tirar cebola, ponto da carne..."></textarea>
            </div>
        </div>

        <div class="mt-auto border-top pt-3">
            <div class="d-flex justify-content-between mb-2">
                <strong>Total</strong>
                <strong id="cartTotal">R$ 0,00</strong>
            </div>

            <div id="cartCheckoutMulti" class="d-grid gap-2"></div>
            <a id="cartCheckout" href="#" target="_blank" class="btn btn-success w-100" rel="noopener">
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
        const $  = (s,ctx=document)=>ctx.querySelector(s);
        const $$ = (s,ctx=document)=>Array.from(ctx.querySelectorAll(s));
        const fmtBRL = v => (Number(v||0)/100).toLocaleString('pt-BR',{style:'currency',currency:'BRL'});
        const esc = s => (s||'').replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;', "'":'&#39;' }[m]));
        const BIZ_NAME = @json($business['name'] ?? 'Estabelecimento');
        const BIZ_ID   = @json($business['id']   ?? null);

        // ===== Máscara para WhatsApp =====
        const waInput = document.getElementById('cartCustomerWa');
        if (waInput) {
            waInput.addEventListener('input', function (e) {
                let v = e.target.value.replace(/\D/g, ''); // remove tudo que não for número
                if (v.length > 11) v = v.slice(0, 11); // limita a 11 dígitos

                // Formata como (99) 99999-9999
                if (v.length > 6) {
                    e.target.value = `(${v.slice(0, 2)}) ${v.slice(2, 7)}-${v.slice(7)}`;
                } else if (v.length > 2) {
                    e.target.value = `(${v.slice(0, 2)}) ${v.slice(2)}`;
                } else if (v.length > 0) {
                    e.target.value = `(${v}`;
                } else {
                    e.target.value = '';
                }
            });
        }

        function genOrderId(){
            const chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let id='ZF';
            for(let i=0;i<6;i++) id+=chars.charAt(Math.floor(Math.random()*chars.length));
            return id;
        }
        function fmtPayLabel(v){ return v==='pix'?'Pix':v==='cartao'?'Cartão':v==='dinheiro'?'Dinheiro':'A combinar'; }
        function getGlobalWa(){
            const meta = document.querySelector('meta[name="wa-number"]')?.content || '';
            return String(meta).replace(/\D+/g,'');
        }

        // ===== Filtros (busca, preço min/máx) + Ordenação =====
        const sectionsEl = document.getElementById('menuSections');
        const priceMinEl = document.getElementById('priceMin');
        const priceMaxEl = document.getElementById('priceMax');
        const sortEl     = document.getElementById('menuSort');

        function getCols() {
            return Array.from(sectionsEl.querySelectorAll('.row.row-cols-1.row-cols-md-2.g-3.mb-4 .col'));
        }
        function indexCols() {
            getCols().forEach(col => {
                const card = col.querySelector('.menu-card');
                const btn  = col.querySelector('.js-open-wa');
                const title = (card.querySelector('.h6')?.textContent || '').trim();
                const desc  = (card.querySelector('.text-muted')?.textContent || '').trim();
                const priceCents = parseInt(btn?.dataset.priceCents || '0', 10);
                col.dataset.title = title.toLowerCase();
                col.dataset.desc  = desc.toLowerCase();
                col.dataset.price = String(priceCents);
            });
        }

        function applyFiltersAndSort() {
            const q = (document.getElementById('menuSearch')?.value || '').trim().toLowerCase();
            const min = priceMinEl?.value ? parseInt(priceMinEl.value, 10) * 100 : -Infinity;
            const max = priceMaxEl?.value ? parseInt(priceMaxEl.value, 10) * 100 : +Infinity;

            getCols().forEach(col => {
                const t = col.dataset.title || '';
                const d = col.dataset.desc  || '';
                const p = parseInt(col.dataset.price || '0', 10);
                const matchText  = !q || t.includes(q) || d.includes(q);
                const matchPrice = (p >= min) && (p <= max);
                col.style.display = (matchText && matchPrice) ? '' : 'none';
            });

            const mode = sortEl?.value || '';
            if (!mode) return;

            sectionsEl.querySelectorAll('.row.row-cols-1.row-cols-md-2.g-3.mb-4').forEach(row => {
                const visibleCols = Array.from(row.children).filter(c => c.classList.contains('col') && c.style.display !== 'none');

                let cmp;
                if (mode === 'price-asc')   cmp = (a,b)=> (parseInt(a.dataset.price)-parseInt(b.dataset.price));
                if (mode === 'price-desc')  cmp = (a,b)=> (parseInt(b.dataset.price)-parseInt(a.dataset.price));
                if (mode === 'az')          cmp = (a,b)=> (a.dataset.title > b.dataset.title ? 1 : a.dataset.title < b.dataset.title ? -1 : 0);

                if (cmp) {
                    visibleCols.sort(cmp).forEach(col => row.appendChild(col));
                }
            });
        }

        function clearFiltersUI() {
            document.getElementById('menuSearch').value = '';
            priceMinEl.value = '';
            priceMaxEl.value = '';
            sortEl.value = '';
            applyFiltersAndSort();
        }

        document.getElementById('menuSearch')?.addEventListener('input', applyFiltersAndSort);
        document.getElementById('applyFilters')?.addEventListener('click', e => { e.preventDefault(); applyFiltersAndSort(); });
        document.getElementById('clearFilters')?.addEventListener('click', e => { e.preventDefault(); clearFiltersUI(); });
        sortEl?.addEventListener('change', applyFiltersAndSort);

        indexCols();
        applyFiltersAndSort();
        // ===== fim dos filtros =====

        // Carrinho
        const els = {
            list: $('#cartItems'),
            total: $('#cartTotal'),
            fabCount: $('#cartFabCount'),
            clear: $('#cartClear'),
            obs: $('#cartObs'),
            checkout: $('#cartCheckout'),
            drawer: $('#cartDrawer'),
        };
        const STORAGE_KEY = 'simpleCartMulti';
        const cart = {
            items: [],
            load(){ try{ this.items = JSON.parse(localStorage.getItem(STORAGE_KEY)||'[]'); }catch{ this.items=[]; } },
            save(){ localStorage.setItem(STORAGE_KEY, JSON.stringify(this.items)); },
            add(it){ const k=this.items.findIndex(x=>x.id===it.id&&(x.obs||'')===(it.obs||'')&&(x.wa||'')===(it.wa||'')); if(k>=0)this.items[k].qty+=it.qty; else this.items.push(it); this.save(); render(); openDrawer(); },
            remove(i){ this.items.splice(i,1); this.save(); render(); },
            clear(){ this.items=[]; this.save(); render(); },
            totalAll(){ return this.items.reduce((s,i)=>s+i.priceCents*i.qty,0); },
            count(){ return this.items.reduce((s,i)=>s+i.qty,0); },
            groups(){
                const map=new Map();
                for(const it of this.items){
                    const key=(it.wa&&it.wa.length)?`wa:${it.wa}`:`store:${it.store||'Sem loja'}`;
                    if(!map.has(key))map.set(key,{wa:it.wa||'',store:it.store||'Sem loja',items:[],subtotal:0});
                    const g=map.get(key);
                    g.items.push(it);
                    g.subtotal+=it.priceCents*it.qty;
                }
                return Array.from(map.values());
            }
        };
        function openDrawer(){ const oc=bootstrap.Offcanvas.getOrCreateInstance(els.drawer); oc.show(); }
        function render(){
            const groups = cart.groups();
            els.list.innerHTML = groups.length
                ? groups.map(g=>`
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
            <button class="btn btn-sm btn-outline-secondary mt-1" data-remove-index="${cart.items.indexOf(it)}"><i class="bi bi-trash"></i></button>
          </div>
        </div>`).join('')}
      <div class="d-flex justify-content-between small text-muted">
        <span>Subtotal</span><span>${fmtBRL(g.subtotal)}</span>
      </div>
    </div>`).join('')
                : '<div class="text-center text-muted py-3">Seu carrinho está vazio.</div>';
            els.total.textContent = fmtBRL(cart.totalAll());
            els.fabCount.textContent = String(cart.count());

            els.list.querySelectorAll('[data-remove-index]').forEach(b=>{
                b.addEventListener('click', e=>{
                    const i = parseInt(e.currentTarget.getAttribute('data-remove-index'),10);
                    cart.remove(i);
                });
            });
        }

        // Modal adicionar
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
                const wa    = (btn.dataset.wa || '').replace(/\D+/g,'');
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
            const m = bootstrap.Modal.getInstance(waModal); m?.hide();
        });

        // Checkout
        const elsCheckout = {
            clear: $('#cartClear'),
            checkout: $('#cartCheckout'),
            drawer: $('#cartDrawer'),
        };
        elsCheckout.checkout?.addEventListener('click', e=>{
            e.preventDefault();
            if(!cart.items.length) return;

            const paymentEl   = document.getElementById('cartPayment');
            const addressEl   = document.getElementById('cartAddress');
            const waEl        = document.getElementById('cartCustomerWa');
            const nameEl      = document.getElementById('cartCustomerName');

            const payment     = (paymentEl?.value||'').trim();
            const address     = (addressEl?.value||'').trim();
            const rawWa       = (waEl?.value||'').trim().replace(/\D+/g,'');
            const customerName= (nameEl?.value||'').trim();

            function setInvalid(el, msg){
                if (!el) return;
                el.classList.add('is-invalid');
                const fb = el.nextElementSibling && el.nextElementSibling.classList?.contains('invalid-feedback')
                    ? el.nextElementSibling : null;
                if (fb && msg) fb.textContent = msg;
            }

            function clearInvalid(el){ if (!el) return; el.classList.remove('is-invalid'); }

            let firstError = null;
            if (paymentEl && !payment){ setInvalid(paymentEl,'Informe a forma de pagamento.'); firstError ??= paymentEl; }
            if (addressEl && !address){ setInvalid(addressEl,'Informe o endereço de entrega.'); firstError ??= addressEl; }
            if (waEl && (!rawWa || rawWa.length<10 || rawWa.length>13)){ setInvalid(waEl,'Informe um WhatsApp válido (somente números, 10 a 13 dígitos).'); firstError ??= waEl; }


            if (firstError){ firstError.focus(); return; }

            const orderId = genOrderId();
            const groups = cart.groups();
            const g = groups[0];
            const waLoja = (g?.wa || getGlobalWa() || '{{ $waNumber }}').replace(/\D+/g,'');


            const text = (function buildZapFoodMessage({orderId,storeName,items,payment,address,customerName,customerWa,notes,subtotalCents}){
                const sub = typeof subtotalCents==='number'
                    ? subtotalCents
                    : (items||[]).reduce((s,i)=>s + Number(i.priceCents||0)*Number(i.qty||0), 0);
                const lines = (items||[]).map(i=>{
                    const totItem = Number(i.priceCents||0)*Number(i.qty||0);
                    const qtyTxt  = String(i.qty||0).padStart(2,'0')+' un';
                    return ` *${i.name}* | Qtde: ${qtyTxt} | Valor: ${fmtBRL(totItem)}`;
                }).join('\n');

                return [
                    `*${storeName}*  Novo pedido via * FeiraON.app*!`,
                    ``,
                    `*Pedido:* #${orderId}`,
                    ``,
                    lines,
                    ``,
                    `*Total:* ${fmtBRL(sub)}`,
                    ``,
                    `*Pagamento:* ${fmtPayLabel(payment)}`,
                    ``,
                    `*Endereço:* ${address || '-'}`,
                    ``,
                    `*WhatsApp:* ${customerWa || '-'}`,
                    ``,
                    ` *Observações:* ${notes || '-'}`,
                    ``,
                    `(Enviado via  FeiraON.app)`
                ].join('\n');


            })({
                orderId,
                storeName: g?.store || (cart.items[0]?.store || 'Loja'),
                items: g?.items || cart.items,
                payment,
                address,
                customerName,
                customerWa: rawWa,
                notes: ($('#cartObs')?.value||'').trim(),
                subtotalCents: cart.totalAll()
            });

            const href = (function makeWaHref(waNumber,text){
                const n = String(waNumber||'').replace(/\D+/g,'');
                return n ? `https://wa.me/${n}?text=${encodeURIComponent(text)}` : '#';
            })(waLoja, text);
            console.log(text)
            window.open(href,'_blank');
        });

        document.addEventListener('input',(e)=>{
            const t=e.target;
            if (['cartPayment','cartAddress','cartCustomerWa','cartCustomerName'].includes(t.id)){
                t.classList.remove('is-invalid');
            }
        });

        elsCheckout.clear?.addEventListener('click', e=>{ e.preventDefault(); cart.clear(); });

        // init
        cart.load();
        render();
    })();
</script>

</body>
</html>
