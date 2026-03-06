<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $products = [
            [
                'name' => 'Product 1',
                'price' => 1000000,
                'image' => 'https://eagle.co.id/wp-content/uploads/2024/06/JERSEY-GRADIENT-B-MERAH-PUTIH-1-Website-1.jpg'
            ],
            [
                'name' => 'Product 2',
                'price' => 2000000,
                'image' => 'https://eagle.co.id/wp-content/uploads/2024/06/JERSEY-GRADIENT-B-MERAH-PUTIH-1-Website-1.jpg'
            ],
            [
                'name' => 'Product 3',
                'price' => 3000000,
                'image' => 'https://eagle.co.id/wp-content/uploads/2024/06/JERSEY-GRADIENT-B-MERAH-PUTIH-1-Website-1.jpg'
            ]
        ];

        $accordionItems = [
            [
                'title' => 'Accordion Item 1',
                'body' => 'Content for accordion item 1'
            ],
            [
                'title' => 'Accordion Item 2',
                'body' => 'Content for accordion item 2'
            ],
            [
                'title' => 'Accordion Item 3',
                'body' => 'Content for accordion item 3'
            ]
        ];
        return view('home', compact('products', 'accordionItems'));
        // return view('home', [
        //     'products' => $products,
        //     'accordionItems' => $accordionItems
        // ]);
    }

    public function productDetails()
    {
        return view('product_detail');
    }
}
