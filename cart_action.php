<?php
session_start();

require 'config/database.php';
require 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit;
}

$action = $_POST['action'] ?? '';
$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

if (!$productId || $productId < 1) {
    header('Location: index.php');
    exit;
}

// Check that the product exists and get its stock.
$stmt = $mysqli->prepare(
    "SELECT id, stock FROM products WHERE id = ? LIMIT 1"
);
$stmt->bind_param("i", $productId);
$stmt->execute();

$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header('Location: index.php');
    exit;
}

$stock = (int) $product['stock'];
$userId = $_SESSION['user_id'] ?? null;

// Create a guest cart if one does not exist.
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ADD TO CART
if ($action === 'add') {
    $quantity = max(1, (int) $quantity);

    if ($stock < 1) {
        header('Location: cart.php');
        exit;
    }

    if ($userId) {
        // Check existing quantity for this member.
        $stmt = $mysqli->prepare(
            "SELECT quantity FROM cart_items
             WHERE user_id = ? AND product_id = ?"
        );
        $stmt->bind_param("ii", $userId, $productId);
        $stmt->execute();

        $existing = $stmt->get_result()->fetch_assoc();
        $oldQuantity = $existing ? (int) $existing['quantity'] : 0;

        $newQuantity = min(
            $oldQuantity + $quantity,
            $stock,
            99
        );

        $stmt = $mysqli->prepare(
            "INSERT INTO cart_items (user_id, product_id, quantity)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)"
        );
        $stmt->bind_param(
            "iii",
            $userId,
            $productId,
            $newQuantity
        );
        $stmt->execute();
    } else {
        $oldQuantity = (int) ($_SESSION['cart'][$productId] ?? 0);

        $_SESSION['cart'][$productId] = min(
            $oldQuantity + $quantity,
            $stock,
            99
        );
    }
}

// UPDATE QUANTITY
elseif ($action === 'update') {
    if (!$quantity || $quantity < 1 || $stock < 1) {
        $action = 'remove';
    } else {
        $quantity = min($quantity, $stock, 99);

        if ($userId) {
            $stmt = $mysqli->prepare(
                "UPDATE cart_items
                 SET quantity = ?
                 WHERE user_id = ? AND product_id = ?"
            );
            $stmt->bind_param(
                "iii",
                $quantity,
                $userId,
                $productId
            );
            $stmt->execute();
        } elseif (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] = $quantity;
        }
    }
}

// REMOVE ITEM
if ($action === 'remove') {
    if ($userId) {
        $stmt = $mysqli->prepare(
            "DELETE FROM cart_items
             WHERE user_id = ? AND product_id = ?"
        );
        $stmt->bind_param("ii", $userId, $productId);
        $stmt->execute();
    } else {
        unset($_SESSION['cart'][$productId]);
    }
}

header('Location: cart.php');
exit;
