<x-app-layout :title="'Edit Product'">
	<x-slot name="header">
		<div class="flex items-center justify-between gap-4">
			<h2 class="font-semibold text-xl text-gray-800 leading-tight">
				{{ __('Edit Product') }}
			</h2>
		</div>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
				<div class="p-6 text-gray-900">
					<form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" id="formUpload" class="space-y-6">
						@csrf
						@method('PUT')

						<div>
							<x-input-label for="name" value="{{ __('Product Name') }}" />
							<x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $product->name)" required autofocus />
							<x-input-error :messages="$errors->get('name')" class="mt-2" />
						</div>

						<div>
							<x-input-label for="description" value="{{ __('Description') }}" />
							<textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>{{ old('description', $product->description) }}</textarea>
							<x-input-error :messages="$errors->get('description')" class="mt-2" />
						</div>

						<div class="space-y-3">
							<div>
								<x-input-label value="{{ __('Current Image') }}" />
								<img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}" class="mt-2 h-40 w-40 rounded-lg border border-gray-200 object-cover" />
							</div>

							<div>
								<x-input-label for="uploadImage" value="{{ __('Replace Product Image') }}" />
								<x-text-input type="file" id="uploadImage" accept="image/*" class="mt-1 block w-full" />
								<p class="mt-2 text-sm text-gray-500">Kosongkan jika tidak ingin mengganti gambar. Jika pilih gambar baru, crop 1:1 dan output otomatis menjadi 1280x1280 px.</p>
								<div id="croppieContainer" class="mt-3" style="width: 100%; max-width: 600px;"></div>
								<input type="hidden" name="image" id="imageResult">
								<div id="image-error" class="mb-4 text-sm text-red-500" style="display:none;"></div>
								<x-input-error :messages="$errors->get('image')" class="mt-2" />
							</div>
						</div>

						<div>
							<x-input-label for="price" value="{{ __('Price') }}" />
							<x-text-input id="price" name="price" type="number" step="1" min="0" class="mt-1 block w-full" :value="old('price', $product->price)" required />
							<x-input-error :messages="$errors->get('price')" class="mt-2" />
						</div>

						<div>
							<x-input-label for="stock" value="{{ __('Stock') }}" />
							<x-text-input id="stock" name="stock" type="number" min="0" class="mt-1 block w-full" :value="old('stock', $product->stock)" required />
							<x-input-error :messages="$errors->get('stock')" class="mt-2" />
						</div>

						<div>
							<x-input-label for="product_category_id" value="{{ __('Category') }}" />
							<select id="product_category_id" name="product_category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
								<option value="">{{ __('Select Category') }}</option>
								@foreach ($productCategories as $category)
									<option value="{{ $category->id }}" @selected(old('product_category_id', $product->product_category_id) == $category->id)>
										{{ $category->name }}
									</option>
								@endforeach
							</select>
							<x-input-error :messages="$errors->get('product_category_id')" class="mt-2" />
						</div>

						<div class="flex items-center gap-3">
							<x-primary-button type="submit">
								{{ __('Update Product') }}
							</x-primary-button>
							<a href="{{ route('products.index') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
								{{ __('Cancel') }}
							</a>
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
		<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<script>
			document.addEventListener('DOMContentLoaded', function () {
				const form = document.getElementById('formUpload');
				const uploadInput = document.getElementById('uploadImage');
				const croppieElement = document.getElementById('croppieContainer');
				const imageResultInput = document.getElementById('imageResult');
				const imageError = document.getElementById('image-error');

				if (!form || !uploadInput || !croppieElement || !imageResultInput || typeof Croppie === 'undefined') {
					return;
				}

				let hasNewImage = false;
				const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
				const maxSize = 2 * (1024 * 1024);
				const croppie = new Croppie(croppieElement, {
					viewport: {
						width: 320,
						height: 320,
						type: 'square'
					},
					boundary: {
						width: 320,
						height: 320
					},
					enableExif: true
				});

				uploadInput.addEventListener('change', function (e) {
					const file = e.target.files[0];
					imageError.style.display = 'none';
					imageError.textContent = '';

					if (!file) {
						hasNewImage = false;
						imageResultInput.value = '';
						return;
					}

					if (file.size > maxSize) {
						alert('Ukuran gambar maksimal 2MB');
						e.target.value = '';
						hasNewImage = false;
						return;
					}

					const extension = file.name.split('.').pop().toLowerCase();
					if (!allowedExtensions.includes(extension)) {
						alert('Format gambar harus JPG, JPEG, PNG, atau WEBP');
						e.target.value = '';
						hasNewImage = false;
						return;
					}

					hasNewImage = true;
					const reader = new FileReader();
					reader.onload = function (event) {
						croppie.bind({
							url: event.target.result
						});
					};
					reader.readAsDataURL(file);
				});

				form.addEventListener('submit', function (e) {
					if (!hasNewImage) {
						return;
					}

					e.preventDefault();

					croppie.result({
						type: 'base64',
						size: { width: 1280, height: 1280 },
						format: 'webp',
						quality: 90
					}).then(function (base64) {
						const sizeInBytes = (base64.length * 3) / 4;
						if (sizeInBytes > maxSize) {
							imageError.textContent = 'Hasil gambar melebihi 2MB, kurangi kualitas atau pilih gambar lain.';
							imageError.style.display = 'block';
							return;
						}

						imageResultInput.value = base64;
						form.submit();
					});
				});
			});
		</script>
	@endpush
</x-app-layout>
