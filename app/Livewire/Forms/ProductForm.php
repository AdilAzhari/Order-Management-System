<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProductForm extends Form
{
    public array $categories = [];
    public array $countries = [];
    public array $searchColumns = [
        'name' => '',
        'price' => ['', ''],
        'description' => '',
        'category_id' => 0,
        'country_id' => 0,
    ];
    #[Url()]
    public string $sortColumn = 'products.name';

}
