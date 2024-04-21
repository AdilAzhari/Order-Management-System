<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Category 1', 'slug' => 'category-1', 'is_active' => true, 'position' => 1],
            ['name' => 'Category 2', 'slug' => 'category-2', 'is_active' => true, 'position' => 2],
            ['name' => 'Category 3', 'slug' => 'category-3', 'is_active' => true, 'position' => 3],
            ['name' => 'Category 4', 'slug' => 'category-4', 'is_active' => true, 'position' => 4],
            ['name' => 'Category 5', 'slug' => 'category-5', 'is_active' => true, 'position' => 5],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }

    }
}
