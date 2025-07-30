<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       $title = fake()->unique()->name();
        $slug = Str::slug($title);
        $subCategories =[119,128];
        $subCatRandKey=array_rand($subCategories);
        $brands =[7,14];
        $brandRandKey=array_rand($brands);
        return [
            'title' =>$title,
            'slug' =>$slug,
            'category_id' =>20,
            'sub_Category_id' => $subCategories[$subCatRandKey],
            'brand_id' => $brands[$brandRandKey],
            'price' => rand(10,1000),
            'sku'=> rand(1000,10000),
            'track_qty' =>'Yes',
            'price'=> 10,
            'is_featured'=> 'Yes',
            'status'=>1,
        ];
    }
}