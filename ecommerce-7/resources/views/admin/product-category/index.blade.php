<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Product Category') }}
            </h2>
            <a href="{{ route('product-categories.create') }}"
               class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Tambah kategori
            </a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table id="product-categories-table" class="display w-full">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Kategori</th>
                                    <th>Slug</th>
                                    <th>Jumlah Produk</th>
                                    <th>Jumlah Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productCategories as $productCategory)
                                    <tr>
                                        <td>{{ $productCategory->id }}</td>
                                        <td>{{ $productCategory->name }}</td>
                                        <td>{{ $productCategory->slug }}</td>
                                        <td>{{ $productCategory->products_count }}</td>
                                        <td>{{ $productCategory->total_stock }}</td>
                                        <td>
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('product-categories.edit', $productCategory) }}"
                                                   class="inline-flex items-center rounded-md bg-yellow-500 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-yellow-400">
                                                    Edit
                                                </a>

                                                <form action="{{ route('product-categories.destroy', $productCategory) }}" method="POST"
                                                      onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
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
                </div>
            </div>
        </div>
    </div>
    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    @endpush
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
        <script>
            $(function () {
                $('#product-categories-table').DataTable({
                    language: {
                        search: 'Cari:',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                        infoEmpty: 'Data tidak tersedia',
                        zeroRecords: 'Data tidak ditemukan',
                        paginate: {
                            first: 'Pertama',
                            last: 'Terakhir',
                            next: 'Berikutnya',
                            previous: 'Sebelumnya'
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
