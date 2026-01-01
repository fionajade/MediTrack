<?php
session_start();
include("connect.php");

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Invalid request method."]);
    exit();
}

if (!isset($_SESSION['userID'])) {
    http_response_code(401);
    echo json_encode(["error" => "You must be logged in to checkout."]);
    exit();
}

$user_id = $_SESSION['userID'];

$data = json_decode(file_get_contents('php://input'), true);

if (!is_array($data) || !isset($data['cart']) || count($data['cart']) === 0) {
    http_response_code(400);
    echo json_encode(["error" => "Cart is empty."]);
    exit();
}

$cart = $data['cart'];


try {
    $pdo->beginTransaction();

    $checkStock = $pdo->prepare("SELECT quantity FROM medicines WHERE medicine_id = ?");
    $updateStock = $pdo->prepare("UPDATE medicines SET quantity = quantity - ? WHERE medicine_id = ?");
    $insertSale = $pdo->prepare("INSERT INTO sales (user_id, medicine_id, quantity, total_price, sale_date) VALUES (?, ?, ?, ?, NOW())");

    foreach ($cart as $item) {
        if (!isset($item['medicine_id'], $item['quantity'], $item['price']) ||
            !is_numeric($item['medicine_id']) || !is_numeric($item['quantity']) || !is_numeric($item['price'])) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid cart data."]);
            exit();
        }

        $medicine_id = (int) $item['medicine_id'];
        $quantity = (int) $item['quantity'];
        $price = (float) $item['price'];
        $subtotal = $quantity * $price;

        if ($quantity <= 0) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid quantity for medicine ID $medicine_id."]);
            exit();
        }

        $checkStock->execute([$medicine_id]);
        $stockRow = $checkStock->fetch();

        if (!$stockRow) {
            $pdo->rollBack();
            http_response_code(404);
            echo json_encode(["error" => "Medicine ID $medicine_id not found."]);
            exit();
        }

        if ($stockRow['quantity'] < $quantity) {
            $pdo->rollBack();
            http_response_code(409);
            echo json_encode(["error" => "Not enough stock for medicine ID $medicine_id."]);
            exit();
        }

        $updateStock->execute([$quantity, $medicine_id]);
        $insertSale->execute([$user_id, $medicine_id, $quantity, $subtotal]);
    }

    $pdo->commit();
    echo json_encode(["success" => true, "message" => "Checkout completed successfully."]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(["error" => "Checkout failed: " . $e->getMessage()]);
}
