<section class="space-y-6">
    <header class="space-y-1">
        <h1 class="text-2xl font-semibold">Foods of the Philippines & Indonesia</h1>
    <p class="text-sm" style="color: var(--text-secondary)">Browse traditional dishes. Use search and filters to explore.</p>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
        <input type="text" placeholder="Search by name, description..." class="md:col-span-2 border px-3 py-2 rounded" wire:model.debounce.300ms="search">

        <select class="border px-3 py-2 rounded" wire:model="category">
            <option value="">All categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
            @endforeach
        </select>

        <select class="border px-3 py-2 rounded" wire:model="place">
            <option value="">All places of origin</option>
            @foreach($places as $p)
                <option value="{{ $p }}">{{ $p }}</option>
            @endforeach
        </select>

        <input type="number" step="0.01" placeholder="Min price" class="border px-3 py-2 rounded" wire:model.lazy="minPrice">
        <input type="number" step="0.01" placeholder="Max price" class="border px-3 py-2 rounded" wire:model.lazy="maxPrice">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse($items as $food)
            <article class="border rounded overflow-hidden" style="background-color: var(--card-bg)">
                @php
                    $isPH = str_contains(strtolower($food->place_of_origin ?? ''), 'philipp');
                    $base = $isPH ? 'images/foods/ph' : 'images/foods/id';
                    $filename = $food->filename ?: '';
                    $path = $filename ? public_path($base . '/' . $filename) : null;
                @endphp
                @if($path && file_exists($path))
                    <img src="{{ asset($base . '/' . $filename) }}" alt="{{ $food->name }}" class="w-full h-40 object-cover">
                @endif
                <div class="p-4 space-y-2">
                    <div class="flex items-center justify-between">
                        <h2 class="font-medium">{{ $food->name }}</h2>
                        <span class="text-xs" style="color: var(--text-secondary)">{{ $food->category }}</span>
                    </div>
                    <p class="text-sm" style="color: var(--text-secondary)">{{ $food->description }}</p>
                    <div class="text-sm"><span class="font-semibold">Origin:</span> {{ $food->place_of_origin ?? '—' }}</div>
                    <div class="text-sm"><span class="font-semibold">Price:</span> {{ $food->price !== null ? number_format((float)$food->price, 2) : '—' }}</div>
                </div>
            </article>
        @empty
            <p>No results found.</p>
        @endforelse
    </div>

    <div>
        {{ $items->links() }}
    </div>
</section>
