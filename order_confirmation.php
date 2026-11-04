<?php
session_start();

require 'config/database.php';
require 'includes/functions.php';

$confirmation = $_SESSION['purchase_confirmation'] ?? null;

if (!$confirmation) {
    header('Location: index.php');
    exit;
}

unset($_SESSION['purchase_confirmation']);

$pageTitle = 'Order Confirmation - Electro';
require 'includes/header.php';
?>

<section class="cart-page">
    <div class="cart-empty">
        <h1>
            <?= $confirmation['guest']
                ? 'Demo Purchase Confirmed!'
                : 'Order Placed Successfully!' ?>
        </h1>

        <?php if ($confirmation['guest']): ?>
            <p>
                This is a demo checkout. No order was saved to a member
                account, and product stock was not changed.
            </p>
        <?php else: ?>
            <p>
                Thank you for shopping with Electro.
                Your order has been recorded.
            </p>

            <p>
                Order number:
                <strong>#<?= (int)$confirmation['order_id'] ?></strong>
            </p>
        <?php endif; ?>

        <p>
            Total:
            <strong><?= money((float)$confirmation['total']) ?></strong>
        </p>

        <a href="index.php#catalog" class="button button-dark">
            Continue Shopping
        </a>

        <?php if (!$confirmation['guest']): ?>
            <a href="purchase_history.php" class="button button-light">
                View Purchase History
            </a>
        <?php endif; ?>
    </div>
</section>

<?php require 'includes/footer.php'; ?>