<?php

namespace App\Livewire\Forms;

use App\Models\Category;
use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Illuminate\Support\Str;

class CategoriesList extends Form
{
    public ?Category $category = null;
    public string $name = '';
    public string $slug = '';
    public Collection $categories;
    public array $active = [];
    public int $currentPage = 1;
    public int $editedCategoryId = 0;
    public int $perPage = 10;
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3'],
            'slug' => ['nullable', 'string'],
        ];
    }
    public function updatedName(): void
    {
        $this->slug = Str::slug($this->name);
    }
}
