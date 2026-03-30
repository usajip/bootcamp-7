<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="{{ route('home') }}">My E-commerce</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="{{ url('/') }}">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('cart.index') }}">Keranjang</a>
        </li>
        @auth
          @if(Auth::user()->role === 'admin')
          <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
          </li>
          @endif
          <li class="nav-item">
            <a class="nav-link" href="{{ route('profile.edit') }}">Profile</a>
          </li>
          <li class="nav-item">
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
              @csrf
              <button type="submit" class="nav-link btn btn-link">Logout</button>
            </form>
          </li>
        @else
          <li class="nav-item">
            <a class="nav-link" href="{{ route('login') }}">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('register') }}">Register</a>
          </li>
        @endauth
      </ul>
      <form action="{{ route('home') }}" method="GET" class="d-flex" role="search">
        <input class="form-control me-2" type="search" name="search" value="{{ request('search') }}" placeholder="Search" aria-label="Search"/>
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>