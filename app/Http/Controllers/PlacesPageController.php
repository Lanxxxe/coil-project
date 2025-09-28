<?php

namespace App\Http\Controllers;

use App\Models\Places;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlacesPageController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) max(1, min(48, (int) $request->get('per_page', 12)));
        $places = Places::orderBy('name')->paginate($perPage)->withQueryString();
        return view('places.index', compact('places'));
    }

    public function show(string $slug): View
    {
        $place = Places::getBySlugWithPhotos($slug)
            ?: Places::getByNameWithPhotos(str_replace('-', ' ', urldecode($slug)))
            ?: Places::getByNameLikeWithPhotos($slug);

        abort_if(!$place, 404);

        return view('places.show', compact('place'));
    }
}
