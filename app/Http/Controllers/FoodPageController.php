<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FoodPageController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) max(1, min(48, (int) $request->get('per_page', 12)));
        $foods = Food::orderBy('name')->paginate($perPage)->withQueryString();
        return view('food.index', compact('foods'));
    }

    public function show(string $slug): View
    {
        $food = Food::getBySlug($slug)
            ?: Food::getByName(urldecode(str_replace('-', ' ', $slug)))
            ?: Food::getByNameLike($slug);

        abort_if(!$food, 404);

        return view('food.show', compact('food'));
    }
}
