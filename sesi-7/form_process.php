<?php
$errors = [];
$allowedCategories = ['electronics', 'fashion', 'home', 'beauty'];

$name = trim($_POST['name'] ?? '');
$priceRaw = trim((string)($_POST['price'] ?? ''));
$description = trim($_POST['description'] ?? '');
$category = trim($_POST['category'] ?? '');
$stockRaw = trim((string)($_POST['stock'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: tugas.php');
	exit;
}

if ($name === '' || strlen($name) > 255) {
	$errors[] = 'Name is required and must be less than 256 characters.';
}

if ($description === '') {
	$errors[] = 'Description is required.';
}

if ($priceRaw === '' || !is_numeric($priceRaw) || (int)$priceRaw <= 0) {
	$errors[] = 'Price must be a number greater than 0.';
}

if (!in_array($category, $allowedCategories, true)) {
	$errors[] = 'Category is invalid.';
}

if ($stockRaw === '' || filter_var($stockRaw, FILTER_VALIDATE_INT) === false || (int)$stockRaw < 0) {
	$errors[] = 'Stock must be an integer 0 or more.';
}

$price = $priceRaw !== '' && is_numeric($priceRaw) ? (int)$priceRaw : 0;
$stock = filter_var($stockRaw, FILTER_VALIDATE_INT) !== false ? (int)$stockRaw : 0;
$formattedPrice = "Rp".number_format($price, 2, ',', '.'); // Format harga dengan tanda "Rp" dan pemisah ribuan serta desimal sesuai format Indonesia 40000 menjadi Rp40.000,00
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Product Input Result</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
	<div class="container py-5">
		<div class="row">
			<div class="col-md-8 mx-auto">
				<h1 class="mb-4">Form Processing Result</h1>

				<?php if (!empty($errors)): ?>
					<div class="alert alert-danger" role="alert">
						<h4 class="alert-heading">Validation failed</h4>
						<ul class="mb-0">
							<?php foreach ($errors as $error): ?>
								<li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
					<a href="tugas.php" class="btn btn-secondary">Back to Form</a>
				<?php else: ?>
					<div class="card">
						<div class="card-body">
							<h5 class="card-title mb-3">Product Data</h5>
							<p class="mb-2"><strong>Name:</strong> <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></p>
							<p class="mb-2"><strong>Price:</strong> <?php echo htmlspecialchars($formattedPrice, ENT_QUOTES, 'UTF-8'); ?></p>
							<p class="mb-2"><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($description, ENT_QUOTES, 'UTF-8')); ?></p>
							<p class="mb-2"><strong>Category:</strong> <?php echo htmlspecialchars(ucfirst($category), ENT_QUOTES, 'UTF-8'); ?></p>
							<p class="mb-0"><strong>Stock:</strong> <?php echo htmlspecialchars((string)$stock, ENT_QUOTES, 'UTF-8'); ?></p>
						</div>
					</div>
					<a href="tugas.php" class="btn btn-primary mt-3">Input Another Product</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
