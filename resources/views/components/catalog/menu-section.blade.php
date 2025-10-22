@props(['section' => ['name'=>'', 'items'=>[]], 'fmt' => fn($c)=>$c])

<h3 id="cat-{{ \Illuminate\Support\Str::slug($section['name']) }}"
    class="h5 mb-3 section-title">{{ $section['name'] }}</h3>

<div class="row row-cols-1 row-cols-md-2 g-3 mb-4">
    @forelse(($section['items'] ?? []) as $it)
        <div class="col">
            <div class="card menu-card h-100" data-price-cents="{{ (int) ($it['price'] ?? 0) }}">
                <img src="{{ $it['img'] ?? 'https://via.placeholder.com/600x400?text=Sem+imagem' }}"
                     class="card-img-top" alt="{{ $it['name'] ?? 'Item' }}">
                <div class="card-body">
                    <h4 class="h6 mb-1">{{ $it['name'] }}</h4>
                    @if(!empty($it['desc']))
                        <p class="text-muted small mb-2">{{ $it['desc'] }}</p>
                    @endif
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="price fw-bold">{{ $fmt($it['price'] ?? 0) }}</div>
                        @if(!empty($waNumber))
                            <button class="btn btn-sm btn-outline-primary js-open-wa"
                                    data-bs-toggle="modal" data-bs-target="#waModal"
                                    data-item-id="{{ $it['id'] }}"
                                    data-item-name="{{ $it['name'] }}"
                                    data-price-cents="{{ (int) ($it['price'] ?? 0) }}">
                                <i class="bi bi-plus-circle"></i> Adicionar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="alert alert-light border">Sem itens nesta seção.</div></div>
    @endforelse
</div>
