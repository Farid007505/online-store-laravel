<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class categoryFacotyFactory extends Factory
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
        $subCategories =[5,6];
        $subCatRandKey=array_rand($subCategories);
        $brands =[1,9];
        $brandRandKey=array_rand($brands);
        return [
            'title' =>$title,
            'slug' =>$slug,
            'category_id' =>28,
            'subCategories' => $subCategories[$subCatRandKey],
            'brands' => $brands[$brandRandKey],
            'price' => rand(10,1000),
            'sku'=> rand(1000,10000),
            'track_qty' =>'Yes',
            'price'=> 10,
            'is_featured'=> 'Yes',
            'status'=>1,
        ];
    }
}