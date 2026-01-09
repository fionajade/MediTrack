<?php
include("connect.php");
session_start();
header('Content-Type: application/json');

// Make sure user is logged in
$userID = $_SESSION['userID'] ?? null;
if (!$userID) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(['success' => false, 'error' => 'No data received']);
    exit;
}

$paymentID = $data['paymentID'] ?? '';
$cart = $data['cart'] ?? [];
$total = $data['total'] ?? '';
$name = $data['name'] ?? '';
$contact = $data['contact'] ?? '';
$address = $data['address'] ?? '';

// Validate required fields
if (!$paymentID || !$cart || !$total || !$name || !$contact || !$address) {
    echo json_encode(['success' => false, 'error' => 'Incomplete data']);
    exit;
}

try {
    // Insert into orders table
    $stmt = $pdo->prepare("
        INSERT INTO orders (userID, full_name, contact, address, total_amount, payment_method, payment_id, status)
        VALUES (?, ?, ?, ?, ?, 'PayPal', ?, 'Paid')
    ");
    $stmt->execute([$userID, $name, $contact, $address, $total, $paymentID]);
    $orderID = $pdo->lastInsertId();

    // Insert order items
    $stmtItem = $pdo->prepare("
        INSERT INTO order_items (order_id, medicine_id, price, quantity)
        VALUES (?, ?, ?, ?)
    ");
    foreach ($cart as $item) {
        $stmtItem->execute([
            $orderID,
            $item['medicine_id'],
            $item['price'],
            $item['quantity']
        ]);

        $stmtStock = $pdo->prepare("UPDATE medicines SET quantity = quantity - ? WHERE medicine_id = ?");
        $stmtStock->execute([$item['quantity'], $item['medicine_id']]);
    }

    echo json_encode(['success' => true, 'orderID' => $orderID]);
} catch (PDOException $e) {
    // Log the exact error for debugging
    error_log("Order save error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
