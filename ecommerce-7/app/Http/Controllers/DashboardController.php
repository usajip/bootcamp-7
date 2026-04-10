<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahProduk = Product::count();
        $jumlahCategori = ProductCategory::count();
        $jumlahOrder = Order::count();
        $jumlahStok = Product::sum('stock');
        $jumlahKlikProduct = Product::sum('clicks');
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
        $date_labels = [
            Carbon::now()->subDays(7)->format('Y-m-d'),
            Carbon::now()->subDays(6)->format('Y-m-d'),
            Carbon::now()->subDays(5)->format('Y-m-d'),
            Carbon::now()->subDays(4)->format('Y-m-d'),
            Carbon::now()->subDays(3)->format('Y-m-d'),
            Carbon::now()->subDays(2)->format('Y-m-d'),
            Carbon::now()->subDays(1)->format('Y-m-d'),
            Carbon::now()->format('Y-m-d'),
        ];
        // grab data order dari database, lalu group by tanggal dan hitung jumlah order serta total pendapatan
        $orders_data = Order::selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        
        $total_orders = [];
        $total_revenue = [];
        foreach ($date_labels as $date) {
            $order = $orders_data->firstWhere('date', $date);
            $total_orders[] = $order ? (int) $order->orders : 0;
            $total_revenue[] = $order ? (int) $order->revenue : 0;
        }

        return [
            'labels' => $date_labels,
            'orders' => $total_orders,
            'revenue' => $total_revenue,
        ];
    }
}
