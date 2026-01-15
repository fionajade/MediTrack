<?php
session_start();
include("connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medicineId = $_POST['medicine_id'] ?? null;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $action = $_POST['action'] ?? '';

    if ($medicineId && $quantity > 0 && in_array($action, ['add', 'subtract'])) {
        $stmt = $pdo->prepare("SELECT quantity FROM medicines WHERE medicine_id = ?");
        $stmt->execute([$medicineId]);
        $med = $stmt->fetch();

        if ($med) {
            $currentQty = (int)$med['quantity'];
            $newQty = ($action === 'add') ? $currentQty + $quantity : max(0, $currentQty - $quantity);

            $update = $pdo->prepare("UPDATE medicines SET quantity = ? WHERE medicine_id = ?");
            $update->execute([$newQty, $medicineId]);
        }
    }
}

header("Location: medicines_stock.php");
exit;
?>