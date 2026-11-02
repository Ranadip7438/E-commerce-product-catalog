<?php
session_start();

require 'config/database.php';
require 'includes/functions.php';

$search = $_GET['q'] ?? '';
$categoryId = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT);

if (!$categoryId || $categoryId < 1) {
    $categoryId = null;
}
$category = $_GET['category'] ?? '';

$categoryResult = $mysqli->query("SELECT * FROM categories ORDER BY name");

// Get products
$sql = "SELECT products.*, categories.name AS category_name
        FROM products
        JOIN categories ON products.category_id = categories.id
        WHERE 1=1";

if ($search != '') {
    $search = trim($search);
    $sql .= " AND (products.name LIKE ? OR products.description LIKE ?)";
}

if ($categoryId != '') {
    $sql .= " AND categories.id = ?";
}

$sql .= " ORDER BY products.name ASC";

$stmt = $mysqli->prepare($sql);

// Bind search and category values
if ($search != '' && $categoryId != '') {
    $searchTerm = "%$search%";
    $stmt->bind_param("ssi", $searchTerm, $searchTerm, $categoryId);
} elseif ($search != '') {
    $searchTerm = "%$search%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
} elseif ($categoryId != '') {
    $stmt->bind_param("i", $categoryId);
}

$stmt->execute();
$productResult = $stmt->get_result();

$pageTitle = "Electro Catalog";
require 'includes/header.php';
?>

<section class="hero">
    <div class="hero-copy">
        <p class="eyebrow">THE EVERYDAY TECH EDIT</p>
        <h1>Good tech.<br><span>Better living.</span></h1>
        <p class="hero-description">
            Discover electronics that make everyday life easier.
        </p>
        <a class="button button-dark" href="#catalog">
            Explore the collection
        </a>
    </div>

    <div class="hero-art">
        <img src="assets/images/hero-banner.png"
             alt="Electronics catalog banner"
             height="300">
    </div>
</section>

<section class="catalog-section" id="catalog">
    <div class="section-heading">
        <div>
            <p class="eyebrow">CURATED FOR YOU</p>
            <h2>Find your next <span>favourite.</span></h2>
        </div>
    </div>

    <form class="catalog-tools" action="index.php#catalog" method="get">
        <label class="search-box">
            <input type="search"
                   name="q"
                   placeholder="Search products..."
                   value="<?= e($search) ?>">
        </label>

        <label class="category-select">
            <select name="category">
                <option value="">All categories</option>

                <?php while ($category = $categoryResult->fetch_assoc()) { ?>
                    <option value="<?= (int)$category['id'] ?>"
                        <?= (string)$categoryId === (string)$category['id'] ? 'selected' : '' ?>>
                        <?= e($category['name']) ?>
                    </option>
                <?php } ?>
            </select>
        </label>

        <button class="button button-dark" type="submit">
            Find products
        </button>
        <?php if ($search !== '' || $categoryId !== null): ?>
            <a class="clear-link" href="index.php#catalog">Clear filters</a>
        <?php endif; ?>
    </form>

    <?php if ($productResult->num_rows > 0) { ?>

        <div class="product-grid">

            <?php while ($product = $productResult->fetch_assoc()) { ?>

                <article class="product-card">

                    <a class="product-image-wrap"
                       href="product.php?id=<?= (int)$product['id'] ?>">
                        <img src="<?= e(image_url($product['image'])) ?>"
                             alt="<?= e($product['name']) ?>"
                             loading="lazy">
                    </a>

                    <div class="product-meta">
                        <span><?= e($product['category_name']) ?></span>
                        <span>ESSENTIALS</span>
                    </div>

                    <h3>
                        <a href="product.php?id=<?= (int)$product['id'] ?>">
                            <?= e($product['name']) ?>
                        </a>
                    </h3>

                    <p class="product-description">
                        <?= e(mb_strimwidth(
                            (string)$product['description'],
                            0,
                            60,
                            '...'
                        )) ?>
                    </p>

                    <div class="product-bottom">
                        <strong><?= money($product['price']) ?></strong>

                        <a href="product.php?id=<?= (int)$product['id'] ?>"
                           class="text-link">
                            View details →
                        </a>
                    </div>

                </article>

            <?php } ?>

        </div>

    <?php } else { ?>

        <div class="empty-state">
            <h3>No products found</h3>
            <p>Try another search or select a different category.</p>
            <a class="button button-dark" href="index.php#catalog">
                View all products
            </a>
        </div>

    <?php } ?>
</section>

<section class="bottom-banner">
    <div>
        <p class="eyebrow">LESS SCROLLING. MORE DISCOVERING.</p>
        <h2>Tech that just <em>fits.</em></h2>
    </div>

    <a href="#catalog" class="button button-dark">
        Browse catalog
    </a>
</section>

<?php require 'includes/footer.php'; ?>
