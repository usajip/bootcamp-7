<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Products') }}
            </h2>
            <a href="{{ route('products.create') }}"
               class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Tambah produk
            </a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-[20px]">
            @include('layouts.success-error-msg')
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <form method="GET" action="{{ route('products.index') }}" class="flex items-center gap-2">
                            <input type="text" name="search" placeholder="Cari produk..." value="{{ request('search') }}"
                                   class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <button type="submit"
                                    class="inline-flex items-center rounded-md bg-blue-600 px-3 py-3 text-xs font-semibold text-white transition hover:bg-blue-500">
                                Cari
                            </button>
                        </form>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-300 rounded-lg">
                            <thead>
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">ID</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Nama</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Slug</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Deskripsi</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Harga</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Stok</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Gambar</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Kategori</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                               @foreach($products as $product)
                                   <tr class="bg-white border-b hover:bg-gray-50">
                                       <td class="px-4 py-3 text-sm text-gray-700">{{ $product->id }}</td>
                                       <td class="px-4 py-3 text-sm text-gray-700">{{ $product->name }}</td>
                                       <td class="px-4 py-3 text-sm text-gray-700">{{ $product->slug }}</td>
                                       <td class="px-4 py-3 text-sm text-gray-700">{{ $product->description }}</td>
                                       <td class="px-4 py-3 text-sm text-gray-700">{{ "Rp" . number_format($product->price, 0, ",", ".") }}</td>
                                       <td class="px-4 py-3 text-sm text-gray-700">{{ $product->stock }}</td>
                                       <td class="px-4 py-3">
                                            <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded border border-gray-200">
                                        </td>
                                       <td class="px-4 py-3 text-sm text-gray-700">{{ $product->product_category ? $product->product_category->name : 'Tidak ada kategori' }}</td>
                                       <td class="px-4 py-3">
                                           <div class="flex items-center gap-2">
                                                <a href="{{ route('product.detail', $product->slug) }}"
                                                   class="inline-flex items-center rounded-md bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-blue-500">
                                                    Lihat
                                                </a>
                                               <a href="{{ route('products.edit', $product) }}"
                                                  class="inline-flex items-center rounded-md bg-yellow-500 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-yellow-400">
                                                   Edit
                                               </a>

                                               <form action="{{ route('products.destroy', $product) }}" method="POST"
                                                     onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                                   @csrf
                                                   @method('DELETE')
                                                   <button type="submit"
                                                           class="inline-flex items-center rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-red-500">
                                                       Hapus
                                                   </button>
                                               </form>
                                           </div>
                                       </td>
                                   </tr>
                               @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
