<div class="card" style="width: 18rem;">
  <img src="{{ $image }}" class="card-img-top" alt="{{ $name }}">
  <div class="card-body">
    <h5 class="card-title">{{ $name }}</h5>
    <p class="card-text">Rp{{ number_format($price, 0, ',', '.') }}</p>
    <a href="#" class="btn btn-primary">Go somewhere</a>
  </div>
</div>