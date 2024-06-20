<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'Affogato',
                'price' => 25000,
                'category' => 'Coffee',
                'img' => 'product-img/affogato.jpg',
                'description' => 'Deskripsi produk A.'
            ],
            [
                'name' => 'Americano',
                'price' => 23000,
                'category' => 'Coffee',
                'img' => 'product-img/americano.jpg',
                'description' => 'Deskripsi produk B.'
            ],
            [
                'name' => 'Cappuccino',
                'price' => 25000,
                'category' => 'Coffee',
                'img' => 'product-img/cappuccino.jpg',
                'description' => 'Deskripsi produk C.'
            ],
            [
                'name' => 'Cortado',
                'price' => 29000,
                'category' => 'Coffee',
                'img' => 'product-img/cortado.jpg',
                'description' => 'Deskripsi produk D.'
            ],
            [
                'name' => 'Espresso',
                'price' => 20500,
                'category' => 'Coffee',
                'img' => 'product-img/espresso.jpg',
                'description' => 'Deskripsi produk E.'
            ],
            [
                'name' => 'Caffe Latte',
                'price' => 27000,
                'category' => 'Coffee',
                'img' => 'product-img/latte.jpg',
                'description' => 'Deskripsi produk F.'
            ],
            [
                'name' => 'Macchiato',
                'price' => 30000,
                'category' => 'Coffee',
                'img' => 'product-img/macchiato.jpg',
                'description' => 'Deskripsi produk F.'
            ],
            [
                'name' => 'Green Tea',
                'price' => 29000,
                'category' => 'Non Coffee',
                'img' => 'product-img/greentea.png',
                'description' => 'Deskripsi produk G.'
            ],
            [
                'name' => 'Banana Smoothies',
                'price' => 28000,
                'category' => 'Non Coffee',
                'img' => 'product-img/banana.jpg',
                'description' => 'Deskripsi produk G.'
            ],
            [
                'name' => 'Chocolate Smoothies',
                'price' => 28000,
                'category' => 'Non Coffee',
                'img' => 'product-img/chocolate.jpg',
                'description' => 'Deskripsi produk G.'
            ],
        ]);
    }
}
