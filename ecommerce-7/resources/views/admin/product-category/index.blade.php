<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Product Category') }}
            </h2>
            <x-primary-button
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'create-new-category')"
            >{{ __('Tambah Kategori') }}</x-primary-button>
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
                                                <x-primary-button
                                                    x-data=""
                                                    x-on:click.prevent="$dispatch('open-modal', 'edit-category.{{ $productCategory->id }}')"
                                                    class=""
                                                >
                                                    {{ __('Edit') }}
                                                </x-primary-button>
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
                                    @push('scripts')
                                    <x-modal name="edit-category.{{ $productCategory->id }}" maxWidth="md" focusable>
                                        <form method="POST" action="{{ route('product-categories.update', $productCategory) }}" class="p-4">
                                            @csrf
                                            @method('PUT')
                                            <h2 class="text-lg font-medium text-gray-900">
                                                Edit Kategori
                                            </h2>

                                            <div class="mt-4">
                                                <x-input-label for="name" value="{{ __('Nama Kategori') }}" />
                                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $productCategory->name) }}" required />
                                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                            </div>
                                            <div class="mt-4">
                                                <x-input-label for="slug" value="{{ __('Slug Kategori') }}" />
                                                <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" value="{{ old('slug', $productCategory->slug) }}" required />
                                                <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                                            </div>
                                            <div class="mt-6 flex justify-end">
                                                <x-secondary-button x-on:click="$dispatch('close')">
                                                    {{ __('Batal') }}
                                                </x-secondary-button>
                                                <x-primary-button class="ms-3" type="submit">
                                                    {{ __('Simpan') }}
                                                </x-primary-button>
                                            </div>
                                        </form>
                                    </x-modal>
                                    @endpush
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
        <x-modal name="create-new-category" maxWidth="md" focusable>
            <form method="POST" action="{{ route('product-categories.store') }}" class="p-6">
                @csrf
                <h2 class="text-lg font-medium text-gray-900">
                    Tambah Kategori Baru
                </h2>

                <div class="mt-4">
                    <x-input-label for="name" value="{{ __('Nama Kategori') }}" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name') }}" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div class="mt-4">
                    <x-input-label for="slug" value="{{ __('Slug Kategori') }}" />
                    <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" value="{{ old('slug') }}" required />
                    <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                </div>
                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Batal') }}
                    </x-secondary-button>

                    <x-primary-button class="ms-3" type="submit">
                        {{ __('Simpan') }}
                    </x-primary-button>
                </div>
            </form>
        </x-modal>
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
