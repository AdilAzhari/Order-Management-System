<?php

namespace App\Livewire\Forms;

use App\Models\Category;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Illuminate\Support\Str;
class CategoriesList extends Form
{
    public ?Category $category = null;
    public string $name = '';
    public string $slug = '';
    public bool $showModal = false;
    public array $active = [];
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
