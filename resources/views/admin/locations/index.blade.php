@extends('admin.layout')
@section('title','Locais')

@section('content')
    <div class="row g-3">
        <div class="col-md-5">
            <form method="post" action="{{ route('admin.locations.store',['business'=>$biz->id]) }}" class="card">
                @csrf
                <div class="card-body">
                    <input type="hidden" name="id" value="">
                    <div class="mb-2">
                        <label class="form-label">Cidade (ID)</label>
                        <input name="city_id" class="form-control" placeholder="ID da cidade">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Endereço</label>
                        <input name="address" class="form-control">
                    </div>
                    <div class="row g-2">
                        <div class="col">
                            <label class="form-label">Lat</label>
                            <input name="lat" class="form-control">
                        </div>
                        <div class="col">
                            <label class="form-label">Lng</label>
                            <input name="lng" class="form-control">
                        </div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col">
                            <label class="form-label">Telefone</label>
                            <input name="phone" class="form-control">
                        </div>
                        <div class="col">
                            <label class="form-label">WhatsApp</label>
                            <input name="whatsapp" class="form-control">
                        </div>
                    </div>
                    <div class="mt-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="1">Aberto</option>
                            <option value="0">Fechado</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button class="btn btn-primary">Salvar local</button>
                </div>
            </form>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                            <tr>
                                <th>ID</th><th>Cidade</th><th>Endereço</th><th>Status</th><th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($locations as $loc)
                                <tr>
                                    <td>{{ $loc['id'] }}</td>
                                    <td>{{ $loc['city_id'] }}</td>
                                    <td>{{ $loc['address'] }}</td>
                                    <td>{{ $loc['status'] ? 'Aberto':'Fechado' }}</td>
                                    <td class="text-end">
                                        <form method="post" action="{{ route('admin.locations.destroy',['business'=>$biz->id,'location'=>$loc['id']]) }}">
                                            @csrf @method('delete')
                                            <button class="btn btn-sm btn-outline-danger">Remover</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-muted">Nenhum local.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
