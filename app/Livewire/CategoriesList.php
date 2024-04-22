<?php

namespace App\Livewire;

use App\Livewire\Forms\CategoriesList as FormsCategoriesList;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class CategoriesList extends Component
{
    public FormsCategoriesList $form;
    public bool $showModal = false;
    public int $editedCategoryId = 0;

    use WithPagination;
    public function openModal(): void
    {
        $this->form->showModal = true;
    }
    public function render()
    {
        $this->form->categories = Category::paginate(10)->getCollection();

        $cats = Category::orderBy('position')->paginate($this->form->perPage);
        $links = $cats->links();
        $this->form->currentPage = $cats->currentPage();
        $this->form->categories = collect($cats->items());

        $this->form->active = $this->form->categories->mapWithKeys(
            fn (Category $item) => [$item['id'] => (bool) $item['is_active']]
        )->toArray();

        return view('livewire.categories-list', ['links' => $links]);
    }
    public function save()
    {
        $this->form->validate();

        if (is_null($this->form->category)) {
            $position = Category::max('position') + 1;
            Category::create(array_merge($this->form->only('name', 'slug'), ['position' => $position]));
        } else {
            $this->form->category->update($this->form->only('name', 'slug'));
        }

        $this->reset('showModal');
        $this->form->resetValidation();
        $this->reset('form.showModal', 'form.editedCategoryId');
    }
    public function cancelCategoryEdit()
    {
        $this->form->resetValidation();
        $this->form->reset('form.editedCategoryId');
    }
    public function toggleIsActive(int $categoryId): void
    {
        Category::where('id', $categoryId)->update([
            'is_active' => $this->form->active[$categoryId],
        ]);
    }

    public function updateOrder($list)
    {
        foreach ($list as $item) {
            $cat = $this->form->categories->firstWhere('id', $item['value']);
            $order = $item['order'] + (($this->form->currentPage - 1) * $this->form->perPage);

            if ($cat['position'] != $order) {
                Category::where('id', $item['value'])->update(['position' => $order]);
            }
        }
    }
    public function editCategory(int $categoryId): void
    {
        $this->editedCategoryId = $categoryId;

        $this->form->category = Category::find($categoryId);
        $this->form->name = $this->form->category->name;
        $this->form->slug = $this->form->category->slug;
    }
}
