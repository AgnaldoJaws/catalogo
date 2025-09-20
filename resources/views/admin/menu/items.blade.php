@extends('admin.layout')
@section('title','Itens da Seção')

@section('content')
    <div class="row g-3">
        <div class="col-md-4">
            <form method="post" action="{{ route('admin.menu.items.store',['business'=>$biz->id,'section'=>$section]) }}" class="card">
                @csrf
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label">Nome</label>
                        <input name="name" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Descrição</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Preço (centavos)</label>
                        <input name="price" type="number" class="form-control" value="0" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Imagem (URL)</label>
                        <input name="img_url" class="form-control">
                    </div>
                    <div class="row g-2">
                        <div class="col">
                            <label class="form-label">Disponível</label>
                            <select name="is_available" class="form-select">
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">Ordem</label>
                            <input name="sort_order" type="number" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="mt-2">
                        <label class="form-label">Tempo preparo (min)</label>
                        <input name="prep_min" type="number" class="form-control" value="0">
                    </div>
                    <div class="mt-2">
                        <label class="form-label">Tags (csv)</label>
                        <input name="tags" class="form-control" placeholder="veg,vegan,gluten-free">
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button class="btn btn-primary">Criar item</button>
                </div>
            </form>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($items as $it)
                            <li class="list-group-item">
                                <form class="row g-2 align-items-end" method="post"
                                      action="{{ route('admin.menu.items.update',['business'=>$biz->id,'section'=>$section,'item'=>$it['id']]) }}">
                                    @csrf
                                    <div class="col-md-3">
                                        <label class="form-label">Nome</label>
                                        <input name="name" class="form-control form-control-sm" value="{{ $it['name'] }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Preço (cent)</label>
                                        <input name="price" type="number" class="form-control form-control-sm" value="{{ $it['price'] }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Disponível</label>
                                        <select name="is_available" class="form-select form-select-sm">
                                            <option value="1" @selected($it['is_available'])>Sim</option>
                                            <option value="0" @selected(!$it['is_available'])>Não</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Ordem</label>
                                        <input name="sort_order" type="number" class="form-control form-control-sm" value="{{ $it['sort_order'] }}">
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button class="btn btn-sm btn-primary">Salvar</button>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Descrição</label>
                                        <input name="description" class="form-control form-control-sm" value="{{ $it['description'] }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Imagem (URL)</label>
                                        <input name="img_url" class="form-control form-control-sm" value="{{ $it['img_url'] }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Prep (min)</label>
                                        <input name="prep_min" type="number" class="form-control form-control-sm" value="{{ $it['prep_min'] }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tags</label>
                                        <input name="tags" class="form-control form-control-sm" value="{{ $it['tags'] }}">
                                    </div>
                                </form>
                                <form method="post" class="mt-2 text-end"
                                      action="{{ route('admin.menu.items.destroy',['business'=>$biz->id,'section'=>$section,'item'=>$it['id']]) }}">
                                    @csrf @method('delete')
                                    <button class="btn btn-sm btn-outline-danger">Excluir</button>
                                </form>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">Nenhum item.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
