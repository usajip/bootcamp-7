  // Data Produk (Array JS)
  const products = [
    {
      nama: "Laptop ASUS ROG",
      deskripsi: "Laptop gaming performa tinggi",
      harga: 25000000,
      kategori: "Elektronik",
      gambar: "https://via.placeholder.com/300x200?text=Laptop"
    },
    {
      nama: "iPhone 15",
      deskripsi: "Smartphone flagship terbaru",
      harga: 18000000,
      kategori: "Elektronik",
      gambar: "https://via.placeholder.com/300x200?text=iPhone"
    },
    {
      nama: "Jaket Hoodie",
      deskripsi: "Hoodie nyaman untuk harian",
      harga: 350000,
      kategori: "Fashion",
      gambar: "https://via.placeholder.com/300x200?text=Hoodie"
    },
    {
      nama: "Sepatu Sneakers",
      deskripsi: "Sepatu stylish dan nyaman",
      harga: 750000,
      kategori: "Fashion",
      gambar: "https://via.placeholder.com/300x200?text=Sneakers"
    },
    {
      nama: "Coklat Premium",
      deskripsi: "Coklat impor kualitas terbaik",
      harga: 120000,
      kategori: "Makanan",
      gambar: "https://via.placeholder.com/300x200?text=Coklat"
    },
    {
      nama: "Kopi Arabica",
      deskripsi: "Kopi arabica asli Indonesia",
      harga: 90000,
      kategori: "Makanan",
      gambar: "https://via.placeholder.com/300x200?text=Kopi"
    }
  ];

  const productList = document.getElementById("productList");
  const searchInput = document.getElementById("searchInput");
  const filterKategori = document.getElementById("filterKategori");
  const sortHarga = document.getElementById("sortHarga");

  function tampilkanProduk(data) {
    productList.innerHTML = "";

    if (data.length === 0) {
      productList.innerHTML = `<p class="text-center">Produk tidak ditemukan</p>`;
      return;
    }

    data.forEach(produk => {
      productList.innerHTML += `
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow-sm">
            <img src="${produk.gambar}" class="card-img-top" alt="${produk.nama}">
            <div class="card-body">
              <h5 class="card-title">${produk.nama}</h5>
              <p class="card-text">${produk.deskripsi}</p>
              <p class="fw-bold text-primary">Rp ${produk.harga.toLocaleString()}</p>
              <span class="badge bg-secondary">${produk.kategori}</span>
            </div>
          </div>
        </div>
      `;
    });
  }

  function filterDanSort() {
    let hasil = [...products];

    // Filter kategori
    if (filterKategori.value) {
      hasil = hasil.filter(p => p.kategori === filterKategori.value);
    }

    // Search nama
    if (searchInput.value) {
      hasil = hasil.filter(p =>
        p.nama.toLowerCase().includes(searchInput.value.toLowerCase())
      );
    }

    // Sort harga
    if (sortHarga.value === "tinggi") {
      hasil.sort((a, b) => b.harga - a.harga);
    } else if (sortHarga.value === "rendah") {
      hasil.sort((a, b) => a.harga - b.harga);
    }

    tampilkanProduk(hasil);
  }

  // Event
  searchInput.addEventListener("input", filterDanSort);
  filterKategori.addEventListener("change", filterDanSort);
  sortHarga.addEventListener("change", filterDanSort);

  // Load awal
  tampilkanProduk(products);