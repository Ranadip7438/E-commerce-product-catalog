<?php
require 'config/database.php';
require 'includes/functions.php';

$search = $_GET['q'] ?? '';
$categoryId = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT);

if (!$categoryId || $categoryId < 1) {
    $categoryId = null;
}
$category = $_GET['category'] ?? '';

$categoryResult = $mysqli->query(
    "SELECT * FROM categories ORDER BY name"
);

// Get products based on search and category
if ($search != '' && $category != '') {

    $search = "%$search%";

    $stmt = $mysqli->prepare(
        "SELECT products.*, categories.name AS category_name
         FROM products
         JOIN categories ON products.category_id = categories.id
         WHERE (products.name LIKE ? OR products.description LIKE ?)
         AND categories.id = ?
         ORDER BY products.name"
    );

    $stmt->bind_param("ssi", $search, $search, $category);

} elseif ($search != '') {

    $search = "%$search%";

    $stmt = $mysqli->prepare(
        "SELECT products.*, categories.name AS category_name
         FROM products
         JOIN categories ON products.category_id = categories.id
         WHERE products.name LIKE ? OR products.description LIKE ?
         ORDER BY products.name"
    );

    $stmt->bind_param("ss", $search, $search);

} elseif ($category != '') {

    $stmt = $mysqli->prepare(
        "SELECT products.*, categories.name AS category_name
         FROM products
         JOIN categories ON products.category_id = categories.id
         WHERE categories.id = ?
         ORDER BY products.name"
    );

    $stmt->bind_param("i", $category);

} else {

    $stmt = $mysqli->prepare(
        "SELECT products.*, categories.name AS category_name
         FROM products
         JOIN categories ON products.category_id = categories.id
         ORDER BY products.name"
    );
}

$stmt->execute();
$productResult = $stmt->get_result();

$products = $productResult->fetch_all(MYSQLI_ASSOC);

$pageTitle = "Electro - Electronics Store";

require 'includes/header.php';
?>

<section class="hero">
    <div class="hero-copy">
        <p class="eyebrow"><span class="eyebrow-line"></span> THE EVERYDAY TECH EDIT</p>
        <h1>Good tech.<br><span>Better living.</span></h1>
        <p class="hero-description">Discover the electronics that make your everyday a little more effortless.</p>
        <a class="button button-dark" href="#catalog">Explore the collection <span aria-hidden="true">↘</span></a>
    </div>
    <div class="hero-art" aria-hidden="true">
        <img src="assets/images/hero-banner.png" alt="Electro catalog banner" height= "300">
    </div>
</section>

<section class="catalog-section" id="catalog">
    <div class="section-heading">
        <div>
            <p class="eyebrow">CURATED FOR YOU</p>
            <h2>Find your next <span>favourite.</span></h2>
        </div>
        <p class="result-count"><?= count($products) ?> <?= count($products) === 1 ? 'product' : 'products' ?> to explore</p>
    </div>

    <form class="catalog-tools" action="index.php#catalog" method="get">
        <label class="search-box">
            <span class="sr-only">Search products</span>
            <span class="search-icon" aria-hidden="true">⌕</span>
            <input type="search" name="q" placeholder="Search headphones, cameras, laptops..." value="<?= e($search) ?>">
        </label>
        <label class="category-select">
            <span class="sr-only">Filter by category</span>
            <select name="category">
    <option value="">All categories</option>

    <?php while ($category = $categoryResult->fetch_assoc()) { ?>
        <option value="<?php echo $category['id']; ?>"
            <?php
            if (isset($_GET['category']) &&
                $_GET['category'] == $category['id']) {
                echo "selected";
            }
            ?>>
            <?php echo htmlspecialchars($category['name']); ?>
        </option>
    <?php } ?>
</select>
        </label>
        <button class="button button-dark tool-submit" type="submit">Find products</button>
        <?php if ($search !== '' || $categoryId !== null): ?>
            <a class="clear-link" href="index.php#catalog">Clear filters</a>
        <?php endif; ?>
    </form>

    <?php if ($products): ?>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <article class="product-card">
                    <a class="product-image-wrap" href="product.php?id=<?= (int) $product['id'] ?>" aria-label="View <?= e($product['name']) ?>">
                        <img src="<?= e(image_url($product['image'])) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
                        <span class="image-arrow" aria-hidden="true">↗</span>
                    </a>
                    <div class="product-meta"><span><?= e($product['category_name']) ?></span><span>ESSENTIALS</span></div>
                    <h3><a href="product.php?id=<?= (int) $product['id'] ?>"><?= e($product['name']) ?></a></h3>
                    <p class="product-description"><?= e(mb_strimwidth((string) $product['description'], 0, 90, '…')) ?></p>
                    <div class="product-bottom"><strong><?= money($product['price']) ?></strong><a href="product.php?id=<?= (int) $product['id'] ?>" class="text-link">View details <span aria-hidden="true">→</span></a></div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state"><span class="empty-icon">⌕</span><h3>No products found</h3><p>Try another search term or choose a different category.</p><a class="button button-dark" href="index.php#catalog">View all products</a></div>
    <?php endif; ?>
</section>
<section class="bottom-banner">
    <div><p class="eyebrow">LESS SCROLLING. MORE DISCOVERING.</p><h2>Tech that just <em>fits.</em></h2></div>
    <a href="#catalog" class="button button-dark">Browse catalog <span aria-hidden="true">↗</span></a>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
