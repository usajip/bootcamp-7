<?php
session_start();

require_once 'koneksi.php';
require_once __DIR__ . '/components/template.php';

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'], $_POST['product_id'])) {
    $productId = (int) $_POST['product_id'];

    if ($productId > 0) {
        $cartProductStmt = $conn->prepare('SELECT id, name, price, image FROM products WHERE id = ?');
        if ($cartProductStmt) {
            $cartProductStmt->bind_param('i', $productId);
            $cartProductStmt->execute();
            $cartProductResult = $cartProductStmt->get_result();
            $cartProduct = $cartProductResult ? $cartProductResult->fetch_assoc() : null;
            $cartProductStmt->close();

            if ($cartProduct) {
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId]['quantity']++;
                } else {
                    $_SESSION['cart'][$productId] = [
                        'id' => (int) $cartProduct['id'],
                        'name' => $cartProduct['name'],
                        'price' => (float) $cartProduct['price'],
                        'image' => $cartProduct['image'],
                        'quantity' => 1,
                    ];
                }

                $_SESSION['cart_success'] = 'Product berhasil ditambahkan ke keranjang.';
            }
        }
    }

    header('Location: home.php');
    exit;
}

if (isset($_SESSION['cart_success'])) {
    $successMessage = $_SESSION['cart_success'];
    unset($_SESSION['cart_success']);
}

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

ob_start();
?>
<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Products List</h1>

        <?php if ($successMessage !== ''): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

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
                                <form method="POST" class="mt-3">
                                    <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                    <button type="submit" name="add_to_cart" value="1" class="btn btn-sm btn-primary">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
renderTemplate('Products List', $content, 'home');