@extends('layouts.app')
@php use Illuminate\Support\Str; @endphp

@section('title', 'Places')

@section('content')
    @include('components.floating-shapes')

    <header class="mb-6 relative z-10">
        <h1 class="text-2xl font-semibold">Explore Places</h1>
        <p class="text-sm" style="color: var(--text-secondary)">Browse destinations and cultural highlights.</p>
    </header>

    <div class="relative z-10 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        @forelse($places as $place)
            @php
                $cover = optional($place->photos->first())->filename;
                $base = $place->country === 'Philippines' ? 'images/places/ph' : 'images/places/id';
                $path = $cover ? public_path($base.'/'.$cover) : null;
                $img = ($path && file_exists($path)) ? asset($base.'/'.$cover) : null;
                $slug = $place->slug ?: Str::slug($place->name);
            @endphp
            <article class="border rounded-lg overflow-hidden" style="background: var(--card-bg); border-color: var(--panel-ring)">
                @if($img)
                    <img src="{{ $img }}" loading="lazy" alt="{{ $place->name }}" class="w-full h-40 object-cover" />
                @else
                    <div class="w-full h-40 bg-gradient-to-br from-purple-500 to-blue-600"></div>
                @endif
                <div class="p-4 space-y-2">
                    <h2 class="font-semibold text-lg"><a href="{{ route('places.show', $slug) }}" class="underline">{{ $place->name }}</a></h2>
                    <p class="text-sm" style="color: var(--text-secondary)">{{ Str::limit($place->description ?? $place->caption ?? '—', 120) }}</p>
                    <a href="{{ route('places.show', $slug) }}" class="inline-flex items-center gap-2 text-sm underline">View details</a>
                </div>
            </article>
        @empty
            <p>No places available.</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $places->links() }}</div>
@endsection
