<?php

namespace App\Livewire\Food;

use App\Models\Food as FoodModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\View\View as ViewView;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $category = null;
    public ?string $place = null;
    public ?float $minPrice = null;
    public ?float $maxPrice = null;
    public int $perPage = 9;

    /** @var array<string> */
    public array $categories = [];
    /** @var array<string> */
    public array $places = [];

    public function mount(): void
    {
        // Load filter options from model helpers
        if (method_exists(FoodModel::class, 'getCategories')) {
            $this->categories = FoodModel::getCategories()->values()->all();
        }
        if (method_exists(FoodModel::class, 'getPlacesOfOrigin')) {
            $this->places = FoodModel::getPlacesOfOrigin()->values()->all();
        }
    }

    public function updating($name, $value): void
    {
        $this->resetPage();
    }

    #[Layout('layouts.app')]
    public function render(): ViewView
    {
        $query = FoodModel::query();

        if ($this->search !== '') {
            $term = $this->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%")
                  ->orWhere('caption', 'like', "%{$term}%");
            });
        }

        if ($this->category) {
            $query->where('category', $this->category);
        }

        if ($this->place) {
            $query->where('place_of_origin', 'like', "%{$this->place}%");
        }

        if ($this->minPrice !== null) {
            $query->where('price', '>=', $this->minPrice);
        }
        if ($this->maxPrice !== null) {
            $query->where('price', '<=', $this->maxPrice);
        }

        /** @var LengthAwarePaginator $items */
        $items = $query->orderBy('name')->paginate($this->perPage);

        return view('livewire.food.index', [
            'items' => $items,
        ]);
    }
}
