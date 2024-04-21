<?php

namespace App\Livewire;

use App\Livewire\Forms\CategoriesList as FormsCategoriesList;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class CategoriesList extends Component
{
    public FormsCategoriesList $form;
    use WithPagination;
    public function openModal(): void
    {
        $this->form->showModal = true;
    }
    public function render()
    {
        $categories = Category::latest()->paginate(5);
        $this->form->active = $categories->mapWithKeys(
            fn (Category $item) => [$item['id'] => (bool) $item['is_active']]
        )->toArray();
        return view('livewire.categories-list', compact('categories'));
    }
    public function save()
    {
        $this->form->validate();

        Category::create($this->form->only('name', 'slug'));

        $this->form->reset('showModal');
    }
    public function toggleIsActive(int $categoryId): void
    {
        Category::where('id', $categoryId)->update([
            'is_active' => $this->form->active[$categoryId],
        ]);
    }
}
