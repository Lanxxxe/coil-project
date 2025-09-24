@extends('layouts.app')

@section('title', $food->name)

@section('content')
    @php
        $isPH = str_contains(strtolower($food->place_of_origin ?? ''), 'philipp');
        $base = $isPH ? 'images/foods/ph' : 'images/foods/id';
        $filename = $food->filename ?: '';
        $path = $filename ? public_path($base . '/' . $filename) : null;
        $img = ($path && file_exists($path)) ? asset($base . '/' . $filename) : null;
    @endphp

    <article class="grid md:grid-cols-3 gap-6 items-start">
        <div class="md:col-span-2 space-y-4">
            <h1 class="text-3xl font-bold">{{ $food->name }}</h1>
            @if($img)
                <img src="{{ $img }}" alt="{{ $food->name }}" loading="lazy" class="w-full h-[360px] object-cover rounded" />
            @endif
            <div class="prose prose-invert max-w-none">
                <p>{{ $food->description ?? 'No description available.' }}</p>
                @if($food->caption)
                    <blockquote class="mt-4">{{ $food->caption }}</blockquote>
                @endif
            </div>
        </div>
        <aside class="space-y-2 p-4 border rounded" style="border-color: var(--panel-ring); background: var(--card-bg)">
            <div><span class="font-semibold">Origin:</span> {{ $food->place_of_origin ?? '—' }}</div>
            <div><span class="font-semibold">Category:</span> {{ $food->category ?? '—' }}</div>
            <div><span class="font-semibold">Price:</span> {{ $food->price !== null ? number_format((float)$food->price, 2) : '—' }}</div>
        </aside>
    </article>
@endsection
