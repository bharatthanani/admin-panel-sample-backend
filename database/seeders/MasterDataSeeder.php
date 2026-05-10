<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {

     DB::table('categories')->truncate();
     DB::table('tags')->truncate();
     DB::table('colors')->truncate();

        // ======================
        // CATEGORIES
        // ======================
        $categories = [
            ['category_name' => 'Mobile', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Airphone', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Laptop', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Tablet', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Smart Watch', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Camera', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Headphones', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Speakers', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Gaming Console', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Accessories', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Clothes', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Water', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('categories')->insert($categories);

        // ======================
        // TAGS
        // ======================
        $tags = [
            ['tag_name' => 'Clothing', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tag_name' => 'Accessories', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tag_name' => 'Trendy', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tag_name' => 'Gadgets', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tag_name' => 'Smartphones', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tag_name' => 'Laptops', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tag_name' => 'Furniture', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tag_name' => 'Decor', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tag_name' => 'Kitchen', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tag_name' => 'Organic', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tag_name' => 'Healthy', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tag_name' => 'Vegan', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('tags')->insert($tags);


         DB::table('colors')->insert([
            ['color_name' => 'Red', 'color_code' => '#FF0000', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['color_name' => 'Blue', 'color_code' => '#0000FF', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['color_name' => 'Green', 'color_code' => '#00FF00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['color_name' => 'Yellow', 'color_code' => '#FFFF00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['color_name' => 'Black', 'color_code' => '#000000', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}