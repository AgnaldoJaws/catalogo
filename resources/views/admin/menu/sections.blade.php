@extends('admin.layout')
@section('title','Seções do Cardápio')

@section('content')
    <div class="row g-3">
        <div class="col-md-4">
            <form method="post" action="{{ route('admin.menu.sections.store',['business'=>$biz->id]) }}" class="card">
                @csrf
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label">Nome da seção</label>
                        <input name="name" class="form-control" placeholder="Ex.: Pizzas">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Ordem</label>
                        <input name="sort_order" type="number" class="form-control" value="0">
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button class="btn btn-primary">Criar seção</button>
                </div>
            </form>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($sections as $sec)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">{{ $sec['name'] }}</div>
                                    <div class="text-muted small">ordem: {{ $sec['sort_order'] }}</div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a class="btn btn-sm btn-outline-secondary"
                                       href="{{ route('admin.menu.items.index',['business'=>$biz->id,'section'=>$sec['id']]) }}">
                                        Itens
                                    </a>
                                    <form method="post" action="{{ route('admin.menu.sections.update',['business'=>$biz->id,'section'=>$sec['id']]) }}" class="d-inline-flex gap-2">
                                        @csrf
                                        <input type="text" name="name" value="{{ $sec['name'] }}" class="form-control form-control-sm">
                                        <input type="number" name="sort_order" value="{{ $sec['sort_order'] }}" class="form-control form-control-sm" style="width:100px">
                                        <button class="btn btn-sm btn-primary">Salvar</button>
                                    </form>
                                    <form method="post" action="{{ route('admin.menu.sections.destroy',['business'=>$biz->id,'section'=>$sec['id']]) }}">
                                        @csrf @method('delete')
                                        <button class="btn btn-sm btn-outline-danger">Excluir</button>
                                    </form>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">Nenhuma seção criada.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
