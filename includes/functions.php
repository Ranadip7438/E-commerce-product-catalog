<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function money(float|string $amount): string
{
    return '₹' . number_format((float) $amount, 2);
}

function image_url(?string $filename): string
{
    $filename = trim((string) $filename);
    if ($filename === '') {
        return 'https://placehold.co/800x600/f3f5f7/697386?text=Product+Image';
    }

    $safeName = basename($filename);
    $path = __DIR__ . '/../item pictures/' . $safeName;
    if (!is_file($path)) {
        return 'https://placehold.co/800x600/f3f5f7/697386?text=Image+Not+Found';
    }

    return 'item%20pictures/' . rawurlencode($safeName);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_is_valid(): bool
{
    $posted = $_POST['csrf_token'] ?? '';
    return is_string($posted)
        && isset($_SESSION['csrf_token'])
        && is_string($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $posted);
}
