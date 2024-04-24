<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class category_productSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = \App\Models\Category::all();
        $products = \App\Models\Product::all();
        $categories->each(function ($category) use ($products) {
            $category->products()->attach($products->random(rand(1, 5))->pluck('id'));
        });
    }
}
