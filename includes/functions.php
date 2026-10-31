<?php
declare(strict_types=1);

// Convert special characters to safe HTML
function e($value)
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Display price in Indian Rupees
function money($amount)
{
    return '₹' . number_format((float)$amount, 2);
}

// Get the product image path
function image_url($filename)
{
    if (empty($filename)) {
        return 'https://placehold.co/400x300?text=No+Image';
    }

    $filename = basename($filename);
    $path = __DIR__ . '/../item pictures/' . $filename;

    // Check whether the image exists
    if (file_exists($path)) {
        return 'item%20pictures/' . rawurlencode($filename);
    }

    // Show a default image if the file is missing
    return 'https://placehold.co/400x300?text=Image+Not+Found';
}
