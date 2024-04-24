<?php

namespace App\Livewire;

use App\Livewire\Forms\ProductForm;
use App\Models\{Category, Country, Product};
use Livewire\Component;
use Livewire\WithPagination;

class ProductsList extends Component
{
    use WithPagination;
    public ProductForm $form;
    public function mount(): void
    {
        $this->form->categories = Category::pluck('name', 'id')->toArray();
        $this->form->countries = Country::pluck('name', 'id')->toArray();
    }
    public function render()
    {
        $products = Product::query()
            ->select(['products.*', 'countries.id as countryId', 'countries.name as countryName',])
            ->join('countries', 'countries.id', '=', 'products.country_id')
            ->with('categories');

        foreach ($this->form->searchColumns as $column => $value) {
            if (!empty($value)) {
                $products->when($column == 'price', function ($products) use ($value) {
                    if (is_numeric($value[0])) {
                        $products->where('products.price', '>=', $value[0] * 100);
                    }
                    if (is_numeric($value[1])) {
                        $products->where('products.price', '<=', $value[1] * 100);
                    }
                })
                    ->when($column == 'category_id', fn ($products) => $products->whereRelation('categories', 'id', $value))
                    ->when($column == 'country_id', fn ($products) => $products->whereRelation('country', 'id', $value))
                    ->when($column == 'name', fn ($products) => $products->where('products.' . $column, 'LIKE', '%' . $value . '%'));
            }
        }
        $products->orderBy($this->form->sortColumn, $this->form->sortDirection);
        return view('livewire.products-list', [
            'products' => $products->paginate(10),
        ]);
    }
    public function sortByColumn(string $column): void
    {
        if ($this->sortColumn == $column) {
            $this->form->sortDirection = $this->sortDirection == 'asc' ? 'desc' : 'asc';
        } else {
            $this->reset('sortDirection');
            $this->form->sortColumn = $column;
        }
    }
}
