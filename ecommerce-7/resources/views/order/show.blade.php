@extends('template.layouts')
@section('title', 'Order Details')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 pt-2">
            <h1>Order Details</h1>
            <p>Order Number: <b>{{ $order->order_number }}</b></p>
            <p>Order Date: <b>{{ $order->created_at->format('d M Y H:i') }}</b></p>
            <p>Shipping Address: <b>{{ $order->shipping_address }}</b></p>
        </div>
        <div class="col-12">
            <h3>Order Items</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>Rp{{ number_format($item->product->price, 0, ',', '.') }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp{{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    {{-- total --}}
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                        <td><strong>Rp{{ number_format($order->items->sum(fn($item) => $item->price * $item->quantity), 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
            @php
                $text = "Hello Admin, I have a question about my order " . $order->order_number;
                $waLink = "https://wa.me/6281234567890?text=" . urlencode($text);    
            @endphp
            {{-- Whatsapp button for confirm to admin --}}
            <a href="{{ $waLink }}" class="btn btn-success">Contact Admin via WhatsApp</a>
        </div>
    </div>
</div>
@endsection