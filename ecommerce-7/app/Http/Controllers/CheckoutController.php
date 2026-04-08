<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        // Logic for processing the checkout and creating a transaction
        if(Auth::check()) {
            $user = Auth::user();
            $cartItems = Cart::where('user_id', $user->id)
                            ->with('product')
                            ->get();
            return view('checkout', compact('cartItems'));
        } else {
            return redirect()->route('login')->with('error', 'Please login to proceed to checkout');
        }
    }
}
