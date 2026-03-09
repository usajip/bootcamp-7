<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ProductCategory::query()->get()->keyBy('name');

        if ($categories->isEmpty()) {
            return;
        }

        $productsByCategory = [
            'Elektronik' => [
                ['name' => 'Smartphone Android 5G', 'price' => 3299000, 'stock' => 40],
                ['name' => 'Laptop Ultrabook 14 Inch', 'price' => 9899000, 'stock' => 15],
                ['name' => 'Tablet Belajar 10 Inch', 'price' => 2599000, 'stock' => 25],
                ['name' => 'Smartwatch Fitness Pro', 'price' => 1499000, 'stock' => 35],
                ['name' => 'TWS Noise Cancelling', 'price' => 799000, 'stock' => 70],
                ['name' => 'Keyboard Mechanical RGB', 'price' => 649000, 'stock' => 45],
                ['name' => 'Mouse Wireless Ergonomis', 'price' => 289000, 'stock' => 80],
                ['name' => 'Monitor IPS 24 Inch', 'price' => 1899000, 'stock' => 20],
                ['name' => 'Router WiFi Dual Band', 'price' => 459000, 'stock' => 38],
                ['name' => 'Power Bank 20000mAh', 'price' => 379000, 'stock' => 60],
            ],
            'Fashion' => [
                ['name' => 'Kaos Cotton Pria Basic', 'price' => 119000, 'stock' => 120],
                ['name' => 'Kemeja Flanel Pria', 'price' => 189000, 'stock' => 85],
                ['name' => 'Hoodie Unisex Fleece', 'price' => 249000, 'stock' => 75],
                ['name' => 'Celana Chino Slim Fit', 'price' => 219000, 'stock' => 68],
                ['name' => 'Jaket Denim Wanita', 'price' => 289000, 'stock' => 42],
                ['name' => 'Dress Midi Floral', 'price' => 279000, 'stock' => 36],
                ['name' => 'Hijab Voal Premium', 'price' => 79000, 'stock' => 110],
                ['name' => 'Sepatu Sneakers Casual', 'price' => 359000, 'stock' => 50],
                ['name' => 'Sandal Slide Minimalis', 'price' => 99000, 'stock' => 95],
                ['name' => 'Tas Tote Canvas', 'price' => 139000, 'stock' => 77],
            ],
            'Makanan & Minuman' => [
                ['name' => 'Kopi Arabica Gayo 250gr', 'price' => 89000, 'stock' => 130],
                ['name' => 'Teh Hijau Melati 100gr', 'price' => 49000, 'stock' => 115],
                ['name' => 'Granola Madu Kacang 500gr', 'price' => 75000, 'stock' => 90],
                ['name' => 'Cokelat Dark 70 Persen', 'price' => 39000, 'stock' => 140],
                ['name' => 'Madu Hutan Asli 500ml', 'price' => 129000, 'stock' => 55],
                ['name' => 'Sambal Bawang Botol 250ml', 'price' => 32000, 'stock' => 150],
                ['name' => 'Keripik Pisang Cokelat 200gr', 'price' => 28000, 'stock' => 170],
                ['name' => 'Beras Premium 5kg', 'price' => 79000, 'stock' => 70],
                ['name' => 'Susu Oat Barista 1L', 'price' => 42000, 'stock' => 88],
                ['name' => 'Jus Buah Mix 300ml', 'price' => 18000, 'stock' => 160],
            ],
            'Kesehatan' => [
                ['name' => 'Multivitamin Harian 30 Tablet', 'price' => 89000, 'stock' => 95],
                ['name' => 'Vitamin C 1000mg 60 Tablet', 'price' => 109000, 'stock' => 84],
                ['name' => 'Masker Medis 50 Pcs', 'price' => 45000, 'stock' => 140],
                ['name' => 'Hand Sanitizer 500ml', 'price' => 35000, 'stock' => 125],
                ['name' => 'Termometer Digital', 'price' => 79000, 'stock' => 48],
                ['name' => 'Tensimeter Digital Lengan', 'price' => 329000, 'stock' => 22],
                ['name' => 'Minyak Kayu Putih 120ml', 'price' => 29000, 'stock' => 100],
                ['name' => 'Patch Pereda Nyeri 10 Lembar', 'price' => 26000, 'stock' => 112],
                ['name' => 'Sabun Antibakteri 250ml', 'price' => 24000, 'stock' => 135],
                ['name' => 'Suplemen Omega 3 60 Softgel', 'price' => 119000, 'stock' => 60],
            ],
            'Perlengkapan Rumah' => [
                ['name' => 'Set Sprei Microfiber Queen', 'price' => 259000, 'stock' => 34],
                ['name' => 'Bantal Memory Foam', 'price' => 179000, 'stock' => 52],
                ['name' => 'Keset Anti Slip', 'price' => 49000, 'stock' => 88],
                ['name' => 'Rak Susun Serbaguna 4 Tingkat', 'price' => 299000, 'stock' => 27],
                ['name' => 'Lampu LED Hemat Energi 12W', 'price' => 25000, 'stock' => 160],
                ['name' => 'Set Alat Makan Stainless 24 Pcs', 'price' => 219000, 'stock' => 31],
                ['name' => 'Panci Granite 24cm', 'price' => 199000, 'stock' => 40],
                ['name' => 'Botol Minum Tumbler 1L', 'price' => 69000, 'stock' => 102],
                ['name' => 'Kotak Penyimpanan Lipat', 'price' => 59000, 'stock' => 96],
                ['name' => 'Dispenser Sabun Otomatis', 'price' => 149000, 'stock' => 44],
            ],
        ];

        foreach ($productsByCategory as $categoryName => $products) {
            $category = $categories->get($categoryName);

            if (! $category) {
                continue;
            }

            foreach ($products as $product) {
                Product::create([
                    'name' => $product['name'],
                    'slug' => Str::slug($product['name']),
                    'description' => 'Produk '.$product['name'].' untuk kategori '.$categoryName.'.',
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'image' => 'products/'.Str::slug($product['name']).'.jpg',
                    'product_category_id' => $category->id,
                ]);
            }
        }
    }
}
