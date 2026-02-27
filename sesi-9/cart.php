<?php
session_start();

require_once 'koneksi.php';
require_once __DIR__ . '/components/template.php';

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$errors = [];
$successMessage = '';

$checkoutUser = isset($_SESSION['checkout_user']) && is_array($_SESSION['checkout_user'])
    ? $_SESSION['checkout_user']
    : [
        'name' => '',
        'email' => '',
        'phone' => '',
        'address' => '',
    ];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart']) && isset($_POST['quantity']) && is_array($_POST['quantity'])) {
        foreach ($_POST['quantity'] as $productId => $quantityValue) {
            $id = (int) $productId;
            $quantity = (int) $quantityValue;

            if (!isset($_SESSION['cart'][$id])) {
                continue;
            }

            if ($quantity <= 0) {
                unset($_SESSION['cart'][$id]);
            } else {
                $_SESSION['cart'][$id]['quantity'] = $quantity;
            }
        }

        $successMessage = 'Keranjang berhasil diperbarui.';
    }

    if (isset($_POST['remove_id'])) {
        $removeId = (int) $_POST['remove_id'];
        if (isset($_SESSION['cart'][$removeId])) {
            unset($_SESSION['cart'][$removeId]);
            $successMessage = 'Product berhasil dihapus dari keranjang.';
        }
    }

    if (isset($_POST['checkout'])) {
        $checkoutUser['name'] = isset($_POST['user_name']) ? trim($_POST['user_name']) : '';
        $checkoutUser['email'] = isset($_POST['user_email']) ? trim($_POST['user_email']) : '';
        $checkoutUser['phone'] = isset($_POST['user_phone']) ? trim($_POST['user_phone']) : '';
        $checkoutUser['address'] = isset($_POST['user_address']) ? trim($_POST['user_address']) : '';

        if ($checkoutUser['name'] === '') {
            $errors[] = 'Nama user wajib diisi.';
        }

        if ($checkoutUser['email'] === '' || !filter_var($checkoutUser['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email user tidak valid.';
        }

        if ($checkoutUser['phone'] === '') {
            $errors[] = 'Nomor telepon user wajib diisi.';
        }

        if ($checkoutUser['address'] === '') {
            $errors[] = 'Alamat user wajib diisi.';
        }

        if (empty($_SESSION['cart'])) {
            $errors[] = 'Keranjang masih kosong.';
        } else {
            $validatedItems = [];
            $totalAmount = 0;

            $productStmt = $conn->prepare('SELECT id, name, price FROM products WHERE id = ?');
            if (!$productStmt) {
                $errors[] = 'Prepare product checkout gagal: ' . $conn->error;
            } else {
                foreach ($_SESSION['cart'] as $productId => $cartItem) {
                    $id = (int) $productId;
                    $quantity = (int) $cartItem['quantity'];

                    if ($quantity <= 0) {
                        continue;
                    }

                    $productStmt->bind_param('i', $id);
                    $productStmt->execute();
                    $result = $productStmt->get_result();
                    $product = $result ? $result->fetch_assoc() : null;

                    if (!$product) {
                        $errors[] = 'Product dengan ID ' . $id . ' tidak ditemukan.';
                        continue;
                    }

                    $price = (float) $product['price'];
                    $lineTotal = $price * $quantity;
                    $totalAmount += $lineTotal;

                    $validatedItems[] = [
                        'product_id' => $id,
                        'quantity' => $quantity,
                        'line_total' => $lineTotal,
                    ];
                }

                $productStmt->close();
            }

            if (empty($errors) && !empty($validatedItems)) {
                $_SESSION['checkout_user'] = $checkoutUser;

                $conn->begin_transaction();
                try {
                    $userStmt = $conn->prepare('INSERT INTO users (name, email, phone, address) VALUES (?, ?, ?, ?)');
                    if (!$userStmt) {
                        throw new Exception('Prepare user gagal: ' . $conn->error);
                    }

                    $userName = $checkoutUser['name'];
                    $userEmail = $checkoutUser['email'];
                    $userPhone = $checkoutUser['phone'];
                    $userAddress = $checkoutUser['address'];

                    $userStmt->bind_param('ssss', $userName, $userEmail, $userPhone, $userAddress);
                    if (!$userStmt->execute()) {
                        throw new Exception('Insert user gagal: ' . $userStmt->error);
                    }

                    $userId = (int) $conn->insert_id;
                    $userStmt->close();

                    $transactionStmt = $conn->prepare('INSERT INTO transactions (status, total, user_id, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())');
                    if (!$transactionStmt) {
                        throw new Exception('Prepare transaction gagal: ' . $conn->error);
                    }

                    $status = 'pending';
                    $transactionStmt->bind_param('sdi', $status, $totalAmount, $userId);

                    if (!$transactionStmt->execute()) {
                        throw new Exception('Insert transaction gagal: ' . $transactionStmt->error);
                    }

                    $transactionId = (int) $conn->insert_id;
                    $transactionStmt->close();

                    $itemStmt = $conn->prepare('INSERT INTO transaction_items (quantity, total_price, product_id, transaction_id) VALUES (?, ?, ?, ?)');
                    if (!$itemStmt) {
                        throw new Exception('Prepare transaction items gagal: ' . $conn->error);
                    }

                    foreach ($validatedItems as $item) {
                        $qty = (int) $item['quantity'];
                        $lineTotal = (float) $item['line_total'];
                        $productId = (int) $item['product_id'];

                        $itemStmt->bind_param('idii', $qty, $lineTotal, $productId, $transactionId);
                        if (!$itemStmt->execute()) {
                            throw new Exception('Insert transaction item gagal: ' . $itemStmt->error);
                        }
                    }

                    $itemStmt->close();
                    $conn->commit();

                    $_SESSION['user_id'] = $userId;
                    $_SESSION['cart'] = [];
                    header('Location: transaction_status.php?id=' . $transactionId);
                    exit;
                } catch (Throwable $e) {
                    $conn->rollback();
                    $errors[] = $e->getMessage();
                }
            }
        }
    }
}

$cartItems = array_values($_SESSION['cart']);
$grandTotal = 0;
foreach ($cartItems as $item) {
    $grandTotal += ((float) $item['price']) * ((int) $item['quantity']);
}

ob_start();
?>
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Keranjang</h1>
            <a href="home.php" class="btn btn-outline-secondary">Lanjut Belanja</a>
        </div>

        <?php if ($successMessage !== ''): ?>
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

        <?php if (empty($cartItems)): ?>
            <div class="alert alert-info">Keranjang masih kosong.</div>
        <?php else: ?>
            <div class="card mb-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <form method="POST">
                            <table class="table table-striped mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Product</th>
                                        <th style="width: 140px;">Price</th>
                                        <th style="width: 130px;">Quantity</th>
                                        <th style="width: 160px;">Subtotal</th>
                                        <th style="width: 120px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cartItems as $item): ?>
                                        <?php
                                        $itemId = (int) $item['id'];
                                        $itemPrice = (float) $item['price'];
                                        $itemQuantity = (int) $item['quantity'];
                                        $itemSubtotal = $itemPrice * $itemQuantity;
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars((string) $itemPrice, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm" name="quantity[<?php echo $itemId; ?>]" min="1" value="<?php echo $itemQuantity; ?>">
                                            </td>
                                            <td><?php echo htmlspecialchars((string) $itemSubtotal, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <button type="submit" name="remove_id" value="<?php echo $itemId; ?>" class="btn btn-sm btn-danger">Remove</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div class="p-3 d-flex justify-content-between align-items-center border-top">
                                <strong>Total: <?php echo htmlspecialchars((string) $grandTotal, ENT_QUOTES, 'UTF-8'); ?></strong>
                                <div class="d-flex gap-2">
                                    <button type="submit" name="update_cart" value="1" class="btn btn-outline-primary">Update Cart</button>
                                    <button type="submit" name="checkout" value="1" class="btn btn-success">Checkout</button>
                                </div>
                            </div>

                            <div class="p-3 border-top bg-light">
                                <h2 class="h5 mb-3">Data User</h2>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="user_name" class="form-label">Name</label>
                                        <input type="text" id="user_name" name="user_name" class="form-control" value="<?php echo htmlspecialchars($checkoutUser['name'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="user_email" class="form-label">Email</label>
                                        <input type="email" id="user_email" name="user_email" class="form-control" value="<?php echo htmlspecialchars($checkoutUser['email'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="user_phone" class="form-label">Phone</label>
                                        <input type="text" id="user_phone" name="user_phone" class="form-control" value="<?php echo htmlspecialchars($checkoutUser['phone'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="user_address" class="form-label">Address</label>
                                        <input type="text" id="user_address" name="user_address" class="form-control" value="<?php echo htmlspecialchars($checkoutUser['address'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
renderTemplate('Keranjang', $content, 'cart');
