<?php
require_once 'koneksi.php';

// get filter values from URL (GET)
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$selectedCategory = isset($_GET['category']) ? trim($_GET['category']) : '';

// get all categories for filter dropdown
$categories = [];
$categoriesResult = $conn->query("SELECT DISTINCT category FROM products ORDER BY category ASC");
if (!$categoriesResult) {
    die("Query categories failed: " . $conn->error);
}

while ($row = $categoriesResult->fetch_assoc()) {
    if (!empty($row['category'])) {
        $categories[] = $row['category'];
    }
}

// build query with optional filters
if ($selectedCategory !== '' && $search !== '') {
    $stmt = $conn->prepare("SELECT * FROM products WHERE category = ? AND name LIKE ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $searchLike = "%$search%";
    $stmt->bind_param("ss", $selectedCategory, $searchLike);
} elseif ($selectedCategory !== '') {
    $stmt = $conn->prepare("SELECT * FROM products WHERE category = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $selectedCategory);
} elseif ($search !== '') {
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $searchLike = "%$search%";
    $stmt->bind_param("s", $searchLike);
} else {
    $stmt = $conn->prepare("SELECT * FROM products");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
}

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();
if (!$result) {
    die("Get result failed: " . $stmt->error);
}

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="mb-4">Products List</h1>

                    <form method="GET" class="row g-2 mb-4">
                        <div class="col-md-5">
                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search product name..."
                                value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
                            >
                        </div>
                        <div class="col-md-4">
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selectedCategory === $category ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars(ucfirst($category), ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                            <a href="home.php" class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </form>

                    <?php if (empty($products)): ?>
                        <p>No products found.</p>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($products as $product): ?>
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                            <p class="card-text">
                                                <strong>Price:</strong> <?php echo htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?><br>
                                                <strong>Category:</strong> <?php echo htmlspecialchars(ucfirst($product['category']), ENT_QUOTES, 'UTF-8'); ?>
                                            </p>
                                            <p class="card-text"><small class="text-muted"><?php echo nl2br(htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8')); ?></small></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    </body>
</html>