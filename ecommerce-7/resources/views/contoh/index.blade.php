<h1>Hello {{ $name }} </h1>
<p>Ini adalah halaman contoh</p>
<p>Waktu sekarang: {{ $date }}</p>

{!! $html !!}

@if($angka > 5)
    <p>Angka lebih besar dari 5</p>
@else
    <p>Angka lebih kecil atau sama dengan 5</p>
@endif

<p>{{ $fruits[0] }}</p>
<ol type="A">
    @foreach($fruits as $fruit)
        <li>{{ $fruit }}</li>
    @endforeach
</ol>