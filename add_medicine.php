<?php
include("connect.php");
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$suppliers = $pdo->query("SELECT * FROM suppliers")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("INSERT INTO medicines (name, unit_price, quantity, expiry_date, category_id, supplier_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['name'], $_POST['price'], $_POST['quantity'],
        $_POST['expiry'], $_POST['category'], $_POST['supplier']
    ]);
    header("Location: view_medicines.php");
    exit;
}
?>
<form method="post">
  <input name="name" placeholder="Medicine Name" class="form-control" required>
  <input name="price" type="number" step="0.01" class="form-control" placeholder="Price" required>
  <input name="quantity" type="number" class="form-control" placeholder="Stock" required>
  <input name="expiry" type="date" class="form-control" required>
  <select name="category" class="form-control" required>
    <?php foreach($categories as $cat): ?>
      <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
    <?php endforeach; ?>
  </select>
  <select name="supplier" class="form-control" required>
    <?php foreach($suppliers as $sup): ?>
      <option value="<?= $sup['id'] ?>"><?= $sup['name'] ?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit" class="btn btn-success mt-2">Add</button>
</form>
