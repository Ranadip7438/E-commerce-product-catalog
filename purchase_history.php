<?php
session_start();

require 'config/database.php';
require 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];

$stmt = $mysqli->prepare(
    "SELECT id, total_amount, status, created_at
     FROM orders
     WHERE user_id = ?
     ORDER BY created_at DESC"
);
$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();
$orders = [];

while ($order = $result->fetch_assoc()) {
    $orders[] = $order;
}

$itemStmt = $mysqli->prepare(
    "SELECT product_name, unit_price, quantity
     FROM order_items
     WHERE order_id = ?
     ORDER BY id"
);

$pageTitle = 'Purchase History - Electro';
require 'includes/header.php';
?>

<section class="cart-page">
    <div class="cart-heading">
        <div>
            <p class="eyebrow">YOUR ACCOUNT</p>
            <h1>Purchase History</h1>
            <p>Review your previous orders.</p>
        </div>

        <a href="index.php#catalog" class="button button-light">
            Continue Shopping
        </a>
    </div>

    <?php if (empty($orders)): ?>
        <div class="cart-empty">
            <h2>No purchases yet</h2>
            <p>Your completed orders will appear here.</p>
            <a href="index.php#catalog" class="button button-dark">
                Browse Products
            </a>
        </div>
    <?php else: ?>

        <?php foreach ($orders as $order): ?>
            <article class="history-order">
                <div class="history-order-heading">
                    <div>
                        <h2>Order #<?= (int)$order['id'] ?></h2>
                        <p>
                            <?= e($order['created_at']) ?>
                            · <?= e(ucfirst($order['status'])) ?>
                        </p>
                    </div>

                    <strong>
                        <?= money((float)$order['total_amount']) ?>
                    </strong>
                </div>

                <?php
                $orderId = (int)$order['id'];
                $itemStmt->bind_param("i", $orderId);
                $itemStmt->execute();
                $orderItems = $itemStmt->get_result();
                ?>

                <div class="history-items">
                    <?php while ($item = $orderItems->fetch_assoc()): ?>
                        <div class="history-item">
                            <span>
                                <?= e($item['product_name']) ?>
                                × <?= (int)$item['quantity'] ?>
                            </span>

                            <strong>
                                <?= money(
                                    (float)$item['unit_price']
                                    * (int)$item['quantity']
                                ) ?>
                            </strong>
                        </div>
                    <?php endwhile; ?>
                </div>
            </article>
        <?php endforeach; ?>

    <?php endif; ?>
</section>

<?php require 'includes/footer.php'; ?>