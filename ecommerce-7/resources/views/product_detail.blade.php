@extends('template.layouts')
@section('title', $product->name)
@section('content')
	<div class="container py-4">
		<div class="row g-4">
            <div class="col-md-6">
                <img src="{{ asset('images/'.$product->image) }}" alt="{{ $product->name }}" class="img-fluid">
            </div>
            <div class="col-md-6">
                <h1>{{ $product->name }}</h1>
                <p class="text-muted">Category: {{ $product->product_category->name }}</p>
                <h3>Rp{{ number_format($product->price, 0, ',', '.') }}</h3>
                <p>{{ $product->description }}</p>
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('cart.store') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button type="submit" class="btn btn-success">Add to Cart</button>
                </form>
            </div>
            <div class="col-12">
                <h4>Recommended Products</h4>
                <div class="row g-4">
                    @foreach ($product_recommendations as $recommendation)
                        <div class="col-md-3 d-flex justify-content-center">
                            <x-product-card
                                :name="$recommendation->name"
                                :price="$recommendation->price"
                                :image="$recommendation->image"
                                :slug="$recommendation->slug"
                            />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection