@props(['categories' => []]) {{-- array de strings --}}
<div class="category-scroll-wrapper">
    <div class="category-scroll">
        @foreach($categories as $name)
            <a class="chip-link" href="#cat-{{ \Illuminate\Support\Str::slug($name) }}">{{ $name }}</a>
        @endforeach
    </div>
</div>
