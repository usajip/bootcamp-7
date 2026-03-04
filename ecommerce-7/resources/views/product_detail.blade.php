@extends('template.layouts')

@section('title', 'Product Detail Page')

@section('content')
@push('js')
<script>
    alert('Selamat datang di halaman detail product');
</script>
@endpush
    <h1>Product Detail Page</h1>
    <p>Ini adalah halaman detail product</p>
@push('css')
    <style>
        h1 {
            color: red;
        }
    </style>
@endpush
@endsection