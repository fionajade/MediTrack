<?php
include("connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $pdo->prepare("INSERT INTO categories (name) VALUES (?)")->execute([$_POST['name']]);
    } elseif (isset($_POST['delete'])) {
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$_POST['id']]);
    }
    header("Location: manage_categories.php");
    exit;
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<h2>Manage Categories</h2>
<form method="post">
  <input name="name" required placeholder="New Category" class="form-control mb-2">
  <button name="add" class="btn btn-primary">Add Category</button>
</form>

<table class="table mt-4">
  <?php foreach ($categories as $cat): ?>
    <tr>
      <td><?= htmlspecialchars($cat['name']) ?></td>
      <td>
        <form method="post">
          <input type="hidden" name="id" value="<?= $cat['id'] ?>">
          <button name="delete" class="btn btn-danger btn-sm">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
