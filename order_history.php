<?php
session_start();
include "connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order History</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2 class="mb-4">🧾 Order History</h2>

  <?php
  $orders = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");

  if ($orders->num_rows > 0):
    while ($order = $orders->fetch_assoc()):
      $order_id = $order['id'];
      $created_at = $order['created_at'];

      $items = $conn->query("
        SELECT m.name, oi.quantity, oi.price
        FROM order_items oi
        JOIN medicines m ON oi.medicine_id = m.id
        WHERE oi.order_id = $order_id
      ");
      ?>

      <div class="card mb-4">
        <div class="card-header">
          <strong>Order #<?= $order_id ?></strong> <span class="text-muted">on <?= $created_at ?></span>
        </div>
        <div class="card-body">
          <table class="table table-sm table-bordered">
            <thead>
              <tr>
                <th>Medicine</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $total = 0;
              while ($item = $items->fetch_assoc()):
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
              ?>
              <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td>₱<?= number_format($item['price'], 2) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>₱<?= number_format($subtotal, 2) ?></td>
              </tr>
              <?php endwhile; ?>
              <tr class="table-secondary">
                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                <td><strong>₱<?= number_format($total, 2) ?></strong></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    <?php endwhile; ?>
  <?php else: ?>
    <p class="text-muted">You have no orders yet.</p>
  <?php endif; ?>
</div>
</body>
</html>
