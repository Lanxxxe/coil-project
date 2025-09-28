<?php

namespace App\Livewire\Places;

use App\Models\Places as PlacesModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\View\View as ViewView;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $type = null;
    public ?string $country = null;
    public int $perPage = 9;

    /** @var array<string> */
    public array $types = ['landmark','restaurant','heritage','gallery','museum','other'];
    /** @var array<string> */
    public array $countries = [];

    public function mount(): void
    {
        if (method_exists(PlacesModel::class, 'getCountries')) {
            $this->countries = PlacesModel::getCountries()->values()->all();
        }
    }

    public function updating($name, $value): void
    {
        $this->resetPage();
    }

    #[Layout('layouts.app')]
    public function render(): ViewView
    {
        $query = PlacesModel::query()->with('photos');

        if ($this->search !== '') {
            $term = $this->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%")
                  ->orWhere('location', 'like', "%{$term}%")
                  ->orWhere('caption', 'like', "%{$term}%");
            });
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->country) {
            $query->where('country', $this->country);
        }

        /** @var LengthAwarePaginator $places */
        $places = $query->orderBy('name')->paginate($this->perPage);

        return view('livewire.places.index', [
            'places' => $places,
        ]);
    }
}
