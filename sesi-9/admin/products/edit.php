<?php
require_once '../../koneksi.php';
require_once __DIR__ . '/../../components/template.php';

$errors = [];
$successMessage = '';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
	header('Location: index.php');
	exit;
}

$stmt = $conn->prepare('SELECT id, name, description, price, category, image FROM products WHERE id = ?');
if (!$stmt) {
	$errors[] = 'Prepare select gagal: ' . $conn->error;
	$product = null;
} else {
	$stmt->bind_param('i', $id);
	$stmt->execute();
	$result = $stmt->get_result();
	$product = $result ? $result->fetch_assoc() : null;
	$stmt->close();
}

if (!$product) {
	header('Location: index.php');
	exit;
}

$name = $product['name'];
$description = $product['description'];
$price = $product['price'];
$category = $product['category'];
$currentImage = $product['image'];

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

	if ($price === '' || !is_numeric($price) || (float) $price < 0) {
		$errors[] = 'Harga harus berupa angka dan tidak boleh negatif.';
	}

	if ($category === '') {
		$errors[] = 'Kategori wajib diisi.';
	}

	$newImageName = $currentImage;

	if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
		if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
			$errors[] = 'Upload gambar gagal. Silakan coba lagi.';
		} else {
			$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
			$extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

			if (!in_array($extension, $allowedExtensions, true)) {
				$errors[] = 'Format gambar tidak didukung. Gunakan: jpg, jpeg, png, webp, avif.';
			} else {
				$imageDir = __DIR__ . '/../../image';
				if (!is_dir($imageDir) && !mkdir($imageDir, 0755, true)) {
					$errors[] = 'Folder image tidak dapat dibuat.';
				} else {
					$newImageName = uniqid('product_', true) . '.' . $extension;
					$newImagePath = $imageDir . '/' . $newImageName;

					if (!move_uploaded_file($_FILES['image']['tmp_name'], $newImagePath)) {
						$errors[] = 'Gagal menyimpan file gambar ke folder image.';
					}
				}
			}
		}
	}

	if (empty($errors)) {
		$updateStmt = $conn->prepare('UPDATE products SET name = ?, description = ?, price = ?, category = ?, image = ? WHERE id = ?');
		if (!$updateStmt) {
			if ($newImageName !== $currentImage && $newImageName !== '') {
				$uploadedImagePath = __DIR__ . '/../../image/' . $newImageName;
				if (is_file($uploadedImagePath)) {
					unlink($uploadedImagePath);
				}
			}
			$errors[] = 'Prepare update gagal: ' . $conn->error;
		} else {
			$priceValue = (float) $price;
			$updateStmt->bind_param('ssissi', $name, $description, $priceValue, $category, $newImageName, $id);

			if ($updateStmt->execute()) {
				if ($newImageName !== $currentImage && !empty($currentImage)) {
					$oldImagePath = __DIR__ . '/../../image/' . $currentImage;
					if (is_file($oldImagePath)) {
						unlink($oldImagePath);
					}
				}

				$currentImage = $newImageName;
				$successMessage = 'Product berhasil diperbarui.';
			} else {
				if ($newImageName !== $currentImage && $newImageName !== '') {
					$uploadedImagePath = __DIR__ . '/../../image/' . $newImageName;
					if (is_file($uploadedImagePath)) {
						unlink($uploadedImagePath);
					}
				}
				$errors[] = 'Gagal memperbarui product: ' . $updateStmt->error;
			}

			$updateStmt->close();
		}
	}
}

ob_start();
?>
<div class="row">
	<div class="col-md-8 mx-auto">
		<div class="card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h1 class="h3 mb-0">Edit Product</h1>
					<a href="index.php" class="btn btn-outline-secondary btn-sm">Kembali ke List</a>
				</div>

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
						<input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" required>
					</div>

					<div class="col-12">
						<label for="description" class="form-label">Deskripsi</label>
						<textarea id="description" name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></textarea>
					</div>

					<div class="col-md-6">
						<label for="price" class="form-label">Harga</label>
						<input type="number" id="price" name="price" class="form-control" step="0.01" min="0" value="<?php echo htmlspecialchars((string) $price, ENT_QUOTES, 'UTF-8'); ?>" required>
					</div>

					<div class="col-md-6">
						<label for="category" class="form-label">Kategori</label>
						<input type="text" id="category" name="category" class="form-control" value="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>" required>
					</div>

					<div class="col-12">
						<label for="image" class="form-label">Gambar Product (opsional)</label>
						<input type="file" id="image" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp,.avif">
					</div>

					<?php if (!empty($currentImage)): ?>
						<div class="col-12">
							<p class="mb-2">Gambar saat ini:</p>
							<img src="../../image/<?php echo htmlspecialchars($currentImage, ENT_QUOTES, 'UTF-8'); ?>" alt="Current Image" class="img-thumbnail" style="max-height: 120px; object-fit: cover;">
						</div>
					<?php endif; ?>

					<div class="col-12">
						<button type="submit" class="btn btn-primary">Update Product</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php
$content = ob_get_clean();
renderTemplate('Edit Product', $content, 'create-product', '../../');
