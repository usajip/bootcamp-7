<x-app-layout :title="'Create Product'">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Products') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-[20px]">
            @include('layouts.success-error-msg')
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('products.store') }}" 
                    enctype="multipart/form-data" 
                    id="formUpload"
                    class="space-y-6">
                        @csrf
                        <div>
                            <x-input-label for="name" value="{{ __('Product Name') }}" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="description" value="{{ __('Description') }}" />
                            <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required></textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="image" value="{{ __('Product Image') }}" />
                            <x-text-input type="file" id="uploadImage" accept="image/*" class="mt-1 block w-full" required accept="image/*" />

                            <div id="croppieContainer" style="width: 100%; max-width: 600px;"></div>

                            <input type="hidden" name="image" id="imageResult">
                            <div id="image-error" class="text-red-500 text-sm mb-4" style="display:none;"></div>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="price" value="{{ __('Price') }}" />
                            <x-text-input id="price" name="price" type="number" step="1" min="0" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="stock" value="{{ __('Stock') }}" />
                            <x-text-input id="stock" name="stock" type="number" min="0" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="product_category_id" value="{{ __('Category') }}" />
                            <select id="product_category_id" name="product_category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">{{ __('Select Category') }}</option>
                                @foreach ($productCategories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('product_category_id')" class="mt-2" />
                        </div>
                        <div>
                            <x-primary-button type="submit">
                                {{ __('Create Product') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @endpush
    
    @push('scripts')
        {{-- Form Validation --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
            let croppie = new Croppie(document.getElementById('croppieContainer'), {
                viewport: {
                    width: 320,
                    height: 320, // 1:1
                    type: 'square'
                },
                boundary: {
                    width: 320,
                    height: 320
                },
                enableExif: true
            });

            const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            const maxSize = 2 * (1024 * 1024); // 2MB

            document.getElementById('uploadImage').addEventListener('change', function (e) {
                const file = e.target.files[0];

                if (!file) return;

                // Validasi ukuran
                if (file.size > maxSize) {
                    alert('Ukuran gambar maksimal 2MB');
                    e.target.value = '';
                    return;
                }

                // Validasi ekstensi
                const extension = file.name.split('.').pop().toLowerCase();
                if (!allowedExtensions.includes(extension)) {
                    alert('Format gambar harus JPG, JPEG, PNG, atau WEBP');
                    e.target.value = '';
                    return;
                }

                // Load ke croppie
                const reader = new FileReader();
                reader.onload = function (event) {
                    croppie.bind({
                        url: event.target.result
                    });
                };
                reader.readAsDataURL(file);
            });
        </script>
        <script>
            document.getElementById('formUpload').addEventListener('submit', function (e) {
                e.preventDefault();

                croppie.result({
                    type: 'base64',
                    size: { width: 1280, height: 1280 },
                    format: 'webp',
                    quality: 90
                }).then(function (base64) {

                    // Estimasi ukuran base64
                    const sizeInBytes = (base64.length * 3) / 4;
                    if (sizeInBytes > maxSize) {
                        alert('Hasil gambar melebihi 2MB, kurangi kualitas');
                        return;
                    }

                    document.getElementById('imageResult').value = base64;
                    e.target.submit();
                });
            });
        </script>
    @endpush
</x-app-layout>