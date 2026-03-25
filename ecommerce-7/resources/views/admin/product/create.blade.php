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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-6">
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
                            <x-text-input id="image" name="image" type="file" class="mt-1 block w-full" required accept="image/*" />
                            <p class="mt-2 text-sm text-gray-500">Crop image 1:1. Output akan otomatis menjadi 800x800 px.</p>
                            <div id="image-crop-container" class="mt-3 hidden">
                                <div id="croppie-container"></div>
                            </div>
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
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
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
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');
            const imageInput = document.getElementById('image');
            const cropContainer = document.getElementById('image-crop-container');
            const croppieElement = document.getElementById('croppie-container');

            let croppieInstance = null;
            let cropReady = false;
            let isSubmittingWithCroppedImage = false;

            if (typeof Croppie !== 'undefined') {
                croppieInstance = new Croppie(croppieElement, {
                    viewport: { width: 250, height: 250, type: 'square' },
                    boundary: { width: 320, height: 320 },
                    enableExif: true,
                    minZoom: 0.1,
                    maxZoom: 2.0,
                });
            }

            imageInput.addEventListener('change', function () {
                const file = this.files[0];
                cropReady = false;

                if (!file) {
                    cropContainer.classList.add('hidden');
                    return;
                }

                const fileType = file.type;
                const validImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
                if (!validImageTypes.includes(fileType)) {
                    alert('Please select a valid image file (JPEG, PNG, GIF, WEBP, JPG).');
                    this.value = '';
                    cropContainer.classList.add('hidden');
                    return;
                }

                const maxSize = 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('The selected image is too large. Please select an image smaller than 2MB.');
                    this.value = '';
                    cropContainer.classList.add('hidden');
                    return;
                }

                if (!croppieInstance) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (event) {
                    croppieInstance.bind({ url: event.target.result })
                        .then(function () {
                            cropContainer.classList.remove('hidden');
                            cropReady = true;
                        })
                        .catch(function () {
                            alert('Failed to load image for cropping. Please try another image.');
                            imageInput.value = '';
                            cropContainer.classList.add('hidden');
                            cropReady = false;
                        });
                };

                reader.readAsDataURL(file);
            });

            form.addEventListener('submit', function (event) {
                const price = document.getElementById('price').value;
                const stock = document.getElementById('stock').value;
                const name = document.getElementById('name').value.trim();
                const description = document.getElementById('description').value.trim();

                if (!name) {
                    alert('Product name is required.');
                    event.preventDefault();
                    return;
                }

                if (!description) {
                    alert('Product description is required.');
                    event.preventDefault();
                    return;
                }

                if (price < 0) {
                    alert('Price must be a positive number.');
                    event.preventDefault();
                }

                if (stock < 0) {
                    alert('Stock must be a positive number.');
                    event.preventDefault();
                }

                if (event.defaultPrevented || isSubmittingWithCroppedImage || !imageInput.files.length) {
                    return;
                }

                if (!croppieInstance || !cropReady) {
                    alert('Please select and crop the product image first.');
                    event.preventDefault();
                    return;
                }

                event.preventDefault();

                croppieInstance.result({
                    type: 'blob',
                    size: { width: 800, height: 800 },
                    format: 'jpeg',
                    quality: 0.95,
                }).then(function (blob) {
                    const croppedImage = new File([blob], `product-${Date.now()}.jpg`, { type: 'image/jpeg' });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(croppedImage);
                    imageInput.files = dataTransfer.files;

                    isSubmittingWithCroppedImage = true;
                    form.submit();
                }).catch(function () {
                    alert('Failed to crop image. Please try again.');
                });
            });
        });
    </script>
    @endpush
</x-app-layout>