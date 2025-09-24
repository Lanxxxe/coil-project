@extends('layouts.app')

@section('title', $place->name)

@section('content')
    @php
        $cover = optional($place->photos->first())->filename;
        $base = $place->country === 'Philippines' ? 'images/places/ph' : 'images/places/id';
        $path = $cover ? public_path($base.'/'.$cover) : null;
        $img = ($path && file_exists($path)) ? asset($base.'/'.$cover) : null;
    @endphp

    <article class="grid md:grid-cols-3 gap-6 items-start">
        <div class="md:col-span-2 space-y-4">
            <h1 class="text-3xl font-bold">{{ $place->name }}</h1>
            @if($img)
                <img src="{{ $img }}" alt="{{ $place->name }}" loading="lazy" class="w-full h-[360px] object-cover rounded" />
            @endif
            <div class="prose prose-invert max-w-none">
                <p>{{ $place->description ?? 'No description available.' }}</p>
                @if($place->caption)
                    <blockquote class="mt-4">{{ $place->caption }}</blockquote>
                @endif
            </div>

            @if($place->photos && $place->photos->count() > 1)
                <div class="grid grid-cols-2 gap-3 mt-4">
                    @foreach($place->photos->slice(1) as $photo)
                        @php
                            $p = $photo->filename ? public_path($base.'/'.$photo->filename) : null;
                            $pi = ($p && file_exists($p)) ? asset($base.'/'.$photo->filename) : null;
                        @endphp
                        @if($pi)
                            <img src="{{ $pi }}" loading="lazy" alt="{{ $photo->caption ?? $place->name }}" class="w-full h-36 object-cover rounded" />
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
        <aside class="space-y-2 p-4 border rounded" style="border-color: var(--panel-ring); background: var(--card-bg)">
            <div><span class="font-semibold">Country:</span> {{ $place->country ?? '—' }}</div>
            <div><span class="font-semibold">Type:</span> {{ $place->type ?? '—' }}</div>
            <div><span class="font-semibold">Location:</span> {{ $place->location ?? '—' }}</div>
            <div><span class="font-semibold">Coordinates:</span> {{ $place->latitude }}, {{ $place->longitude }}</div>
        </aside>
    </article>
@endsection
