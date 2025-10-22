@props([
  'sections'   => [],             // [{ name:'', items:[{id,name,price,img,desc}] }]
  'waNumber'   => null,           // string só dígitos
  'fmt'        => fn($c)=>'R$ '.number_format(($c??0)/100,2,',','.'), // helper moeda
  'storageKey' => 'bizCart',      // opcional: chave do localStorage
])

<div class="cart-catalog" data-wa="{{ $waNumber }}" data-storage="{{ $storageKey }}">
    {{-- ===================== LISTA DE SEÇÕES / ITENS ===================== --}}
    @forelse($sections as $section)
        <h2 class="h5 mb-3">{{ $section['name'] }}</h2>
        <div class="row row-cols-1 row-cols-md-2 g-3 mb-4">
            @forelse(($section['items'] ?? []) as $it)
                @php
                    $id   = $it['id']   ?? Str::uuid();
                    $name = $it['name'] ?? 'Item';
                    $img  = $it['img']  ?? 'https://via.placeholder.com/600x400?text=Sem+imagem';
                    $desc = $it['desc'] ?? null;
                    $pc   = (int)($it['price'] ?? 0);
                @endphp
                <div class="col">
                    <div class="card menu-card h-100" data-price-cents="{{ $pc }}">
                        <img src="{{ $img }}" class="card-img-top" alt="{{ $name }}">
                        <div class="card-body">
                            <h3 class="h6 mb-1">{{ $name }}</h3>
                            @if($desc)<p class="text-muted small mb-2">{{ $desc }}</p>@endif
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="price fw-bold">{{ $fmt($pc) }}</div>
                                <button
                                    class="btn btn-sm btn-outline-primary js-open-add"
                                    data-item-id="{{ $id }}"
                                    data-item-name="{{ $name }}"
                                    data-price-cents="{{ $pc }}"
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
        <div class="alert alert-light border">Nenhuma seção encontrada.</div>
    @endforelse

    {{-- ===================== MODAL ADICIONAR ===================== --}}
    <div class="modal fade" id="modalAddItem" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="addTitle" class="modal-title">Adicionar item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label small">Quantidade</label>
                        <input id="addQty" type="number" min="1" value="1" class="form-control">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small">Observações</label>
                        <textarea id="addObs" class="form-control" rows="2" placeholder="Ex.: sem maionese"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnAddToCart" class="btn btn-primary">
                        <i class="bi bi-cart-plus"></i> Adicionar ao carrinho
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== FAB + OFFCANVAS CARRINHO ===================== --}}
    <button id="cartFab" class="btn btn-danger position-fixed"
            data-bs-toggle="offcanvas" data-bs-target="#cartDrawer"
            style="right:16px; bottom:16px; z-index:1050; border-radius:999px; box-shadow:0 6px 24px rgba(0,0,0,.2)">
        <i class="bi bi-bag"></i> <span class="ms-1" id="cartFabCount">0</span>
    </button>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartDrawer">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Seu pedido</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column">
            <div id="cartList" class="vstack gap-2"></div>

            <div class="mt-auto border-top pt-3">
                <div class="d-flex justify-content-between mb-2">
                    <strong>Total</strong>
                    <strong id="cartTotal">R$ 0,00</strong>
                </div>
                <div class="mb-2">
                    <label class="form-label small">Observações gerais</label>
                    <textarea id="cartObs" class="form-control" rows="2" placeholder="Ex.: tirar cebola..."></textarea>
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
</div>

{{-- ===================== CSS LOCAL ===================== --}}
<style>
    .menu-card{border:1px solid #ececec; box-shadow:0 2px 10px rgba(0,0,0,.04)}
    .menu-card img{aspect-ratio:4/3; object-fit:cover}
    .toast-add{position:fixed; left:50%; bottom:88px; transform:translateX(-50%);
        background:#111; color:#fff; padding:.6rem .9rem; border-radius:.6rem; z-index:1100; display:none}
    .toast-add.show{display:block; animation:fade .15s ease}
    @keyframes fade{from{opacity:0; transform:translate(-50%,10px)} to{opacity:1; transform:translate(-50%,0)}}
</style>

{{-- ===================== JS LOCAL (CARRINHO COMPLETO) ===================== --}}
<script>
    (function(){
        const root = document.currentScript.closest('.cart-catalog'); // escopo deste componente
        const waNumber = root.dataset.wa || '';
        const storageKey = root.dataset.storage || 'bizCart';

        const $  = (s,ctx=document)=>ctx.querySelector(s);
        const $$ = (s,ctx=document)=>Array.from(ctx.querySelectorAll(s));
        const fmt = cents => (Number(cents||0)/100).toLocaleString('pt-BR',{style:'currency',currency:'BRL'});

        // refs
        const modalEl  = $('#modalAddItem', root);
        const addTitle = $('#addTitle', root);
        const addQty   = $('#addQty', root);
        const addObs   = $('#addObs', root);
        const btnAdd   = $('#btnAddToCart', root);

        const cartList   = $('#cartList', root);
        const cartTotal  = $('#cartTotal', root);
        const cartObs    = $('#cartObs', root);
        const cartClear  = $('#cartClear', root);
        const cartFabCnt = $('#cartFabCount', root);
        const cartDrawer = $('#cartDrawer', root);
        const cartCheckout = $('#cartCheckout', root);

        const bsModal   = modalEl ? new bootstrap.Modal(modalEl) : null;

        // estado atual do item no modal
        let currentItem = null; // {id,name,priceCents}

        // carrinho
        const cart = {
            items: [], // {id,name,priceCents,qty,obs}
            load(){
                try{ this.items = JSON.parse(localStorage.getItem(storageKey) || '[]'); }
                catch{ this.items = []; }
            },
            save(){ localStorage.setItem(storageKey, JSON.stringify(this.items)); },
            add(it){
                const idx = this.items.findIndex(x => x.id===it.id && (x.obs||'')===(it.obs||''));
                if (idx >= 0) this.items[idx].qty += it.qty;
                else this.items.push(it);
                this.save(); render();
                toast(`Adicionado: ${it.name} × ${it.qty}`);
            },
            remove(i){ this.items.splice(i,1); this.save(); render(); },
            clear(){ this.items = []; this.save(); render(); },
            totalCents(){ return this.items.reduce((s,i)=>s + i.priceCents*i.qty, 0); },
            count(){ return this.items.reduce((s,i)=>s + i.qty, 0); }
        };

        // abrir modal ao clicar “Adicionar”
        $$('.js-open-add', root).forEach(btn=>{
            btn.addEventListener('click', ()=>{
                currentItem = {
                    id: btn.getAttribute('data-item-id'),
                    name: btn.getAttribute('data-item-name'),
                    priceCents: parseInt(btn.getAttribute('data-price-cents')||'0',10)
                };
                addTitle.textContent = `Adicionar: ${currentItem.name} — ${fmt(currentItem.priceCents)}`;
                addQty.value = 1;
                addObs.value = '';
                bsModal?.show();
            });
        });

        // confirmar inclusão
        btnAdd?.addEventListener('click', ()=>{
            if (!currentItem) return;
            const qty = Math.max(1, parseInt(addQty.value||'1',10));
            const obs = (addObs.value||'').trim();
            cart.add({ ...currentItem, qty, obs });
            bsModal?.hide();
            // feedback abrindo o drawer
            const oc = bootstrap.Offcanvas.getOrCreateInstance(cartDrawer);
            oc.show();
        });

        // renderização do carrinho
        function render(){
            cartList.innerHTML = cart.items.map((it,idx)=>{
                const sub = it.priceCents * it.qty;
                return `
        <div class="d-flex align-items-start justify-content-between border rounded p-2">
          <div class="me-2">
            <div class="fw-semibold">${it.name} <span class="text-muted">× ${it.qty}</span></div>
            <div class="small text-muted">${fmt(it.priceCents)} cada${it.obs ? ` • ${escapeHtml(it.obs)}`:''}</div>
          </div>
          <div class="text-end">
            <div class="fw-bold">${fmt(sub)}</div>
            <button class="btn btn-sm btn-outline-secondary mt-1" data-remove="${idx}">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>`;
            }).join('');
            // remove
            $$('[data-remove]', cartList).forEach(b=>{
                b.addEventListener('click', e=> cart.remove(parseInt(e.currentTarget.getAttribute('data-remove'),10)));
            });

            cartTotal.textContent = fmt(cart.totalCents());
            cartFabCnt.textContent = String(cart.count());
            updateCheckout();
        }

        function updateCheckout(){
            const obs = (cartObs.value||'-').trim();
            const lines = cart.items.map(i => `• ${i.name} x ${i.qty} — ${fmt(i.priceCents)}`);
            const total = fmt(cart.totalCents());
            const txt = `Olá! Quero fazer um pedido:%0A%0A${lines.join('%0A')}%0A%0ATotal: ${encodeURIComponent(total)}%0AObs.: ${encodeURIComponent(obs)}%0A%0A(Enviado via catálogo)`;
            const href = waNumber ? `https://wa.me/${waNumber}?text=${txt}` : '#';
            cartCheckout.setAttribute('href', href);
        }

        function toast(msg){
            let t = root.querySelector('.toast-add');
            if (!t){ t = document.createElement('div'); t.className = 'toast-add'; root.appendChild(t); }
            t.textContent = msg; t.classList.add('show');
            setTimeout(()=> t.classList.remove('show'), 1600);
        }

        function escapeHtml(s){ return s.replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m])); }

        cartObs?.addEventListener('input', updateCheckout);
        cartClear?.addEventListener('click', e=>{ e.preventDefault(); cart.clear(); });

        // init
        cart.load();
        render();
    })();
</script>
