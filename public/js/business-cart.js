(function () {
    const currency = (cents) => {
        const v = (Number(cents) || 0) / 100;
        return v.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    };

    // --- Estado em memória + persistência ---
    const CART_KEY = 'cart_v1';
    let cart = loadCart();

    function loadCart() {
        try { return JSON.parse(localStorage.getItem(CART_KEY)) || []; }
        catch { return []; }
    }
    function saveCart() {
        localStorage.setItem(CART_KEY, JSON.stringify(cart));
        renderCart();
    }

    // --- UI refs ---
    const fab = document.getElementById('cartFab');
    const fabCount = document.getElementById('cartFabCount');
    const drawerEl = document.getElementById('cartDrawer');
    const offcanvas = drawerEl ? new bootstrap.Offcanvas(drawerEl) : null;
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    const cartObs = document.getElementById('cartObs');
    const cartClear = document.getElementById('cartClear');
    const cartCheckout = document.getElementById('cartCheckout');

    // --- Modal add item ---
    const waModal = document.getElementById('waModal');
    const waTitle = document.getElementById('waTitle');
    const waQty = document.getElementById('waQty');
    const waObs = document.getElementById('waObs');
    const waAddCart = document.getElementById('waAddCart');
    let currentItem = null;

    // Abrir modal com dados do item
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.js-open-wa');
        if (!btn) return;

        const id = Number(btn.dataset.itemId);
        const name = btn.dataset.itemName;
        const priceCents = Number(btn.dataset.priceCents) || 0;

        currentItem = { id, name, priceCents };
        waTitle.textContent = name;
        waQty.value = 1;
        waObs.value = '';
    });

    // Adicionar ao carrinho
    if (waAddCart) {
        waAddCart.addEventListener('click', () => {
            if (!currentItem) return;
            const qty = Math.max(1, parseInt(waQty.value || '1', 10));
            const obs = (waObs.value || '').trim();

            // Se já existe, soma qtde
            const idx = cart.findIndex(i => i.id === currentItem.id && i.obs === obs);
            if (idx >= 0) {
                cart[idx].qty += qty;
            } else {
                cart.push({
                    id: currentItem.id,
                    name: currentItem.name,
                    priceCents: currentItem.priceCents,
                    qty,
                    obs
                });
            }

            saveCart();
            bootstrap.Modal.getInstance(waModal)?.hide();
            // abre drawer
            offcanvas?.show();
        });
    }

    // FAB abre o carrinho
    if (fab) fab.addEventListener('click', () => offcanvas?.show());

    // Limpar carrinho
    if (cartClear) {
        cartClear.addEventListener('click', () => {
            if (!cart.length) return;
            if (confirm('Limpar o carrinho?')) {
                cart = [];
                saveCart();
            }
        });
    }

    // Montar link WhatsApp
    function buildWaMessage() {
        // pegue o número do WhatsApp da página se tiver data-attr (opcional)
        const bizNumber = document.body.dataset.bizWhatsapp || ''; // ex: “5511999999999”
        const lines = [];
        lines.push('*Meu pedido*');

        cart.forEach((it, idx) => {
            lines.push(`${idx + 1}) ${it.qty}x ${it.name} — ${currency(it.priceCents * it.qty)}`);
            if (it.obs) lines.push(`   _Obs:_ ${it.obs}`);
        });

        const totalCents = cart.reduce((s, i) => s + i.priceCents * i.qty, 0);
        lines.push('');
        lines.push(`*Total:* ${currency(totalCents)}`);

        const globalObs = (cartObs?.value || '').trim();
        if (globalObs) {
            lines.push('');
            lines.push(`*Observações gerais:* ${globalObs}`);
        }

        const text = encodeURIComponent(lines.join('\n'));
        const base = bizNumber ? `https://wa.me/${bizNumber}` : 'https://wa.me/';
        return `${base}?text=${text}`;
    }

    if (cartCheckout) {
        cartCheckout.addEventListener('click', (e) => {
            if (!cart.length) {
                e.preventDefault();
                alert('Seu carrinho está vazio.');
                return;
            }
            cartCheckout.href = buildWaMessage();
        });
    }

    // Render do carrinho
    function renderCart() {
        // contador FAB
        const count = cart.reduce((s, i) => s + i.qty, 0);
        if (fabCount) fabCount.textContent = count;

        // lista
        if (cartItems) {
            cartItems.innerHTML = '';
            cart.forEach((it, idx) => {
                const row = document.createElement('div');
                row.className = 'border rounded p-2';

                row.innerHTML = `
          <div class="d-flex justify-content-between align-items-start">
            <div class="me-2">
              <div class="fw-semibold">${it.qty}× ${it.name}</div>
              ${it.obs ? `<div class="text-muted small">Obs: ${it.obs}</div>` : ''}
              <div class="text-muted small">${currency(it.priceCents)} / un.</div>
            </div>
            <div class="text-end">
              <div class="fw-semibold">${currency(it.priceCents * it.qty)}</div>
              <div class="btn-group btn-group-sm mt-1">
                <button class="btn btn-outline-secondary js-qty-dec" data-idx="${idx}">-</button>
                <button class="btn btn-outline-secondary js-qty-inc" data-idx="${idx}">+</button>
                <button class="btn btn-outline-danger js-remove" data-idx="${idx}">
                  <i class="bi bi-x"></i>
                </button>
              </div>
            </div>
          </div>
        `;
                cartItems.appendChild(row);
            });

            // ações
            cartItems.querySelectorAll('.js-qty-dec').forEach(b => {
                b.addEventListener('click', () => {
                    const i = Number(b.dataset.idx);
                    cart[i].qty = Math.max(1, cart[i].qty - 1);
                    saveCart();
                });
            });
            cartItems.querySelectorAll('.js-qty-inc').forEach(b => {
                b.addEventListener('click', () => {
                    const i = Number(b.dataset.idx);
                    cart[i].qty += 1;
                    saveCart();
                });
            });
            cartItems.querySelectorAll('.js-remove').forEach(b => {
                b.addEventListener('click', () => {
                    const i = Number(b.dataset.idx);
                    cart.splice(i, 1);
                    saveCart();
                });
            });
        }

        // total
        const totalCents = cart.reduce((s, i) => s + i.priceCents * i.qty, 0);
        if (cartTotal) cartTotal.textContent = currency(totalCents);
    }

    // Inicializa
    renderCart();
})();
