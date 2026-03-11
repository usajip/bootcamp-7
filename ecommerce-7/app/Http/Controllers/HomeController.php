<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $products   = Product::with('product_category')
                        ->when($search, function ($query, $search) {
                            return $query->where('name', 'like', "%{$search}%");
                        })
                        ->orderBy('price', 'asc')
                        ->paginate(6);
        
        return view('home', compact('products'));
    }

    public function productDetails(string $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        $product_recommendations = Product::where('product_category_id', $product->product_category_id)
                                        ->where('id', '!=', $product->id)
                                        ->inRandomOrder()
                                        ->take(4)
                                        ->get();
        return view('product_detail', compact('product', 'product_recommendations'));
    }
}
