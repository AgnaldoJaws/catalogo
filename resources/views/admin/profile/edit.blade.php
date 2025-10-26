@extends('admin.layout')
@section('title','Perfil do negócio')

@section('content')
    <form method="post"
          action="{{ route('admin.profile.update',['business'=>$biz->id]) }}"
          class="card shadow-sm border-0"
          enctype="multipart/form-data">
        @csrf

        <div class="card-body">

            {{-- LOGO --}}
            <div class="row g-3 align-items-center mb-3">
                <div class="col-auto">
                    <div style="width:80px;height:80px;border:1px solid #eee;border-radius:12px;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#fafafa">
                        @php $logo = $biz->logo_src ?? null; @endphp
                        @if($logo)
                            <img src="{{ $logo }}" alt="Logo {{ $biz->name }}"
                                 style="width:100%;height:100%;object-fit:cover">
                        @else
                            <span class="text-muted small">Sem logo</span>
                        @endif
                    </div>
                </div>
                <div class="col">
                    <label class="form-label fw-semibold">Logo</label>
                    <input type="file" name="logo_file" class="form-control" accept="image/*">
                </div>
            </div>

            {{-- NOME --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Nome do Negócio</label>
                <input name="name" class="form-control form-control-lg" value="{{ old('name',$biz->name) }}" required>
            </div>

            {{-- WHATSAPP --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">WhatsApp</label>
                <input name="whatsapp" id="whatsapp" class="form-control" value="{{ old('whatsapp',$biz->whatsapp) }}"
                       placeholder="Ex: 11999999999">
            </div>

            {{-- SOBRE --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Sobre</label>
                <textarea name="about" class="form-control" rows="4" placeholder="Conte um pouco sobre o seu negócio...">{{ old('about',$biz->about) }}</textarea>
            </div>

            {{-- CATEGORIAS --}}
            <div class="card mt-4 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-tags-fill text-primary me-1"></i>
                        Categorias do seu negócio
                    </h5>

                    <p class="text-muted mb-3">
                        Escolha as categorias que melhor representam o que você vende.
                        <br>
                        <small>Exemplo: Marmitas, Lanches, Sobremesas, Bebidas...</small>
                    </p>

                    {{-- Selecionadas --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Selecionadas</label>
                        <div id="selected-categories" class="d-flex flex-wrap gap-2">
                            @forelse($selectedCategories as $cat)
                                <span class="badge bg-success text-white fs-6 px-3 py-2 rounded-pill">
                                 {{ $cat->name }}
                                <button type="button"
                                        class="btn btn-sm btn-link text-white ms-1 p-0 remove-category"
                                        data-id="{{ $cat->id }}">
                                    <i class="bi bi-x-circle-fill"></i>
                                </button>
                            </span>
                            @empty
                                <span class="text-muted small">Nenhuma categoria selecionada.</span>
                            @endforelse
                        </div>
                    </div>

                    {{-- Todas as categorias --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Adicionar novas</label>
                        <div id="all-categories" class="d-flex flex-wrap gap-2">
                            @foreach($allCategories as $cat)
                                <button type="button"
                                        class="btn btn-outline-primary fs-6 add-category"
                                        data-id="{{ $cat->id }}">
                                     {{ $cat->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <input type="hidden" name="category_ids" id="category_ids"
                           value="{{ $selectedCategories->pluck('id')->implode(',') }}">
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button class="btn btn-primary btn-lg px-4">
                <i class="bi bi-save me-2"></i>Salvar alterações
            </button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectedEl = document.getElementById('selected-categories');
            const input = document.getElementById('category_ids');

            function updateHiddenInput() {
                const ids = Array.from(selectedEl.querySelectorAll('.remove-category'))
                    .map(b => b.dataset.id);
                input.value = ids.join(',');
            }

            document.querySelectorAll('.add-category').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    const name = btn.textContent.trim();

                    if (selectedEl.querySelector(`[data-id="${id}"]`)) return;

                    const badge = document.createElement('span');
                    badge.className = 'badge bg-success text-white fs-6 px-3 py-2 rounded-pill';
                    badge.innerHTML = `${name}
                <button type="button" class="btn btn-sm btn-link text-white ms-1 p-0 remove-category" data-id="${id}">
                    <i class="bi bi-x-circle-fill"></i>
                </button>`;
                    selectedEl.appendChild(badge);

                    updateHiddenInput();

                    badge.querySelector('.remove-category').addEventListener('click', e => {
                        e.target.closest('span').remove();
                        updateHiddenInput();
                    });
                });
            });

            selectedEl.querySelectorAll('.remove-category').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.target.closest('span').remove();
                    updateHiddenInput();
                });
            });
        });
    </script>
@endpush
