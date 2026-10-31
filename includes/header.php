<?php
$pageTitle = $pageTitle ?? 'Electro — Everyday technology';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Browse electronics, compare products, and find your next everyday essential.">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="index.php" aria-label="Electro home"><span class="brand-mark">e</span> electro<span class="brand-dot">.</span></a>
    <nav class="header-nav" aria-label="Main navigation">
        <span class="nav-note">Electronics, made simple</span>
        <a href="index.php#catalog">Catalog</a>
    </nav>
</header>
<main>
