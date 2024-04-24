<?php

namespace App\Livewire;

use App\Exports\ProductsExport;
use App\Livewire\Forms\ProductForm;
use App\Models\{Category, Country, Product};
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Response as HttpResponse;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductsList extends Component
{
    use WithPagination;
    public ProductForm $form;
    #[Url()]
    public string $sortDirection = 'asc';
    public array $selected = [];

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
        $products->orderBy($this->form->sortColumn, $this->sortDirection);
        return view('livewire.products-list', [
            'products' => $products->paginate(10),
        ]);
    }
    public function sortByColumn(string $column): void
    {
        if ($this->form->sortColumn == $column) {
            $this->sortDirection = $this->sortDirection == 'asc' ? 'desc' : 'asc';
        } else {
            $this->reset('form.sortDirection');
            $this->form->sortColumn = $column;
        }
    }
    public function deleteConfirm(string $method, $id = null): void
    {
        $this->dispatch('swal:confirm', [
            'type'  => 'warning',
            'title' => 'Are you sure?',
            'text'  => '',
            'id'    => $id,
            'method' => $method,
        ]);
    }

    #[On('delete')]
    public function delete(int $id): void
    {
        $product = Product::findOrFail($id);

        $product->delete();
    }

    public function getSelectedCountProperty(): int
    {
        return count($this->selected);
    }

    #[On('deleteSelected')]
    public function deleteSelected(): void
    {
        $products = Product::whereIn('id', $this->selected)->get();

        $products->each->delete();

        $this->reset('form.selected');
    }
    public function export(string $format): BinaryFileResponse
    {
        abort_if(!in_array($format, ['csv', 'xlsx', 'pdf']), HttpResponse::HTTP_NOT_FOUND);

        return Excel::download(new ProductsExport($this->selected), 'products.' . $format);
    }
}
