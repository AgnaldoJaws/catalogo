<section class="py-4">
    <div class="container">

        @php
            // Pegamos a coleção independentemente do tipo de paginator
            $items = $page instanceof \Illuminate\Pagination\AbstractPaginator
              ? $page->getCollection()
              : collect($page ?? []);

        @endphp

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
            @forelse($items as $loc)
                <div class="col-12">
                    @include('home.components.card-restaurant-flex', ['loc' => $loc])
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border">Nenhum resultado encontrado.</div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{-- Mantém a paginação bonita do Bootstrap --}}
            @if($page instanceof \Illuminate\Pagination\AbstractPaginator)
                {{ $page->withQueryString()->onEachSide(1)->links() }}
            @endif
        </div>

    </div>
</section>
