<div class="card" style="width: 18rem;">
  <img src="{{ asset('images/' . $image) }}" class="card-img-top" alt="{{ $name }}">
  <div class="card-body">
    <h5 class="card-title">{{ $name }}</h5>
    <p class="card-text">Rp{{ number_format($price, 0, ',', '.') }}</p>
    <a href="{{ route('product.detail', ['slug' => $slug]) }}" class="btn btn-primary">Lihat Detail</a>
  </div>
</div>