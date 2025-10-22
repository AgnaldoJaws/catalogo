@extends('admin.layout')
@section('title','Seções do Cardápio')

@section('content')
    <div class="row g-4">
        {{-- Criar nova seção --}}
        <div class="col-md-4">
            <form method="post" action="{{ route('admin.menu.sections.store',['business'=>$biz->id]) }}" class="card shadow-sm">
                @csrf
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Nova seção</h5>

                    <div class="mb-3">
                        <label class="form-label">Nome da seção</label>
                        <input name="name" class="form-control form-control-lg" placeholder="Ex.: Pizzas" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ordem</label>
                        <input name="sort_order" type="number" class="form-control" value="0">
                    </div>
                </div>
                <div class="card-footer text-end bg-transparent border-top">
                    <button class="btn btn-primary btn-lg w-100">Criar seção</button>
                </div>
            </form>
        </div>

        {{-- Listagem de seções --}}
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Seções do cardápio</h5>

                    @forelse($sections as $index => $sec)
                        <div class="section-item border rounded-4 p-3 mb-3 d-flex flex-wrap justify-content-between align-items-center shadow-sm-sm bg-white">
                            <div class="d-flex align-items-center gap-3 flex-grow-1">
                                {{-- Medalha de posição --}}
                                <div class="order-badge {{ $index === 0 ? 'gold' : ($index === 1 ? 'silver' : ($index === 2 ? 'bronze' : 'neutral')) }}">
                                    <span>{{ $index + 1 }}º</span>
                                </div>

                                <div>
                                    <div class="fw-semibold fs-5 text-capitalize">{{ $sec['name'] }}</div>
                                    <div class="text-muted small">Ordem atual: {{ $sec['sort_order'] }}</div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0 align-items-center">
                                {{-- Botão de Itens com contagem --}}
                                <a class="btn btn-sm btn-outline-secondary position-relative"
                                   href="{{ route('admin.menu.items.index',['business'=>$biz->id,'section'=>$sec['id']]) }}">
                                    <i class="bi bi-list"></i> Itens
                                    @if(isset($sec['items_count']))
                                        <span class="badge bg-primary position-absolute top-0 start-100 translate-middle rounded-pill">
                                        {{ $sec['items_count'] }}
                                    </span>
                                    @endif
                                </a>

                                {{-- Form de atualização --}}
                                <form method="post"
                                      action="{{ route('admin.menu.sections.update',['business'=>$biz->id,'section'=>$sec['id']]) }}"
                                      class="d-flex gap-2 align-items-center">
                                    @csrf
                                    <input type="text" name="name" value="{{ $sec['name'] }}" class="form-control form-control-sm" style="min-width:120px">
                                    <input type="number" name="sort_order" value="{{ $sec['sort_order'] }}" class="form-control form-control-sm" style="width:80px">
                                    <button class="btn btn-sm btn-primary">Salvar</button>
                                </form>

                                <form method="post"
                                      action="{{ route('admin.menu.sections.destroy',['business'=>$biz->id,'section'=>$sec['id']]) }}">
                                    @csrf @method('delete')
                                    <button class="btn btn-sm btn-outline-danger">Excluir</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted text-center py-4">
                            Nenhuma seção criada ainda.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <style>
        .section-item {
            transition: all .2s ease-in-out;
        }
        .section-item:hover {
            background: #f8f9fa;
            transform: scale(1.01);
        }
        .shadow-sm-sm {
            box-shadow: 0 3px 10px rgba(0,0,0,.05);
        }
        .order-badge {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
            font-size: 15px;
        }
        .order-badge.gold { background: linear-gradient(135deg,#f4d03f,#f1c40f); box-shadow: 0 0 6px rgba(244,208,63,.5); }
        .order-badge.silver { background: linear-gradient(135deg,#bdc3c7,#95a5a6); box-shadow: 0 0 6px rgba(189,195,199,.4); }
        .order-badge.bronze { background: linear-gradient(135deg,#cd7f32,#b87333); box-shadow: 0 0 6px rgba(205,127,50,.4); }
        .order-badge.neutral { background: #6c757d; box-shadow: 0 0 6px rgba(108,117,125,.3); }
    </style>
@endsection
