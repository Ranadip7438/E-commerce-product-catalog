<?php
session_start();

require 'config/database.php';
require 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
$cart = [];
$items = [];
$total = 0.00;

try {
    // Load the cart.
    if ($userId) {
        $stmt = $mysqli->prepare(
            "SELECT product_id, quantity
             FROM cart_items
             WHERE user_id = ?"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $cart[(int)$row['product_id']] =
                (int)$row['quantity'];
        }
    } else {
        $cart = $_SESSION['cart'] ?? [];
    }

    if (empty($cart)) {
        throw new Exception('Your cart is empty.');
    }

    // Validate cart IDs and quantities.
    foreach ($cart as $productId => $quantity) {
        $productId = filter_var(
            $productId,
            FILTER_VALIDATE_INT
        );
        $quantity = filter_var(
            $quantity,
            FILTER_VALIDATE_INT
        );

        if (!$productId || $productId < 1 ||
            !$quantity || $quantity < 1 || $quantity > 99) {
            throw new Exception('Your cart contains an invalid item.');
        }
    }

    // Start a transaction and lock products while checking stock.
    $mysqli->begin_transaction();

    $ids = array_map('intval', array_keys($cart));
    sort($ids, SORT_NUMERIC);

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    $stmt = $mysqli->prepare(
        "SELECT id, name, price, stock
         FROM products
         WHERE id IN ($placeholders)
         ORDER BY id
         FOR UPDATE"
    );

    $stmt->bind_param($types, ...$ids);
    $stmt->execute();

    $result = $stmt->get_result();

    while ($product = $result->fetch_assoc()) {
        $productId = (int)$product['id'];
        $quantity = (int)$cart[$productId];

        if ((int)$product['stock'] < $quantity) {
            throw new Exception(
                'Not enough stock for ' . $product['name'] .
                '. Please update your cart.'
            );
        }

        $product['quantity'] = $quantity;
        $product['price'] = (float)$product['price'];

        $items[] = $product;
        $total += $product['price'] * $quantity;
    }

    if (count($items) !== count($cart)) {
        throw new Exception(
            'One or more products are no longer available.'
        );
    }

    // MEMBERS: save the order and order items.
    if ($userId) {
        $stmt = $mysqli->prepare(
            "INSERT INTO orders (user_id, total_amount, status)
             VALUES (?, ?, 'completed')"
        );
        $stmt->bind_param("id", $userId, $total);
        $stmt->execute();

        $orderId = $mysqli->insert_id;

        $itemStmt = $mysqli->prepare(
            "INSERT INTO order_items
             (order_id, product_id, product_name, unit_price, quantity)
             VALUES (?, ?, ?, ?, ?)"
        );

        $stockStmt = $mysqli->prepare(
            "UPDATE products
             SET stock = stock - ?
             WHERE id = ? AND stock >= ?"
        );

        foreach ($items as $item) {
            $productId = (int)$item['id'];
            $productName = $item['name'];
            $price = $item['price'];
            $quantity = $item['quantity'];

            $itemStmt->bind_param(
                "iisdi",
                $orderId,
                $productId,
                $productName,
                $price,
                $quantity
            );
            $itemStmt->execute();

            $stockStmt->bind_param(
                "iii",
                $quantity,
                $productId,
                $quantity
            );
            $stockStmt->execute();

            if ($stockStmt->affected_rows !== 1) {
                throw new Exception(
                    'Stock changed during checkout. Please try again.'
                );
            }
        }

        // Clear the member cart inside the transaction.
        $stmt = $mysqli->prepare(
            "DELETE FROM cart_items WHERE user_id = ?"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $mysqli->commit();

        // Clear the session cart only after successful commit.
        unset($_SESSION['cart']);

        $_SESSION['purchase_confirmation'] = [
            'order_id' => $orderId,
            'total' => $total,
            'guest' => false
        ];
    } else {
        // Guest checkout is a demo: no order is saved and stock
        // is not changed. Commit the successful stock validation.
        $mysqli->commit();

        // Clear the guest cart only after successful validation.
        unset($_SESSION['cart']);

        $_SESSION['purchase_confirmation'] = [
            'order_id' => null,
            'total' => $total,
            'guest' => true
        ];
    }

    header('Location: order_confirmation.php');
    exit;

} catch (Throwable $e) {
    try {
        $mysqli->rollback();
    } catch (Throwable $ignored) {
        // A transaction might not have started.
    }

    $_SESSION['checkout_error'] =
        $e instanceof mysqli_sql_exception
        ? 'Checkout could not be completed. Please try again.'
        : $e->getMessage();

    header('Location: cart.php');
    exit;
}
