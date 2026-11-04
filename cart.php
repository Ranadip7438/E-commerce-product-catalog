<?php
session_start();

require 'config/database.php';
require 'includes/functions.php';

$userId = $_SESSION['user_id'] ?? null;
$cartItems = [];
$subtotal = 0;

// MEMBER CART: load from MySQL.
if ($userId) {
    $stmt = $mysqli->prepare(
        "SELECT p.id, p.name, p.price, p.image, p.stock,
                ci.quantity
         FROM cart_items ci
         JOIN products p ON p.id = ci.product_id
         WHERE ci.user_id = ?
         ORDER BY ci.id DESC"
    );

    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $result = $stmt->get_result();

    while ($item = $result->fetch_assoc()) {
        $cartItems[] = $item;
    }
}
// GUEST CART: load from PHP session.
else {
    $guestCart = $_SESSION['cart'] ?? [];

    if (!empty($guestCart)) {
        $ids = array_filter(
            array_map('intval', array_keys($guestCart)),
            fn($id) => $id > 0
        );

        if (!empty($ids)) {
            $idList = implode(',', $ids);

            $result = $mysqli->query(
                "SELECT id, name, price, image, stock
                 FROM products
                 WHERE id IN ($idList)"
            );

            while ($product = $result->fetch_assoc()) {
                $productId = (int) $product['id'];

                $product['quantity'] =
                    (int) $guestCart[$productId];

                $cartItems[] = $product;
            }
        }
    }
}

// Calculate subtotal.
foreach ($cartItems as $item) {
    $subtotal += (float) $item['price']
               * (int) $item['quantity'];
}

$pageTitle = 'Shopping Cart - Electro';

require 'includes/header.php';
?>

<section class="cart-page">
    <div class="cart-heading">
        <div>
            <p class="eyebrow">YOUR SELECTION</p>
            <h1>Shopping Cart</h1>
            <p>Review your products before continuing.</p>
        </div>

        <a href="index.php#catalog" class="button button-light">
            Continue Shopping
        </a>
    </div>

    <?php if (!empty($_SESSION['checkout_error'])): ?>
    <p class="checkout-error">
        <?= e($_SESSION['checkout_error']) ?>
    </p>
    <?php unset($_SESSION['checkout_error']); ?>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>

        <div class="cart-empty">
            <h2>Your cart is empty</h2>
            <p>Find something you love in our electronics catalog.</p>
            <a href="index.php#catalog" class="button button-dark">
                Browse Products
            </a>
        </div>

    <?php else: ?>

        <div class="cart-layout">
            <div class="cart-items">

                <?php foreach ($cartItems as $item): ?>
                    <?php
                    $itemTotal = (float) $item['price']
                               * (int) $item['quantity'];
                    ?>

                    <article class="cart-item">
                        <img
                            class="cart-product-image"
                            src="<?= e(image_url($item['image'])) ?>"
                            alt="<?= e($item['name']) ?>"
                        >

                        <div class="cart-product-info">
                            <h2><?= e($item['name']) ?></h2>

                            <p class="cart-unit-price">
                                <?= money((float) $item['price']) ?> each
                            </p>

                            <p class="cart-item-total">
                                Item total: <strong><?= money($itemTotal) ?></strong>
                            </p>

                            <?php if ((int) $item['stock'] < 1): ?>
                                <p class="cart-stock-warning">
                                    Currently out of stock.
                                </p>
                            <?php endif; ?>

                            <div class="cart-actions">

                                <form
                                    action="cart_action.php"
                                    method="post"
                                    class="cart-update-form"
                                >
                                    <input type="hidden" name="action" value="update">
                                    <input
                                        type="hidden"
                                        name="product_id"
                                        value="<?= (int) $item['id'] ?>"
                                    >

                                    <label>
                                        Qty
                                        <input
                                            type="number"
                                            name="quantity"
                                            min="1"
                                            max="<?= max(1, min(99, (int) $item['stock'])) ?>"
                                            value="<?= (int) $item['quantity'] ?>"
                                            required
                                        >
                                    </label>

                                    <button
                                        type="submit"
                                        class="button button-light"
                                    >
                                        Update
                                    </button>
                                </form>

                                <form action="cart_action.php" method="post">
                                    <input type="hidden" name="action" value="remove">
                                    <input
                                        type="hidden"
                                        name="product_id"
                                        value="<?= (int) $item['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="cart-remove-button"
                                    >
                                        Remove
                                    </button>
                                </form>

                            </div>
                        </div>
                    </article>

                <?php endforeach; ?>

            </div>

            <aside class="cart-summary">
                <h2>Order Summary</h2>

                <div class="summary-row">
                    <span>Items</span>
                    <span><?= count($cartItems) ?></span>
                </div>

                <div class="summary-row summary-total">
                    <span>Subtotal</span>
                    <strong><?= money($subtotal) ?></strong>
                </div>
                <form action="checkout.php" method="post">
                    <button
                        type="submit"
                        class="button button-dark cart-buy-button"
                    >
                        Buy Now
                    </button>
                </form>
                <p class="cart-summary-note">
                    Shipping and taxes are not included.
                </p>

                <a
                    href="index.php#catalog"
                    class="button button-dark cart-continue-button"
                >
                    Continue Shopping
                </a>
            </aside>
        </div>

    <?php endif; ?>
</section>

<?php require 'includes/footer.php'; ?>