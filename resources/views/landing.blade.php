<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Marketplace Local — Pré-cadastro</title>
    <!-- Bootstrap 5 (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root{
            /* Paleta inspirada em interfaces de apps de delivery (vermelho forte, branco, preto) */
            --brand-red:#ea1d2c; /* NÃO usar logo/marca de terceiros; apenas a cor base */
            --brand-red-600:#c41723;
            --ink:#111111;
            --muted:#6b7280;
            --bg:#ffffff;
            --surface:#f8f9fa;
            --radius:20px;
        }
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,"Apple Color Emoji","Segoe UI Emoji";color:var(--ink);}
        .btn-brand{background:var(--brand-red);border-color:var(--brand-red);color:#fff;border-radius:999px;padding:.9rem 1.4rem;font-weight:700}
        .btn-brand:hover{background:var(--brand-red-600);border-color:var(--brand-red-600);color:#fff}
        .hero{background:var(--brand-red);color:#fff;position:relative;overflow:hidden}
        .hero .badge-pill{background:#fff;color:var(--brand-red);border-radius:999px;font-weight:700}
        .pill{border-radius:999px}
        .card-feature{border:none;border-radius:var(--radius);background:#fff;box-shadow:0 6px 24px rgba(17,17,17,.06)}
        .section-title{font-weight:800;letter-spacing:-.2px}
        .check{display:inline-flex;align-items:center;gap:.5rem}
        .check i{font-size:1.1rem}
        .price-card{border:none;border-radius:var(--radius);background:#fff;box-shadow:0 8px 30px rgba(17,17,17,.08)}
        .whats-badge{display:inline-flex;align-items:center;gap:.5rem;background:#25D366;color:#fff;padding:.45rem .75rem;border-radius:999px;font-weight:700}
        .no-fees-badge{display:inline-flex;align-items:center;gap:.5rem;background:#16a34a;color:#fff;padding:.45rem .75rem;border-radius:999px;font-weight:700}
        .faq-item{border-bottom:1px solid #eee}
        .footer{background:var(--surface);}
        .shadow-soft{box-shadow:0 12px 40px rgba(17,17,17,.08)}
        .rounded-2xl{border-radius:var(--radius)}
    </style>
</head>
<body>
<!-- NAV -->
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ url('/') }}" aria-label="Início">
            <img src="{{ asset('img/img.png') }}"
                 class="img-fluid"
                 alt="FOOOD"
                 style="max-width: 25%;">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Alternar navegação">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="#beneficios">Benefícios</a></li>
                <li class="nav-item"><a class="nav-link" href="#como-funciona">Como funciona</a></li>
                <li class="nav-item"><a class="nav-link" href="#plano">Plano</a></li>
                <li class="nav-item ms-lg-3"><a class="btn btn-brand" href="#lead">Quero participar</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero py-5 py-lg-6">
    <div class="container py-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span class="badge badge-pill px-3 py-2 mb-3">Pré-lançamento</span>
                <h1 class="display-5 fw-bold lh-tight mb-3">Coloque seu comércio em evidência no Vale do Ribeira</h1>
                <p class="lead mb-4">
                    Nosso marketplace é pensado para dar visibilidade aos comércios locais e fortalecer a economia da região.
                </p>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <span class="no-fees-badge"><i class="bi bi-cash-coin"></i> Sem taxas</span>
                    <span class="whats-badge"><i class="bi bi-whatsapp"></i> Pedidos no WhatsApp</span>
                </div>
{{--                <div class="d-flex gap-2">--}}
{{--                    <a href="#lead" class="btn btn-brand btn-lg">Quero participar do lançamento</a>--}}
{{--                    <a href="#beneficios" class="btn btn-light btn-lg pill">Ver benefícios</a>--}}
{{--                </div>--}}
{{--                <p class="small mt-3 opacity-75">Pré-cadastro gratuito. Nenhuma cobrança agora.</p>--}}
            </div>
            <div class="col-lg-6">
{{--                <div class="bg-white rounded-2xl shadow-soft p-3 text-center">--}}
{{--                    <img src="{{ asset('img/img.png') }}"--}}
{{--                         class="img-fluid"--}}
{{--                         alt="V-Ribeira Food"--}}
{{--                         style="max-width: 320px; height: auto;">--}}
{{--                </div>--}}
            </div>

        </div>
    </div>
</section>

<!-- BENEFÍCIOS -->
<section id="beneficios" class="py-5">
    <div class="container">
        <h2 class="section-title h1 mb-4">Benefícios para o seu comércio</h2>
        <p class="text-muted mb-5">Visibilidade, simplicidade e controle — tudo em um único lugar.</p>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card-feature p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="fs-3 text-danger"><i class="bi bi-shop"></i></div>
                        <h3 class="h5 m-0">Página exclusiva</h3>
                    </div>
                    <p class="mb-0">Vitrine digital do seu negócio com fotos, descrição, contato e localização.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card-feature p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="fs-3 text-danger"><i class="bi bi-bag-check"></i></div>
                        <h3 class="h5 m-0">Catálogo compartilhável</h3>
                    </div>
                    <p class="mb-0">Cadastre produtos/serviços, organize categorias e compartilhe com link único.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card-feature p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="fs-3 text-danger"><i class="bi bi-whatsapp"></i></div>
                        <h3 class="h5 m-0">Pedidos no WhatsApp</h3>
                    </div>
                    <p class="mb-0">O cliente fala com você direto. Sem intermediários, sem burocracia.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card-feature p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="fs-3 text-danger"><i class="bi bi-megaphone"></i></div>
                        <h3 class="h5 m-0">Marketplace regional</h3>
                    </div>
                    <p class="mb-0">Aumente sua visibilidade e seja encontrado por quem procura na sua região.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card-feature p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="fs-3 text-danger"><i class="bi bi-cash-coin"></i></div>
                        <h3 class="h5 m-0">Sem taxas por venda</h3>
                    </div>
                    <p class="mb-0">Cobre do seu jeito. Aqui você não perde margem para comissões.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card-feature p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="fs-3 text-danger"><i class="bi bi-speedometer2"></i></div>
                        <h3 class="h5 m-0">Gestão simples</h3>
                    </div>
                    <p class="mb-0">Painel para editar catálogo, contatos e informações com rapidez.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- COMO FUNCIONA -->
<section id="como-funciona" class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title h1 mb-4">Como funciona</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card-feature p-4 h-100">
                    <div class="fs-3 text-danger mb-2"><i class="bi bi-1-circle"></i></div>
                    <h3 class="h5">Faça o pré-cadastro</h3>
                    <p>Sem cobrança agora. Você garante seu acesso no lançamento.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-feature p-4 h-100">
                    <div class="fs-3 text-danger mb-2"><i class="bi bi-2-circle"></i></div>
                    <h3 class="h5">Crie sua página e catálogo</h3>
                    <p>Adicione fotos, descrição e produtos/serviços em poucos minutos.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-feature p-4 h-100">
                    <div class="fs-3 text-danger mb-2"><i class="bi bi-3-circle"></i></div>
                    <h3 class="h5">Compartilhe e receba pedidos</h3>
                    <p>Divulgue o link e atenda clientes direto no WhatsApp.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MOCK/SHOWCASE -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6 order-lg-2">
                <div class="bg-white rounded-2xl shadow-soft p-3">
                    <img src="https://images.unsplash.com/photo-1548199973-03cce0bbc87b?q=80&w=1400&auto=format&fit=crop" class="img-fluid rounded-2xl" alt="Página do comerciante" />
                </div>
            </div>
            <div class="col-lg-6">
                <h2 class="section-title h1 mb-3">Sua página, seu catálogo, seu jeito</h2>
                <p class="text-muted mb-4">Vitrine moderna, link curto, compartilhamento rápido. Perfeito para grupos de WhatsApp, Instagram e Facebook.</p>
                <ul class="list-unstyled">
                    <li class="check mb-2"><i class="bi bi-check2-circle text-success"></i> Link único para compartilhar</li>
                    <li class="check mb-2"><i class="bi bi-check2-circle text-success"></i> Categorias e itens ilimitados</li>
                    <li class="check mb-2"><i class="bi bi-check2-circle text-success"></i> Contatos e horários atualizáveis</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- PLANO (LEAD) -->
<!-- PLANO (LEAD) -->
<section id="plano" class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="price-card p-4 p-lg-5 text-center">
                    <h2 class="h1 fw-bold mb-2">Plano único (anual)</h2>
                    <p class="text-muted mb-4">
                        <strong>Anuidade de R$ 360,00</strong> — pode ser paga em
                        <strong>12x de R$ 30,00 sem juros</strong>.
                        Pré-cadastro gratuito. Nenhuma cobrança agora.
                    </p>

                    <div class="row text-start g-2 justify-content-center mb-4">
                        <div class="col-md-8">
                            <div class="d-flex align-items-start gap-2 mb-2"><i class="bi bi-check2-circle text-success"></i><span>Sem taxas por venda</span></div>
                            <div class="d-flex align-items-start gap-2 mb-2"><i class="bi bi-check2-circle text-success"></i><span>Pedidos diretos no WhatsApp</span></div>
                            <div class="d-flex align-items-start gap-2 mb-2"><i class="bi bi-check2-circle text-success"></i><span>Página exclusiva do seu negócio (link curto e compartilhável)</span></div>
                            <div class="d-flex align-items-start gap-2 mb-2"><i class="bi bi-check2-circle text-success"></i><span>Catálogo ilimitado com categorias e fotos</span></div>
                            <div class="d-flex align-items-start gap-2 mb-2"><i class="bi bi-check2-circle text-success"></i><span>Participação no marketplace (mais visibilidade)</span></div>
                            <div class="d-flex align-items-start gap-2 mb-2"><i class="bi bi-check2-circle text-success"></i><span>Painel de gestão simples (informações, horários e contatos)</span></div>
                            <div class="d-flex align-items-start gap-2 mb-2"><i class="bi bi-check2-circle text-success"></i><span>Cancelamento simples, sem fidelidade</span></div>
                        </div>
                    </div>

                    <a href="#lead" class="btn btn-brand btn-lg">Quero participar do lançamento</a>
                    <p class="small mt-3">Ao clicar, você será direcionado ao formulário de pré-cadastro.</p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- FAQ -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title h1 mb-4">Perguntas frequentes</h2>
        <div class="row g-4">
            <div class="col-lg-10">
                <div class="faq-item py-3">
                    <h3 class="h6">Vou pagar algo agora?</h3>
                    <p class="text-muted mb-0">Não. O pré-cadastro é gratuito e sem compromisso. Usaremos seu contato para liberar o acesso no lançamento.</p>
                </div>
                <div class="faq-item py-3">
                    <h3 class="h6">Como chegam os pedidos?</h3>
                    <p class="text-muted mb-0">Direto no seu WhatsApp, sem intermediários e sem comissões.</p>
                </div>
                <div class="faq-item py-3">
                    <h3 class="h6">Posso compartilhar meu catálogo?</h3>
                    <p class="text-muted mb-0">Sim. Você terá um link único para divulgar em grupos e redes sociais.</p>
                </div>
                <div class="faq-item py-3">
                    <h3 class="h6">Terei custos por venda?</h3>
                    <p class="text-muted mb-0">Não cobramos taxas por venda. Você define seu preço e recebe tudo.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FORM LEAD -->
<section id="lead" class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="p-4 p-lg-5 bg-white rounded-2xl shadow-soft">
                    <h2 class="h1 fw-bold mb-3">Quero participar do lançamento</h2>
                    <p class="text-muted mb-4">Deixe seus dados abaixo. Avisaremos por WhatsApp ou e-mail quando o acesso antecipado estiver disponível.</p>
                    <!-- Substitua action/method por rotas reais do seu backend -->
                    <form action="/waitlist" method="post" class="needs-validation" novalidate>
                        <!-- @csrf (se for Blade) -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nome</label>
                                <input type="text" class="form-control" name="name" required>
                                <div class="invalid-feedback">Informe seu nome.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nome do comércio</label>
                                <input type="text" class="form-control" name="business_name" required>
                                <div class="invalid-feedback">Informe o nome do comércio.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">WhatsApp</label>
                                <input type="tel" class="form-control" name="whatsapp" placeholder="(xx) xxxxx-xxxx" required>
                                <div class="invalid-feedback">Informe um WhatsApp válido.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">E-mail</label>
                                <input type="email" class="form-control" name="email" required>
                                <div class="invalid-feedback">Informe um e-mail válido.</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Segmento (opcional)</label>
                                <select class="form-select" name="segment">
                                    <option value="">Selecione</option>
                                    <option>Alimentos/Bebidas</option>
                                    <option>Serviços</option>
                                    <option>Moda</option>
                                    <option>Saúde/Beleza</option>
                                    <option>Outros</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="lgpd" name="consent" required>
                                    <label class="form-check-label" for="lgpd">
                                        Concordo em receber comunicações sobre o lançamento (LGPD).
                                    </label>
                                    <div class="invalid-feedback">É necessário aceitar para prosseguir.</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-brand btn-lg w-100" type="submit">Confirmar pré-cadastro</button>
                            </div>
                            <div class="col-12">
                                <p class="small text-muted m-0">Protegemos seus dados. Você pode sair da lista quando quiser.</p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer py-4">
    <div class="container d-flex flex-column flex-lg-row align-items-center justify-content-between gap-3">
        <div class="text-muted small">© <span id="y"></span> V-Ribeira Food • Todos os direitos reservados</div>
        <div class="d-flex gap-3 small">
            <a href="#" class="link-secondary text-decoration-none">Política de privacidade</a>
            <a href="#" class="link-secondary text-decoration-none">Termos de uso</a>
            <a href="#lead" class="link-secondary text-decoration-none">Pré-cadastro</a>
        </div>
    </div>
</footer>

<script>
    // Ano no footer
    document.getElementById('y').textContent = new Date().getFullYear();

    // Validação Bootstrap
    (function(){
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
