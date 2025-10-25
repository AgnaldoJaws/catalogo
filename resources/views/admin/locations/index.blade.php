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
{{--                        <div class="col">--}}
{{--                            <label class="form-label">Telefone</label>--}}
{{--                            <input name="phone" id="phone" class="form-control">--}}
{{--                        </div>--}}
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
                               <th>Cidade</th><th>Endereço</th><th>Status</th><th class="text-end">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($locations as $loc)
                                <tr id="row-{{ $loc['id'] }}">
                                    <td>{{ $loc['city']['name'] }}</td>
                                    <td>{{ $loc['address'] }}</td>
                                    <td>
                                        <span class="badge {{ $loc['status'] ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $loc['status'] ? 'Aberto':'Fechado' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
{{--                                        <button--}}
{{--                                            class="btn btn-sm btn-outline-primary me-2 btn-edit"--}}
{{--                                            data-id="{{ $loc['id'] }}"--}}
{{--                                        >Editar</button>--}}

                                        <form method="post" action="{{ route('admin.locations.status',['location'=>$loc['id']]) }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="{{ $loc['status'] ? 0 : 1 }}">
                                            <button class="btn btn-sm {{ $loc['status'] ? 'btn-outline-secondary' : 'btn-outline-success' }}">
                                                {{ $loc['status'] ? 'Fechar loja' : 'Abrir loja' }}
                                            </button>
                                        </form>

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
                </div>
            </div>
        </div>
    </div>

    {{-- Offcanvas de Edição --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="editLocationCanvas" aria-labelledby="editLocationCanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="editLocationCanvasLabel">Editar local</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form method="post" action="{{ route('admin.locations.store',['business'=>$biz->id]) }}">
                @csrf
                <input type="hidden" name="id" id="e-id">

                <div class="mb-2">
                    <label class="form-label">Cidade</label>
                    <select name="city_id" id="e-city_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        @foreach($cities as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-2">
                    <label class="form-label">Endereço</label>
                    <input name="address" id="e-address" class="form-control" required>
                </div>

                <div class="row g-2">
                    <div class="col">
                        <label class="form-label">Lat</label>
                        <input name="lat" id="e-lat" class="form-control" inputmode="decimal">
                    </div>
                    <div class="col">
                        <label class="form-label">Lng</label>
                        <input name="lng" id="e-lng" class="form-control" inputmode="decimal">
                    </div>
                </div>

                <div class="row g-2 mt-2">
{{--                    <div class="col">--}}
{{--                        <label class="form-label">Telefone</label>--}}
{{--                        <input name="phone" id="e-phone" class="form-control">--}}
{{--                    </div>--}}
                    <div class="col">
                        <label class="form-label">WhatsApp</label>
                        <input name="whatsapp" id="e-whatsapp" class="form-control">
                    </div>
                </div>

                <div class="mt-2">
                    <label class="form-label">Status</label>
                    <select name="status" id="e-status" class="form-select" required>
                        <option value="1">Aberto</option>
                        <option value="0">Fechado</option>
                    </select>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-primary">Salvar</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        (function(){
            const offcanvasEl = document.getElementById('editLocationCanvas');
            let offcanvasInstance;

            function ensureOffcanvas() {
                if (!offcanvasInstance) {
                    offcanvasInstance = new bootstrap.Offcanvas(offcanvasEl);
                }
                return offcanvasInstance;
            }

            document.addEventListener('click', async function(e){
                const btn = e.target.closest('.btn-edit');
                if(!btn) return;

                const id = btn.dataset.id;
                const url = "{{ route('admin.locations.show',['location'=>'__ID__']) }}".replace('__ID__', id);

                try {
                    const rsp = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
                    if (!rsp.ok) throw new Error('Falha ao carregar local');
                    const data = await rsp.json();

                    document.getElementById('e-id').value        = data.id ?? '';
                    document.getElementById('e-city_id').value   = data.city_id ?? '';
                    document.getElementById('e-address').value   = data.address ?? '';
                    document.getElementById('e-lat').value       = data.lat ?? '';
                    document.getElementById('e-lng').value       = data.lng ?? '';
                    document.getElementById('e-phone').value     = data.phone ?? '';
                    document.getElementById('e-whatsapp').value  = data.whatsapp ?? '';
                    document.getElementById('e-status').value    = (data.status ?? 1);

                    ensureOffcanvas().show();
                } catch (err) {
                    alert(err.message || 'Erro ao carregar dados do local');
                }
            });

            const cancelBtn = document.getElementById('cancel-edit');
            cancelBtn?.addEventListener('click', () => {
                document.getElementById('loc-id').value = '';
                document.getElementById('city_id').value = '';
                document.getElementById('address').value = '';
                document.getElementById('lat').value = '';
                document.getElementById('lng').value = '';
                document.getElementById('phone').value = '';
                document.getElementById('whatsapp').value = '';
                document.getElementById('status').value = '1';
            });
        })();
    </script>
@endsection
