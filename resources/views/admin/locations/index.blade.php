@extends('admin.layout')
@section('title','Locais')

@section('content')
    <div class="row g-3">
        <div class="col-md-5">
            <form id="location-form" method="post" action="{{ route('admin.locations.store',['business'=>$biz->id]) }}" class="card">
                @csrf
                <div class="card-body">
                    <input type="hidden" name="id" id="loc-id" value="">

                    <div class="mb-2">
                        <label class="form-label">Cidade</label>
                        <select name="city_id" id="city_id" class="form-select" required>
                            <option value="">Selecione...</option>
                            @foreach($cities as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Endereço</label>
                        <input name="address" id="address" class="form-control" required>
                    </div>

                    <div class="row g-2">
                        <div class="col">
                            <label class="form-label">Lat</label>
                            <input name="lat" id="lat" class="form-control" inputmode="decimal">
                        </div>
                        <div class="col">
                            <label class="form-label">Lng</label>
                            <input name="lng" id="lng" class="form-control" inputmode="decimal">
                        </div>
                    </div>

                    <div class="row g-2 mt-2">
                        <div class="col">
                            <label class="form-label">Telefone</label>
                            <input name="phone" id="phone" class="form-control">
                        </div>
                        <div class="col">
                            <label class="form-label">WhatsApp</label>
                            <input name="whatsapp" id="whatsapp" class="form-control">
                        </div>
                    </div>

                    <div class="mt-2">
                        <label class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="1">Aberto</option>
                            <option value="0">Fechado</option>
                        </select>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <button class="btn btn-primary" id="save-btn">Salvar local</button>
                    <button type="button" class="btn btn-outline-secondary d-none" id="cancel-edit">Cancelar edição</button>
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
                                <th>ID</th><th>Cidade</th><th>Endereço</th><th>Status</th><th class="text-end">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($locations as $loc)
                                <tr id="row-{{ $loc['id'] }}">
                                    <td>{{ $loc['id'] }}</td>
                                    <td>{{ $citiesMap[$loc['city_id']] ?? $loc['city_id'] }}</td>
                                    <td>{{ $loc['address'] }}</td>
                                    <td>
                  <span class="badge {{ $loc['status'] ? 'bg-success' : 'bg-secondary' }}">
                    {{ $loc['status'] ? 'Aberto':'Fechado' }}
                  </span>
                                    </td>
                                    <td class="text-end">
                                        <button
                                            class="btn btn-sm btn-outline-primary me-2 btn-edit"
                                            data-id="{{ $loc['id'] }}"
                                            data-city_id="{{ $loc['city_id'] }}"
                                            data-address="{{ $loc['address'] }}"
                                            data-lat="{{ $loc['lat'] }}"
                                            data-lng="{{ $loc['lng'] }}"
                                            data-phone="{{ $loc['phone'] }}"
                                            data-whatsapp="{{ $loc['whatsapp'] }}"
                                            data-status="{{ (int)$loc['status'] }}"
                                        >Editar</button>

                                        <form method="post" action="{{ route('admin.locations.destroy',['business'=>$biz->id,'location'=>$loc['id']]) }}" class="d-inline">
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

                    {{-- (Opcional) Toggle rápido de status via post para o mesmo upsert --}}
                    {{-- <form method="post" action="{{ route('admin.locations.store',['business'=>$biz->id]) }}">@csrf
                        <input type="hidden" name="id" value="{{ $loc['id'] }}">
                        <input type="hidden" name="city_id" value="{{ $loc['city_id'] }}">
                        <input type="hidden" name="address" value="{{ $loc['address'] }}">
                        <input type="hidden" name="status" value="{{ $loc['status']?0:1 }}">
                        <button class="btn btn-sm btn-outline-secondary">Abrir/Fechar</button>
                    </form> --}}
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            const form = document.getElementById('location-form');
            const saveBtn = document.getElementById('save-btn');
            const cancelBtn = document.getElementById('cancel-edit');

            function fillForm(data) {
                document.getElementById('loc-id').value   = data.id || '';
                document.getElementById('city_id').value  = data.city_id || '';
                document.getElementById('address').value  = data.address || '';
                document.getElementById('lat').value      = data.lat || '';
                document.getElementById('lng').value      = data.lng || '';
                document.getElementById('phone').value    = data.phone || '';
                document.getElementById('whatsapp').value = data.whatsapp || '';
                document.getElementById('status').value   = (data.status ?? 1);
                const editing = !!data.id;
                saveBtn.textContent = editing ? 'Salvar alterações' : 'Salvar local';
                cancelBtn.classList.toggle('d-none', !editing);
            }

            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', () => {
                    fillForm({
                        id: btn.dataset.id,
                        city_id: btn.dataset.city_id,
                        address: btn.dataset.address,
                        lat: btn.dataset.lat,
                        lng: btn.dataset.lng,
                        phone: btn.dataset.phone,
                        whatsapp: btn.dataset.whatsapp,
                        status: btn.dataset.status
                    });
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            });

            cancelBtn?.addEventListener('click', () => fillForm({}));
        </script>
    @endsection
@endsection
