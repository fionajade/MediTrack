<?php
include("connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $pdo->prepare("INSERT INTO suppliers (name, contact_info, category_id) VALUES (?, ?, ?)")
            ->execute([$_POST['name'], $_POST['contact'], $_POST['category']]);
    } elseif (isset($_POST['delete'])) {
        $pdo->prepare("DELETE FROM suppliers WHERE id = ?")->execute([$_POST['id']]);
    }
    header("Location: manage_suppliers.php");
    exit;
}

$suppliers = $pdo->query("SELECT s.id, s.name, s.contact_info, c.name AS category FROM suppliers s JOIN categories c ON s.category_id = c.id")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<h2>Supplier Management</h2>
<form method="post" class="mb-4">
  <input name="name" placeholder="Supplier Name" required class="form-control mb-2">
  <input name="contact" placeholder="Contact Info" required class="form-control mb-2">
  <select name="category" class="form-control mb-2" required>
    <?php foreach ($categories as $cat): ?>
      <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
    <?php endforeach; ?>
  </select>
  <button name="add" class="btn btn-primary">Add Supplier</button>
</form>

<table class="table">
  <thead><tr><th>Name</th><th>Contact</th><th>Category</th><th>Action</th></tr></thead>
  <tbody>
    <?php foreach ($suppliers as $sup): ?>
      <tr>
        <td><?= $sup['name'] ?></td>
        <td><?= $sup['contact_info'] ?></td>
        <td><?= $sup['category'] ?></td>
        <td>
          <form method="post">
            <input type="hidden" name="id" value="<?= $sup['id'] ?>">
            <button name="delete" class="btn btn-danger btn-sm">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
