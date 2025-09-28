<section class="space-y-6">
    <header class="space-y-1">
        <h1 class="text-2xl font-semibold">Places in the Philippines & Indonesia</h1>
    <p class="text-sm" style="color: var(--text-secondary)">Discover landmarks, heritage sites, museums, and more.</p>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <input type="text" placeholder="Search by name, description, location..." class="md:col-span-2 border px-3 py-2 rounded" wire:model.debounce.300ms="search">

        <select class="border px-3 py-2 rounded" wire:model="type">
            <option value="">All types</option>
            @foreach($types as $t)
                <option value="{{ $t }}">{{ ucfirst($t) }}</option>
            @endforeach
        </select>

        <select class="border px-3 py-2 rounded" wire:model="country">
            <option value="">All countries</option>
            @foreach($countries as $c)
                <option value="{{ $c }}">{{ $c }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse($places as $place)
            <article class="border rounded overflow-hidden" style="background-color: var(--card-bg)">
                @php
                    $isPH = strtolower($place->country ?? '') === 'philippines';
                    $base = $isPH ? 'images/places/ph' : 'images/places/id';
                    $firstPhoto = $place->photos->first();
                    $filename = $firstPhoto->filename ?? null;
                    $path = $filename ? public_path($base . '/' . $filename) : null;
                @endphp
                @if($path && file_exists($path))
                    <img src="{{ asset($base . '/' . $filename) }}" alt="{{ $place->name }}" class="w-full h-40 object-cover">
                @endif
                <div class="p-4 space-y-2">
                    <div class="flex items-center justify-between">
                        <h2 class="font-medium">{{ $place->name }}</h2>
                        <span class="text-xs capitalize" style="color: var(--text-secondary)">{{ $place->type }}</span>
                    </div>
                    <p class="text-sm" style="color: var(--text-secondary)">{{ $place->description }}</p>
                    <div class="text-sm"><span class="font-semibold">Country:</span> {{ $place->country ?? '—' }}</div>
                    <div class="text-sm"><span class="font-semibold">Location:</span> {{ $place->location ?? '—' }}</div>
                    <div class="text-xs" style="color: var(--text-secondary)">Photos: {{ $place->photos->count() }}</div>
                </div>
            </article>
        @empty
            <p>No results found.</p>
        @endforelse
    </div>

    <div>
        {{ $places->links() }}
    </div>
</section>
