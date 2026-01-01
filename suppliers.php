<?php
session_start();
include("connect.php");

// Fetch categories
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Assign a unique color to each category
$baseColors = ['#FFDEE9', '#D0F4DE', '#E4C1F9', '#C1E1C1', '#FAD6A5', '#A0CED9', '#FFDAC1', '#D5AAFF'];
$category_colors = [];
$index = 0;
foreach ($categories as $cat) {
    $category_colors[$cat['id']] = $baseColors[$index % count($baseColors)];
    $index++;
}

// Fetch suppliers and join with category
$stmt = $pdo->query("
    SELECT suppliers.*, categories.name AS category_name
    FROM suppliers
    LEFT JOIN categories ON suppliers.category_id = categories.id
    ORDER BY categories.name, suppliers.name
");
$suppliers = $stmt->fetchAll();

// Group suppliers by category
$grouped = [];
foreach ($suppliers as $sup) {
    $cat_id = $sup['category_id'] ?? 'uncategorized';
    if (!isset($grouped[$cat_id])) $grouped[$cat_id] = [];
    $grouped[$cat_id][] = $sup;
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Suppliers - Admin</title>
  <link rel="icon" href="assets/medi_logo.png">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --font-family: 'Poppins', sans-serif;
      --main-color: #052241;
    }
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f4f6f8;
      color: var(--main-color);
    }
    .category-card {
      font-family: 'Poppins', sans-serif;
      border-radius: 1rem;
      padding: 1.5rem;
      box-shadow: 0 5px 20px rgba(0,0,0,0.05);
      background: white;
      height: 100%;
      border-left: 5px solid var(--main-color);
    }
    .category-title {
      font-family: 'Poppins', sans-serif;
      font-weight: bold;
      font-size: 1.4rem;
      margin-bottom: 1rem;
      color: var(--main-color);
    }
    .supplier-info {
      font-family: 'Poppins', sans-serif;
      font-size: 0.95rem;
      margin-bottom: 0.3rem;
    }
    .btn-success {
      font-family: 'Poppins', sans-serif;
      background-color: var(--main-color) !important;
      border-color: var(--main-color) !important;
    }
    .btn-outline-primary {
      font-family: 'Poppins', sans-serif;
      color: var(--main-color);
      border-color: var(--main-color);
    }
    .btn-outline-primary:hover {
      background-color: var(--main-color);
      color: white;
    }
    .modal-header {
      font-family: 'Poppins', sans-serif;
      background-color: var(--main-color);
      color: white;
    }
    .form-label, .modal-title {
      font-family: 'Poppins', sans-serif;
      color: var(--main-color);
    }
    .btn-primary {
      font-family: 'Poppins', sans-serif;
      background-color: var(--main-color);
      border-color: var(--main-color);
    }
    .btn-primary:hover {
      background-color: #04192e;
      border-color: #04192e;
    }
  </style>
</head>
<body>
<?php include('admin_sidebar.php'); ?>
<main class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Suppliers</h1>
    <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#supplierModal">+ Add Supplier</button>
  </div>

  <div class="row row-cols-1 row-cols-md-3 g-4">
    <?php foreach ($categories as $cat): ?>
      <?php
        $cat_id = $cat['id'];
        $cat_name = strtoupper($cat['name']);
        $color = $category_colors[$cat_id];
      ?>
      <div class="col">
        <div class="category-card" style="background-color: <?= $color ?>;">
          <div class="category-title"><?= htmlspecialchars($cat_name) ?></div>
          <?php if (!empty($grouped[$cat_id])): ?>
            <?php foreach ($grouped[$cat_id] as $sup): ?>
              <div class="supplier-info"><strong>Name:</strong> <?= htmlspecialchars($sup['name']) ?></div>
              <div class="supplier-info"><strong>Address:</strong> <?= htmlspecialchars($sup['address']) ?></div>
              <div class="supplier-info"><strong>Contact:</strong> <?= htmlspecialchars($sup['contact']) ?></div>
              <div class="supplier-info"><strong>Email:</strong> <?= htmlspecialchars($sup['email']) ?></div>
              <button class="btn btn-sm btn-outline-primary mt-2" onclick='editSupplier(<?= json_encode($sup) ?>)'>Edit</button>
              <hr>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="text-muted">No suppliers in this category.</p>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<!-- Add/Edit Supplier Modal -->
<div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="save_supplier.php" method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="supplierModalLabel">Add/Edit Supplier</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="supplier_id" id="supplier_id">
        <div class="mb-3">
          <label for="name" class="form-label">Supplier Name</label>
          <input type="text" class="form-control" name="name" id="name" required>
        </div>
        <div class="mb-3">
          <label for="address" class="form-label">Address</label>
          <textarea class="form-control" name="address" id="address" required></textarea>
        </div>
        <div class="mb-3">
          <label for="contact" class="form-label">Contact Number</label>
          <input type="text" class="form-control" name="contact" id="contact" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" name="email" id="email" required>
        </div>
        <div class="mb-3">
          <label for="category_id" class="form-label">Category</label>
          <select name="category_id" id="category_id" class="form-select" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save Supplier</button>
      </div>
    </form>
  </div>
</div>

<script>
function editSupplier(supplier) {
  document.getElementById('supplier_id').value = supplier.id;
  document.getElementById('name').value = supplier.name;
  document.getElementById('address').value = supplier.address;
  document.getElementById('contact').value = supplier.contact;
  document.getElementById('email').value = supplier.email;
  document.getElementById('category_id').value = supplier.category_id;

  new bootstrap.Modal(document.getElementById('supplierModal')).show();
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
