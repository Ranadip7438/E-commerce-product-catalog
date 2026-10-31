<?php
declare(strict_types=1);
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(404);
    $product = null;
} else {
    $stmt = $mysqli->prepare(
        'SELECT p.id, p.name, p.description, p.price, p.image, p.stock, c.name AS category_name
         FROM products p JOIN categories c ON c.id = p.category_id WHERE p.id = ?'
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc() ?: null;
}

if (!$product) {
    http_response_code(404);
}

$pageTitle = $product ? $product['name'] . ' — Electro' : 'Product not found — Electro';
require __DIR__ . '/includes/header.php';
?>

<div class="detail-shell">
    <a class="back-link" href="index.php#catalog">← Back to catalog</a>
    <?php if ($product): ?>
        <div class="detail-layout">
            <div class="detail-image"><img src="<?= e(image_url($product['image'])) ?>" alt="<?= e($product['name']) ?>"></div>
            <div class="detail-copy">
                <p class="eyebrow"><?= e($product['category_name']) ?> / PRODUCT DETAILS</p>
                <h1><?= e($product['name']) ?></h1>
                <p class="detail-price"><?= money($product['price']) ?></p>
                <div class="detail-rule"></div>
                <h2>About this product</h2>
                <p class="detail-description"><?= nl2br(e($product['description'])) ?></p>
                <p class="stock-note"><span class="stock-dot"></span><?= (int) $product['stock'] > 0 ? 'In stock' : 'Currently unavailable' ?></p>
                <a class="button button-dark detail-cta" href="index.php#catalog">Continue exploring <span aria-hidden="true">→</span></a>
                <p class="detail-footnote">Product information is provided for catalog demonstration purposes.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-state"><h1>Product not found</h1><p>This product may have been removed or the link is incorrect.</p><a class="button button-dark" href="index.php">Return to catalog</a></div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
