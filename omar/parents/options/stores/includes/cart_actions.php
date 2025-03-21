<?php

use Classes\DataBase\DB;
// Initialize cart if needed
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions
if (isset($_POST['add_to_cart']) && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $product = DB::table('store_items')->where("id", $product_id)->first();
    $option_index = isset($_POST['option_index']) ? intval($_POST['option_index']) : -1;

    // Generate cart key
    $cart_key = $product_id;
    if ($option_index >= 0) {
        $cart_key = $product_id . '_' . $option_index;
    }

    // Check if we can add this item (respect buy_multiple setting)
    $can_add = true;
    if (!$product->buy_multiple && isset($_SESSION['cart'][$cart_key])) {
        // Item is already in cart and doesn't allow multiples
        $can_add = false;
    }

    if ($can_add) {
        // Set quantity based on buy_multiple setting
        $quantity = 1;
        if ($product && $product->buy_multiple) {
            $quantity = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;
        }

        if (isset($_SESSION['cart'][$cart_key])) {
            $_SESSION['cart'][$cart_key]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$cart_key] = [
                'product_id' => $product_id,
                'quantity' => $quantity,
                'option_index' => $option_index
            ];
        }
    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

if (isset($_POST['remove_from_cart']) && isset($_POST['cart_key'])) {
    $cart_key = $_POST['cart_key'];
    unset($_SESSION['cart'][$cart_key]);

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Handle quantity updates
if (isset($_POST['update_quantity']) && isset($_POST['cart_key']) && isset($_POST['quantity'])) {
    $cart_key = $_POST['cart_key'];
    $new_quantity = max(1, intval($_POST['quantity']));

    if (isset($_SESSION['cart'][$cart_key])) {
        $_SESSION['cart'][$cart_key]['quantity'] = $new_quantity;
    }

    // Return JSON response for AJAX requests
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        $product_id = $_SESSION['cart'][$cart_key]['product_id'];
        $product = DB::table('store_items')->where("id", $product_id)->first();
        $option_index = $_SESSION['cart'][$cart_key]['option_index'];

        $price = $product->price;
        if ($option_index >= 0 && !empty($product->options)) {
            $options = json_decode($product->options, true);
            if (isset($options[$option_index]) && !is_null($options[$option_index]['price'])) {
                $price = $options[$option_index]['price'];
            }
        }

        $subtotal = $price * $new_quantity;

        echo json_encode([
            'success' => true,
            'quantity' => $new_quantity,
            'subtotal' => $subtotal,
            'subtotal_formatted' => '$' . number_format($subtotal, 2)
        ]);
        exit;
    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}
