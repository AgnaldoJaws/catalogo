@extends('admin.layout')
@section('title','Itens da Seção')

@section('content')
    <div class="row g-3 g-lg-4">
        {{-- CRIAÇÃO --}}
        <div class="col-12 col-lg-4">
            <form method="post"
                  action="{{ route('admin.menu.items.store',['business'=>$biz->id,'section'=>$section]) }}"
                  class="card shadow-sm"
                  enctype="multipart/form-data" aria-labelledby="novo-item-title">
                @csrf
                <div class="card-body">
                    <h2 id="novo-item-title" class="h5 fw-bold mb-3">Novo item</h2>

                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input name="name" class="form-control form-control-lg" required placeholder="Ex.: X-Salada">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Ex.: Pão brioche, blend 120g, queijo, alface e tomate."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Preço</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" class="form-control price-brl" inputmode="decimal" placeholder="0,00">
                            <input type="hidden" name="price" class="price-cents" value="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Imagem</label>
                        <input name="image_file" type="file" class="form-control image-input" accept="image/*">
                        <img class="mt-2 rounded d-none image-preview" alt="Pré-visualização" style="height:80px;object-fit:cover;border:1px solid #eee">
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Disponível</label>
                            <select name="is_available" class="form-select">
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Ordem</label>
                            <input name="sort_order" type="number" class="form-control" value="0">
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button class="btn btn-primary btn-lg px-4">Criar item</button>
                </div>
            </form>
        </div>

        {{-- LISTAGEM / EDIÇÃO --}}
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5 fw-bold mb-3">Itens da seção</h2>

                    @forelse($items as $i => $it)
                        <div class="card item-card mb-4 rounded-4 shadow-sm-soft position-relative">
                            {{-- Medalha de posição --}}
                            <div class="position-absolute top-0 end-0 translate-middle badge-rank">
                                <span>{{ $i + 1 }}º</span>
                            </div>

                            <div class="card-body">
                                <form class="row gy-3 gx-3 align-items-start item-form" method="post" enctype="multipart/form-data"
                                      action="{{ route('admin.menu.items.update',['business'=>$biz->id,'section'=>$section,'item'=>$it['id']]) }}">
                                    @csrf

                                    <div class="col-12 col-md-4">
                                        <label class="form-label d-block">Item</label>
                                        <div class="d-flex align-items-center gap-2">
                                            @if(!empty($it['image_src'] ?? null))
                                                <img src="{{ $it['image_src'] }}" alt="" width="56" height="56"
                                                     class="rounded border flex-shrink-0" style="object-fit:cover">
                                            @endif
                                            <input name="name" class="form-control" value="{{ $it['name'] }}">
                                        </div>
                                    </div>

                                    <div class="col-6 col-md-2">
                                        <label class="form-label">Preço</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="text" class="form-control price-brl" inputmode="decimal"
                                                   value="{{ number_format(($it['price'] ?? 0)/100, 2, ',', '.') }}">
                                            <input type="hidden" name="price" class="price-cents" value="{{ $it['price'] }}">
                                        </div>
                                    </div>

                                    <div class="col-6 col-md-2">
                                        <label class="form-label">Disponível</label>
                                        <select name="is_available" class="form-select">
                                            <option value="1" @selected($it['is_available'])>Sim</option>
                                            <option value="0" @selected(!$it['is_available'])>Não</option>
                                        </select>
                                    </div>

                                    <div class="col-6 col-md-2">
                                        <label class="form-label">Ordem</label>
                                        <input name="sort_order" type="number" class="form-control" value="{{ $it['sort_order'] }}">
                                    </div>

                                    <div class="col-6 col-md-2 d-grid">
                                        <label class="form-label d-none d-md-block">&nbsp;</label>
                                        <button class="btn btn-primary save-btn">Salvar</button>
                                    </div>

                                    <div class="col-12"><hr class="my-3 section-sep"></div>

                                    <div class="col-12">
                                        <label class="form-label">Descrição</label>
                                        <input name="description" class="form-control" value="{{ $it['description'] }}">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label">Imagem</label>
                                        <img class="d-none mb-2 rounded border image-preview-{{ $i }}"
                                             style="height:72px;object-fit:cover" alt="Pré-visualização">

                                        <input type="file" name="image_file" class="form-control image-input"
                                               data-preview=".image-preview-{{ $i }}" accept="image/*">
                                    </div>
                                </form>
                            </div>

                            <div class="card-footer bg-transparent d-flex justify-content-end">
                                <form method="post"
                                      action="{{ route('admin.menu.items.destroy',['business'=>$biz->id,'section'=>$section,'item'=>$it['id']]) }}">
                                    @csrf @method('delete')
                                    <button class="btn btn-outline-danger">Excluir</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">Nenhum item.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <style>
        .shadow-sm-soft { box-shadow: 0 6px 24px rgba(0,0,0,.06); }
        .section-sep { border-top: 1px dashed #e5e7eb; }
        .item-card .form-label { margin-bottom: .35rem; }

        /* Medalhas de posição */
        .badge-rank {
            background: linear-gradient(135deg,#fe3d28,#fe3d28);
            color: #fff;
            font-weight: 600;
            border-radius: 50%;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .9rem;
            box-shadow: 0 2px 6px rgba(0,0,0,.15);
            margin-top: 10px;
            margin-right: 10px;
        }
    </style>

    @push('scripts')
        <script>
            (function () {
                // Preview imagem
                document.querySelectorAll('.image-input').forEach(function(file){
                    file.addEventListener('change', function(){
                        const sel = file.getAttribute('data-preview');
                        const img = sel ? file.closest('.card').querySelector(sel) : null;
                        if (!img || !file.files?.[0]) return;
                        const r = new FileReader();
                        r.onload = e => { img.src = e.target.result; img.classList.remove('d-none'); };
                        r.readAsDataURL(file.files[0]);
                    });
                });

                // Conversão R$ ↔ centavos
                function brlToCents(str){ const n=String(str).replace(/[^\d,\.]/g,'').replace(/\./g,'').replace(',', '.'); return Math.round(parseFloat(n||'0')*100); }
                function centsToBRL(n){ return (Number(n||0)/100).toLocaleString('pt-BR',{minimumFractionDigits:2, maximumFractionDigits:2}); }

                document.querySelectorAll('.price-brl').forEach(inp=>{
                    const hidden = inp.parentElement.querySelector('.price-cents');
                    if (inp.value.trim()==='' && hidden) inp.value = centsToBRL(hidden.value);
                    inp.addEventListener('input', ()=> hidden.value = brlToCents(inp.value));
                    inp.addEventListener('blur',  ()=> inp.value = centsToBRL(brlToCents(inp.value)));
                });
            })();
        </script>
    @endpush
@endsection
