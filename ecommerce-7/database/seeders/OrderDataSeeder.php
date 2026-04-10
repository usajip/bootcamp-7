<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class OrderDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::query()->pluck('id');

        if ($userIds->isEmpty()) {
            $userIds = User::factory()->count(10)->create()->pluck('id');
        }

        $products = Product::query()->get();

        if ($products->isEmpty()) {
            $this->command?->warn('Seeder order dilewati karena belum ada data produk.');

            return;
        }

        $statuses = ['pending', 'processing', 'completed', 'cancelled'];

        for ($index = 1; $index <= 50; $index++) {
            $orderedAt = Carbon::instance(fake()->dateTimeBetween('-7 days', 'now'));
            $selectedProducts = $products->shuffle()->take(random_int(1, min(4, $products->count())));

            $order = new Order;
            $order->order_number = 'ORD-'.$orderedAt->format('Ymd').'-'.strtoupper(Str::random(6));
            $order->status = fake()->randomElement($statuses);
            $order->shipping_address = fake()->address();
            $order->total_amount = 0;
            $order->user_id = $userIds->random();
            $order->created_at = $orderedAt;
            $order->updated_at = $orderedAt;
            $order->save();

            $totalAmount = 0;

            foreach ($selectedProducts as $product) {
                $quantity = random_int(1, 3);
                $price = (int) $product->price;

                $orderItem = new OrderItem;
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $product->id;
                $orderItem->quantity = $quantity;
                $orderItem->price = $price;
                $orderItem->created_at = $orderedAt;
                $orderItem->updated_at = $orderedAt;
                $orderItem->save();

                $totalAmount += $price * $quantity;
            }

            $order->total_amount = $totalAmount;
            $order->updated_at = $orderedAt;
            $order->save();
        }
    }
}
