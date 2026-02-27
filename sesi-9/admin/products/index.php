<?php
require_once '../../koneksi.php';
require_once __DIR__ . '/../../components/template.php';

$errors = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
	$deleteId = (int) $_POST['delete_id'];

	if ($deleteId <= 0) {
		$errors[] = 'ID product tidak valid.';
	} else {
		$productStmt = $conn->prepare('SELECT image FROM products WHERE id = ?');
		if (!$productStmt) {
			$errors[] = 'Prepare select gagal: ' . $conn->error;
		} else {
			$productStmt->bind_param('i', $deleteId);
			$productStmt->execute();
			$productResult = $productStmt->get_result();
			$product = $productResult ? $productResult->fetch_assoc() : null;
			$productStmt->close();

			if (!$product) {
				$errors[] = 'Product tidak ditemukan.';
			} else {
				$deleteStmt = $conn->prepare('DELETE FROM products WHERE id = ?');
				if (!$deleteStmt) {
					$errors[] = 'Prepare delete gagal: ' . $conn->error;
				} else {
					$deleteStmt->bind_param('i', $deleteId);
					if ($deleteStmt->execute()) {
						if (!empty($product['image'])) {
							$imagePath = __DIR__ . '/../../image/' . $product['image'];
							if (is_file($imagePath)) {
								unlink($imagePath);
							}
						}
						$successMessage = 'Product berhasil dihapus.';
					} else {
						$errors[] = 'Gagal menghapus product: ' . $deleteStmt->error;
					}
					$deleteStmt->close();
				}
			}
		}
	}
}

$products = [];
$result = $conn->query('SELECT id, name, description, price, category, image FROM products');
if ($result) {
	while ($row = $result->fetch_assoc()) {
		$shortDescription = strlen($row['description']) > 80 ? substr($row['description'], 0, 80) . '...' : $row['description'];
		$row['short_description'] = $shortDescription;
		$products[] = $row;
	}
} else {
	$errors[] = 'Gagal mengambil data product: ' . $conn->error;
}

ob_start();
?>
<div class="row">
	<div class="col-12">
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h1 class="h3 mb-0">List Product</h1>
			<a href="create.php" class="btn btn-primary">Create New</a>
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

		<div class="card">
			<div class="card-body p-0">
				<div class="table-responsive">
					<table id="productsTable" class="table table-striped table-hover mb-0 w-100">
						<thead class="table-dark">
							<tr>
								<th style="width: 70px;">ID</th>
								<th style="width: 90px;">Image</th>
								<th>Name</th>
								<th>Description</th>
								<th style="width: 130px;">Price</th>
								<th style="width: 130px;">Category</th>
								<th style="width: 190px;">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php if (empty($products)): ?>
								<tr>
									<td colspan="7" class="text-center py-4">Belum ada data product.</td>
								</tr>
							<?php else: ?>
								<?php foreach ($products as $index => $product): ?>
									<tr>
										<td><?php echo $product['id']; ?></td>
										<td>
											<?php if (!empty($product['image'])): ?>
												<img src="../../image/<?php echo htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid rounded" style="max-height: 48px; object-fit: cover;">
											<?php else: ?>
												<span class="text-muted">-</span>
											<?php endif; ?>
										</td>
										<td><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo htmlspecialchars($product['short_description'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo htmlspecialchars((string) "Rp. " . number_format($product['price'], 0, '.', '.'), ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td>
											<div class="d-flex gap-2">
												<a href="edit.php?id=<?php echo (int) $product['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
												<form method="POST" onsubmit="return confirm('Yakin ingin menghapus product ini?');">
													<input type="hidden" name="delete_id" value="<?php echo (int) $product['id']; ?>">
													<button type="submit" class="btn btn-sm btn-danger">Delete</button>
												</form>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
$content = ob_get_clean();
$extraHead = '<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">';
$extraScript = '
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
<script>
	document.addEventListener("DOMContentLoaded", function () {
		new DataTable("#productsTable", {
			pageLength: 10,
			lengthMenu: [5, 10, 25, 50],
			order: [[0, "asc"]]
		});
	});
</script>';
renderTemplate('List Product', $content, 'create-product', '../../', $extraHead, $extraScript);
