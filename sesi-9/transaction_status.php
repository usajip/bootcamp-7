<?php
session_start();

require_once 'koneksi.php';
require_once __DIR__ . '/components/template.php';

$errors = [];
$transaction = null;
$items = [];
$user = null;

$whatsAppNumber = '62883734734';
$whatsAppUrl = '';

$transactionId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($transactionId <= 0) {
    $errors[] = 'ID transaksi tidak valid.';
} else {
    $transactionStmt = $conn->prepare('SELECT id, status, total, user_id, created_at, updated_at FROM transactions WHERE id = ?');
    if (!$transactionStmt) {
        $errors[] = 'Prepare transaksi gagal: ' . $conn->error;
    } else {
        $transactionStmt->bind_param('i', $transactionId);
        $transactionStmt->execute();
        $transactionResult = $transactionStmt->get_result();
        $transaction = $transactionResult ? $transactionResult->fetch_assoc() : null;
        $transactionStmt->close();

        if (!$transaction) {
            $errors[] = 'Transaksi tidak ditemukan.';
        }
    }

    if (empty($errors) && $transaction) {
        $userStmt = $conn->prepare('SELECT id, name, email, phone, address FROM users WHERE id = ?');
        if (!$userStmt) {
            $errors[] = 'Prepare user transaksi gagal: ' . $conn->error;
        } else {
            $userId = (int) $transaction['user_id'];
            $userStmt->bind_param('i', $userId);
            $userStmt->execute();
            $userResult = $userStmt->get_result();
            $user = $userResult ? $userResult->fetch_assoc() : null;
            $userStmt->close();
        }

        if (!empty($errors)) {
            $user = null;
        }
    }

    if (empty($errors) && $transaction) {
        $itemStmt = $conn->prepare('SELECT ti.quantity, ti.total_price, ti.product_id, p.name FROM transaction_items ti LEFT JOIN products p ON p.id = ti.product_id WHERE ti.transaction_id = ?');
        if (!$itemStmt) {
            $errors[] = 'Prepare item transaksi gagal: ' . $conn->error;
        } else {
            $itemStmt->bind_param('i', $transactionId);
            $itemStmt->execute();
            $itemResult = $itemStmt->get_result();
            while ($row = $itemResult->fetch_assoc()) {
                $items[] = $row;
            }
            $itemStmt->close();
        }
    }
}

if ($transaction) {
    $messageLines = [
        'Halo, saya ingin konfirmasi transaksi.',
        'ID Transaksi: #' . $transaction['id'],
        'Status: ' . $transaction['status'],
        'Total: ' . $transaction['total'],
        'Tanggal: ' . $transaction['created_at'],
    ];

    $whatsAppMessage = implode("\n", $messageLines);
    $encodedMessage = urlencode($whatsAppMessage);

    $cleanNumber = preg_replace('/\D+/', '', $whatsAppNumber);
    if ($cleanNumber !== '') {
        $whatsAppUrl = 'https://wa.me/' . $cleanNumber . '?text=' . $encodedMessage;
    } else {
        $whatsAppUrl = 'https://wa.me/?text=' . $encodedMessage;
    }
}

ob_start();
?>
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Status Transaksi</h1>
            <div class="d-flex gap-2">
                <?php if ($whatsAppUrl !== ''): ?>
                    <a href="<?php echo htmlspecialchars($whatsAppUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-success">Konfirmasi via WhatsApp</a>
                <?php endif; ?>
                <a href="home.php" class="btn btn-outline-secondary">Kembali ke Home</a>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h2 class="h5 mb-3">Informasi Transaksi</h2>
                    <div class="row g-2">
                        <div class="col-md-4"><strong>ID:</strong> #<?php echo (int) $transaction['id']; ?></div>
                        <div class="col-md-4"><strong>Status:</strong> <?php echo htmlspecialchars($transaction['status'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="col-md-4"><strong>User ID:</strong> <?php echo (int) $transaction['user_id']; ?></div>
                        <div class="col-md-6"><strong>Total:</strong> <?php echo htmlspecialchars((string) $transaction['total'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="col-md-6"><strong>Dibuat:</strong> <?php echo htmlspecialchars($transaction['created_at'], ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>

                    <?php if ($user): ?>
                        <hr>
                        <h3 class="h6 mb-2">Data User</h3>
                        <div class="row g-2">
                            <div class="col-md-6"><strong>Name:</strong> <?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="col-md-6"><strong>Email:</strong> <?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="col-md-6"><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="col-md-6"><strong>Address:</strong> <?php echo htmlspecialchars($user['address'], ENT_QUOTES, 'UTF-8'); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Product</th>
                                    <th style="width: 140px;">Quantity</th>
                                    <th style="width: 180px;">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($items)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4">Tidak ada item transaksi.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['name'] ?? ('Product #' . $item['product_id']), ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo (int) $item['quantity']; ?></td>
                                            <td><?php echo htmlspecialchars((string) $item['total_price'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
renderTemplate('Status Transaksi', $content, 'cart');
