<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahProduk = Product::count();
        $jumlahCategori = ProductCategory::count();
        $jumlahOrder = Order::count();
        $jumlahStok = Product::sum('stock');
        $jumlahKlikProduct = 200;
        $data = [
            ['label' => 'Jumlah produk', 'value' => $jumlahProduk, 'color' => '#4CAF50', 'icon' => 'box'],
            ['label' => 'Jumlah categori', 'value' => $jumlahCategori, 'color' => '#2196F3', 'icon' => 'category'],
            ['label' => 'Jumlah order', 'value' => $jumlahOrder, 'color' => '#FF9800', 'icon' => 'shopping_cart'],
            ['label' => 'Jumlah klik product', 'value' => $jumlahKlikProduct, 'color' => '#F44336', 'icon' => 'touch_app'],
            ['label' => 'Jumlah stok', 'value' => $jumlahStok, 'color' => '#9C27B0', 'icon' => 'inventory'],
        ];
        $salesData = $this->orderData();
        $latestOrders = Order::latest()->take(5)->get();

        return view('dashboard', compact('data', 'salesData', 'latestOrders'));
    }

    // Array Data dummy untuk grafik penjualan 7 hari (jumlah order dan total pendapatan)
    public static function orderData()
    {
        return [
            'labels' => [
                Carbon::now()->subDays(6)->format('Y-m-d'),
                Carbon::now()->subDays(5)->format('Y-m-d'),
                Carbon::now()->subDays(4)->format('Y-m-d'),
                Carbon::now()->subDays(3)->format('Y-m-d'),
                Carbon::now()->subDays(2)->format('Y-m-d'),
                Carbon::now()->subDays(1)->format('Y-m-d'),
                Carbon::now()->format('Y-m-d'),
            ],
            'orders' => [12, 19, 8, 25, 18, 22, 30],
            'revenue' => [2400, 3800, 1600, 5000, 3600, 4400, 6000]
        ];
    }
}
