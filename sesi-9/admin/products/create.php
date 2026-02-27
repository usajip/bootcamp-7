<?php
require_once '../../koneksi.php';
require_once __DIR__ . '/../../components/template.php';

$errors = [];
$successMessage = '';

$name = '';
$description = '';
$price = '';
$category = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$name = isset($_POST['name']) ? trim($_POST['name']) : '';
	$description = isset($_POST['description']) ? trim($_POST['description']) : '';
	$price = isset($_POST['price']) ? trim($_POST['price']) : '';
	$category = isset($_POST['category']) ? trim($_POST['category']) : '';

	if ($name === '') {
		$errors[] = 'Nama product wajib diisi.';
	}

	if ($description === '') {
		$errors[] = 'Deskripsi wajib diisi.';
	}

	if ($price === '' || !is_numeric($price) || (int) $price < 0) {
		$errors[] = 'Harga harus berupa angka dan tidak boleh negatif.';
	}

	if ($category === '') {
		$errors[] = 'Kategori wajib diisi.';
	}

	if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
		$errors[] = 'Gambar product wajib diupload.';
	}

	$imageName = '';

	if (empty($errors) && isset($_FILES['image'])) {
		$uploadError = $_FILES['image']['error'];
		if ($uploadError !== UPLOAD_ERR_OK) {
			$errors[] = 'Upload gambar gagal. Silakan coba lagi.';
		} else {
			$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
			$originalName = $_FILES['image']['name'];
			$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

			if (!in_array($extension, $allowedExtensions, true)) {
				$errors[] = 'Format gambar tidak didukung. Gunakan: jpg, jpeg, png, webp, avif.';
			} else {
				$imageDir = __DIR__ . '/../../image';
				if (!is_dir($imageDir) && !mkdir($imageDir, 0755, true)) {
					$errors[] = 'Folder image tidak dapat dibuat.';
				} else {
					$imageName = uniqid('product_', true) . '.' . $extension;
					$imagePath = $imageDir . '/' . $imageName;

					if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
						$errors[] = 'Gagal menyimpan file gambar ke folder image.';
					}
				}
			}
		}
	}

	if (empty($errors)) {
		$stmt = $conn->prepare('INSERT INTO products (name, description, price, category, image) VALUES (?, ?, ?, ?, ?)');
		if (!$stmt) {
			if ($imageName !== '') {
				$uploadedImagePath = __DIR__ . '/../../image/' . $imageName;
				if (is_file($uploadedImagePath)) {
					unlink($uploadedImagePath);
				}
			}
			$errors[] = 'Prepare statement gagal: ' . $conn->error;
		} else {
			$priceValue = (int) $price;
			$stmt->bind_param('ssiss', $name, $description, $priceValue, $category, $imageName);

			if ($stmt->execute()) {
				$successMessage = 'Product berhasil disimpan.';
				$name = '';
				$description = '';
				$price = '';
				$category = '';
			} else {
				if ($imageName !== '') {
					$uploadedImagePath = __DIR__ . '/../../image/' . $imageName;
					if (is_file($uploadedImagePath)) {
						unlink($uploadedImagePath);
					}
				}
				$errors[] = 'Gagal menyimpan data product: ' . $stmt->error;
			}

			$stmt->close();
		}
	}
}

ob_start();
?>
<div class="row">
	<div class="col-md-8 mx-auto">
		<div class="card">
			<div class="card-body">
				<h1 class="h3 mb-3">Tambah Product</h1>

				<?php if (!empty($successMessage)): ?>
					<div class="alert alert-success"><?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?></div>
				<?php endif; ?>

				<?php if (!empty($errors)): ?>
					<div class="alert alert-danger">
						<ul class="mb-0">
							<?php foreach ($errors as $error): ?>
								<li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<form method="POST" enctype="multipart/form-data" class="row g-3">
					<div class="col-12">
						<label for="name" class="form-label">Nama Product</label>
						<input
							type="text"
							id="name"
							name="name"
							class="form-control"
							value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"
							required
						>
					</div>

					<div class="col-12">
						<label for="description" class="form-label">Deskripsi</label>
						<textarea
							id="description"
							name="description"
							class="form-control"
							rows="4"
							required
						><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></textarea>
					</div>

					<div class="col-md-6">
						<label for="price" class="form-label">Harga</label>
						<input
							type="number"
							id="price"
							name="price"
							class="form-control"
							step="0.01"
							min="0"
							value="<?php echo htmlspecialchars($price, ENT_QUOTES, 'UTF-8'); ?>"
							required
						>
					</div>

					<div class="col-md-6">
						<label for="category" class="form-label">Kategori</label>
						<select
							id="category"
							name="category"
							class="form-control"
							required
						>
							<option value="">Pilih Kategori</option>
							<option value="Minuman" <?php echo $category === 'Minuman' ? 'selected' : ''; ?>>Minuman</option>
							<option value="Makanan" <?php echo $category === 'Makanan' ? 'selected' : ''; ?>>Makanan</option>
							<option value="Snack" <?php echo $category === 'Snack' ? 'selected' : ''; ?>>Snack</option>
							<option value="Lainnya" <?php echo $category === 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
						</select>
					</div>

					<div class="col-12">
						<label for="image" class="form-label">Gambar Product</label>
						<input
							type="file"
							id="image"
							name="image"
							class="form-control"
							accept=".jpg,.jpeg,.png,.webp,.avif"
							required
						>
					</div>

					<div class="col-12">
						<button type="submit" class="btn btn-primary">Simpan Product</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php
$content = ob_get_clean();
renderTemplate('Tambah Product', $content, 'create-product', '../../');
