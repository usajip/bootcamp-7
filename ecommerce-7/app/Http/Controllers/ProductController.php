<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::with('product_category')
                        ->orderBy('id', 'desc');
        
        // if(
        //     $request->has('price_order') 
        //     && in_array($request->price_order, ['asc', 'desc'])
        // ) {
        //     $products->orderBy('price', $request->price_order);
        // }
        if ($request->has('search')) {
            $products->where('name', 'like', '%' . $request->search . '%');
        }
        $products = $products->paginate(10);
        return view('admin.product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productCategories = ProductCategory::all();
        return view('admin.product.create', compact('productCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'required',
            'product_category_id' => 'required|exists:product_categories,id',
        ]);

        if ($request->image) {
            $imageData = $request->image;
            list($type, $imageData) = explode(';', $imageData);
            list(, $imageData) = explode(',', $imageData);
            $imageData = base64_decode($imageData);
            $imageName = 'products/' . uniqid() . '.webp';
            Storage::disk('images')->put($imageName, $imageData);
        }

        $slug = strtolower(str_replace(' ', '-', $request->name)).'-'.uniqid();

        Product::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imageName,
            'product_category_id' => $request->product_category_id,
        ]);

        return redirect()
                ->route('products.index')
                ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::with('product_category')->findOrFail($id);
        return view('admin.product.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $productCategories = ProductCategory::all();
        return view('admin.product.edit', compact('product', 'productCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
