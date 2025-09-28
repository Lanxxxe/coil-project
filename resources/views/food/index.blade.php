@extends('layouts.app')
@php use Illuminate\Support\Str; @endphp

@section('title', 'Food')

@section('content')
    @include('components.floating-shapes')

    <header class="mb-6 relative z-10">
        <h1 class="text-2xl font-semibold">Explore Foods</h1>
        <p class="text-sm" style="color: var(--text-secondary)">Browse traditional dishes and discover their stories.</p>
    </header>

    <div class="relative z-10 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        @forelse($foods as $food)
            @php
                $isPH = str_contains(strtolower($food->place_of_origin ?? ''), 'philipp');
                $base = $isPH ? 'images/foods/ph' : 'images/foods/id';
                $filename = $food->filename ?: '';
                $path = $filename ? public_path($base . '/' . $filename) : null;
                $img = ($path && file_exists($path)) ? asset($base . '/' . $filename) : null;
                $slug = $food->slug ?: Str::slug($food->name);
            @endphp
            <article class="border rounded-lg overflow-hidden bg-white/5" style="background: var(--card-bg); border-color: var(--panel-ring)">
                @if($img)
                    <img src="{{ $img }}" loading="lazy" alt="{{ $food->name }}" class="w-full h-40 object-cover" />
                @else
                    <div class="w-full h-40 bg-gradient-to-br from-orange-500 to-red-600"></div>
                @endif
                <div class="p-4 space-y-2">
                    <h2 class="font-semibold text-lg"><a href="{{ route('food.show', $slug) }}" class="underline">{{ $food->name }}</a></h2>
                    <p class="text-sm" style="color: var(--text-secondary)">{{ Str::limit($food->description ?? $food->caption ?? '—', 120) }}</p>
                    <a href="{{ route('food.show', $slug) }}" class="inline-flex items-center gap-2 text-sm underline">Read more</a>
                </div>
            </article>
        @empty
            <p>No foods available.</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $foods->links() }}</div>
@endsection
