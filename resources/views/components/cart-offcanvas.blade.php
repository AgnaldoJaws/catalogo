{{-- resources/views/components/cart-offcanvas.blade.php --}}
<button id="cartFab" class="btn position-fixed"
        data-bs-toggle="offcanvas"
        data-bs-target="#cartDrawer"
        aria-controls="cartDrawer"
        style="right:16px; bottom:16px; z-index:1050;">
    <i class="bi bi-bag"></i>
    <span class="ms-1" id="cartFabCount">0</span>
</button>

<div class="offcanvas offcanvas-end" tabindex="-1" id="cartDrawer">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Seu pedido</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body d-flex flex-column">
        <div id="cartItems" class="vstack gap-2"></div>

        <div class="mt-3">
            <div class="mb-2">
                <label class="form-label small">Seu nome</label>
                <input id="cartCustomerName" class="form-control" placeholder="Ex.: João da Silva" required>
                <div class="invalid-feedback">Informe seu nome.</div>
            </div>

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
                <label class="form-label small">Seu WhatsApp (contato)</label>
                <input id="cartCustomerWa" class="form-control" placeholder="(DDD) 9xxxx-xxxx" inputmode="numeric" pattern="\d{10,13}" required>
                <div class="form-text">Usamos para contato sobre o pedido.</div>
                <div class="invalid-feedback">Informe um WhatsApp válido (somente números, 10 a 13 dígitos).</div>
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

            <a id="cartCheckout" href="#" target="_blank" class="btn btn-success w-100" rel="noopener">
                <i class="bi bi-whatsapp"></i> Finalizar no WhatsApp
            </a>

            <button id="cartClear" class="btn btn-outline-secondary w-100 mt-2">
                <i class="bi bi-trash"></i> Limpar carrinho
            </button>
        </div>
    </div>
</div>

<script>
    (function(){
        const $  = (s,ctx=document)=>ctx.querySelector(s);
        const fmtBRL = v => (Number(v||0)/100).toLocaleString('pt-BR',{style:'currency',currency:'BRL'});
        const esc = s => (s||'').replace(/[&<>"']/g, m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
        const STORAGE_KEY = 'simpleCartMulti';

        const els = {
            list: $('#cartItems'),
            total: $('#cartTotal'),
            fabCount: $('#cartFabCount'),
            footerCount: $('#cartFooterCount'),
            clear: $('#cartClear'),
            checkout: $('#cartCheckout'),
            drawer: $('#cartDrawer'),
        };

        // ============ CARRINHO GLOBAL ============
        window.cart = {
            items: [],
            load(){ try{ this.items = JSON.parse(localStorage.getItem(STORAGE_KEY)||'[]'); }catch{ this.items=[]; } },
            save(){ localStorage.setItem(STORAGE_KEY, JSON.stringify(this.items)); },
            add(it){
                const k=this.items.findIndex(x=>x.id===it.id&&(x.obs||'')===(it.obs||'')&&(x.wa||'')===(it.wa||''));
                if(k>=0)this.items[k].qty+=it.qty;
                else this.items.push(it);
                this.save(); render(); openDrawer();
            },
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

        function openDrawer(){
            const el = els.drawer;
            if (!el) return;
            bootstrap.Offcanvas.getOrCreateInstance(el).show();
        }

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
            const count = cart.count();
            if (els.fabCount) els.fabCount.textContent = count;
            if (els.footerCount) els.footerCount.textContent = count;

            els.list.querySelectorAll('[data-remove-index]').forEach(b=>{
                b.addEventListener('click', e=>{
                    const i = parseInt(e.currentTarget.getAttribute('data-remove-index'),10);
                    cart.remove(i);
                });
            });
        }

        // ============ WHATSAPP CHECKOUT ============
        function fmtPayLabel(v){ return v==='pix'?'Pix':v==='cartao'?'Cartão':v==='dinheiro'?'Dinheiro':'A combinar'; }
        function makeWaHref(waNumber,text){
            const n = String(waNumber||'').replace(/\D+/g,'');
            return n ? `https://wa.me/${n}?text=${encodeURIComponent(text)}` : '#';
        }

        function buildZapFoodMessage({orderId,storeName,items,payment,address,customerName,customerWa,notes,subtotalCents}){
            const sub = typeof subtotalCents==='number'
                ? subtotalCents
                : (items||[]).reduce((s,i)=>s + Number(i.priceCents||0)*Number(i.qty||0), 0);

            const lines = (items||[]).map(i=>{
                const totItem = Number(i.priceCents||0)*Number(i.qty||0);
                const qtyTxt  = String(i.qty||0).padStart(2,'0')+' un';
                return ` *${i.name}* | Qtde: ${qtyTxt} | Valor: ${fmtBRL(totItem)}`;
            }).join('\n');

            return [
                ` *${storeName}* recebemos um novo pedido via *ZapFood*!`,
                ``,
                `pedido=${orderId}`,
                ``,
                lines,
                ``,
                ` *Total:* ${fmtBRL(sub)}`,
                ``,
                ` *Pagamento:* ${fmtPayLabel(payment)}`,
                ``,
                ` *Endereço:* ${address && address.trim() ? address : '-'}`,
                ``,
                ` *Cliente:* ${customerName && customerName.trim() ? customerName : '-'}`,
                ` *WhatsApp:* ${customerWa && customerWa.trim() ? customerWa : '-'}`,
                ``,
                ` *Observações:* ${notes && notes.trim() ? notes : '-'}`,
                ``,
                ` (Enviado via ZapFood)`
            ].join('\n');
        }

        els.checkout?.addEventListener('click', e=>{
            e.preventDefault();
            if(!cart.items.length) return;

            const payment = $('#cartPayment')?.value || '';
            const address = $('#cartAddress')?.value || '';
            const wa      = $('#cartCustomerWa')?.value?.replace(/\D+/g,'') || '';
            const name    = $('#cartCustomerName')?.value || '';
            const notes   = $('#cartObs')?.value || '';
            if(!wa || wa.length<10) return alert('Informe um WhatsApp válido.');

            const g = cart.groups()[0];
            const lojaWa = (g?.wa || '').replace(/\D+/g,'');
            if(!lojaWa) return alert('Número de WhatsApp da loja não configurado.');

            const orderId = 'ZF' + Math.random().toString(36).substring(2,8).toUpperCase();
            const text = buildZapFoodMessage({
                orderId,
                storeName: g?.store || 'Estabelecimento',
                items: g?.items || [],
                payment, address, customerName: name, customerWa: wa, notes, subtotalCents: cart.totalAll()
            });

            window.open(makeWaHref(lojaWa, text),'_blank');
        });

        els.clear?.addEventListener('click', e=>{ e.preventDefault(); cart.clear(); });

        // ====== INICIALIZA ======
        cart.load();
        render();
    })();
</script>
