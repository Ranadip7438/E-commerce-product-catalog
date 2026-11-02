
<?php
$pageTitle = $pageTitle ?? 'Electro - Everyday technology';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="Browse electronics and find your favourite products.">

    <title><?= e($pageTitle) ?></title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="site-header">

    <a class="brand" href="index.php">
        <span class="brand-mark">e</span>
        electro<span class="brand-dot">.</span>
    </a>

    <nav class="header-nav">

        <?php if (isset($_SESSION['user_id'])) { ?>

            <span class="nav-note">
                Hi, <?= e($_SESSION['user_name'] ?? 'User') ?>
            </span>

            <form class="nav-logout-form" action="logout.php" method="post">
                <button class="nav-button" type="submit">
                    Log out
                </button>
            </form>

        <?php } else { ?>

            <a href="login.php">Log in</a>

            <a class="nav-signup" href="register.php">
                Sign up
            </a>

        <?php } ?>

        <a class="button button-light" href="index.php#catalog">Catalog</a>

    </nav>

</header>

<main>

