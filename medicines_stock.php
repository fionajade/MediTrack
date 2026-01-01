<?php
session_start();
include("connect.php");

// Fetch categories and suppliers
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$suppliers = $pdo->query("SELECT * FROM suppliers ORDER BY name")->fetchAll();

// Add Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['category_name']);
    if ($name !== '') {
        $pdo->prepare("INSERT INTO categories (name) VALUES (?)")->execute([$name]);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Delete Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_category'])) {
    $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$_POST['category_id']]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Add Medicine
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_medicine'])) {
    $stmt = $pdo->prepare("INSERT INTO medicines (name, unit_price, quantity, expiry_date, category_id, supplier_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['medicine_name'], $_POST['unit_price'], $_POST['quantity'],
        $_POST['expiry_date'], $_POST['category_id'], $_POST['supplier_id']
    ]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Medicines Stock</title>
  <link rel="icon" href="assets/medi_logo.png">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --primary: #052241;
      --background: #f4f6fa;
      --white: #ffffff;
      --border-radius: 15px;
    }

    body {
      background-color: var(--background);
      font-family: 'Poppins', sans-serif;
    }

    .main-content {
      font-family: 'Poppins', sans-serif;
      padding: 110px 30px 30px 30px;
      margin-left: 250px;
    }

    h2 {
      font-family: 'Poppins', sans-serif;
      font-weight: bolder;
      color: var(--primary);
      text-align: center;
      margin-bottom: 40px;
    }

    .navbar {
      font-family: 'Poppins', sans-serif;
      background-color: var(--primary);
    }

    .navbar-brand,
    .nav-link {
      color: white !important;
    }

    .category-card {
      font-family: 'Poppins', sans-serif;
      background-color: var(--white);
      border-radius: var(--border-radius);
      padding: 30px;
      margin-bottom: 50px;
      box-shadow: 0 4px 16px rgba(5, 34, 65, 0.05);
      border-left: 5px solid var(--primary);
    }

    .category-title {
      font-family: 'Poppins', sans-serif;
      color: var(--primary);
      font-size: 1.6rem;
      font-weight: bold;
      border-bottom: 2px solid #ccc;
      margin-bottom: 20px;
    }

    .medicine-card {
      font-family: 'Poppins', sans-serif;
      background: #ffffff;
      border: none;
      border-radius: var(--border-radius);
      box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
      transition: 0.3s ease;
    }

    .medicine-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 20px rgba(5, 34, 65, 0.15);
    }

    .medicine-card .card-body {
      padding: 25px;
    }

    .medicine-card .card-title {
      font-family: 'Poppins', sans-serif;
      color: var(--primary);
      font-weight: bold;
      font-size: 1.2rem;
    }

    .form-control,
    .form-select {
      border-radius: 8px;
    }

    .btn-primary {
      font-family: 'Poppins', sans-serif;
      background-color: var(--primary);
      border-color: var(--primary);
    }

    .btn-primary:hover {
      background-color: #02172c;
    }

    .btn-success {
      font-family: 'Poppins', sans-serif;
      background-color: #198754;
      border-color: #198754;
    }

    .btn-danger {
      background-color: #dc3545;
      border-color: #dc3545;
    }

    ul.list-group li {
      font-family: 'Poppins', sans-serif;
      border: none;
      background-color: #fff;
      margin-bottom: 5px;
      border-left: 4px solid var(--primary);
      border-radius: 8px;
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        padding-top: 80px;
      }
    }
    html {
    scroll-behavior: smooth;
    }

  </style>
</head>
<body>
  <?php include('admin_sidebar.php'); ?>

  <!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background-color: #052241; margin-left: 220px; width: calc(100% - 220px); z-index: 999;">
  <div class="container-fluid justify-content-center">

    <!-- Centered Brand -->
    <a class="navbar-brand fw-bold mx-3" href="#">Categories</a>

    <!-- Toggler for mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#categoryNavbar" aria-controls="categoryNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Centered nav links -->
    <div class="collapse navbar-collapse justify-content-center" id="categoryNavbar">
      <ul class="navbar-nav">
        <?php foreach ($categories as $category): ?>
          <li class="nav-item">
            <a class="nav-link px-3" href="#category-<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

  </div>
</nav>





  <!-- Main Content -->
  <div class="main-content">
    <div class="container">
      <h2>Medicines Stock Per Category</h2>

      <!-- Manage Categories -->
      <div class="mb-5">
        <h5 class="text-primary">Manage Categories</h5>
        <form method="POST" class="d-flex align-items-center gap-2 mb-3">
          <input type="text" name="category_name" class="form-control" placeholder="New Category" required>
          <button name="add_category" class="btn btn-primary">Add</button>
        </form>
        <?php if ($categories): ?>
          <ul class="list-group">
            <?php foreach ($categories as $cat): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <?= htmlspecialchars($cat['name']) ?>
                <form method="POST" onsubmit="return confirm('Delete this category?')">
                  <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                  <button name="delete_category" class="btn btn-danger btn-sm">Delete</button>
                </form>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <!-- Add Medicine -->
      <div class="mb-5">
        <h5 class="text-success">Add New Medicine</h5>
        <form method="POST" class="row g-3">
          <div class="col-md-4">
            <input type="text" name="medicine_name" class="form-control" placeholder="Medicine Name" required>
          </div>
          <div class="col-md-2">
            <input type="number" name="unit_price" step="0.01" class="form-control" placeholder="Price" required>
          </div>
          <div class="col-md-2">
            <input type="number" name="quantity" class="form-control" placeholder="Qty" required>
          </div>
          <div class="col-md-2">
            <input type="date" name="expiry_date" class="form-control" required>
          </div>
          <div class="col-md-2">
            <select name="category_id" class="form-select" required>
              <option value="">Category</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <select name="supplier_id" class="form-select" required>
              <option value="">Supplier</option>
              <?php foreach ($suppliers as $sup): ?>
                <option value="<?= $sup['id'] ?>"><?= $sup['name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <button name="add_medicine" class="btn btn-success w-100">Add</button>
          </div>
        </form>
      </div>

      <!-- Display Medicines Per Category -->
      <?php foreach ($categories as $category): ?>
        <div id="category-<?= $category['id'] ?>" class="category-card">
          <h4 class="category-title"><?= htmlspecialchars($category['name']) ?></h4>
          <?php
            $stmt = $pdo->prepare("SELECT * FROM medicines WHERE category_id = ?");
            $stmt->execute([$category['id']]);
            $medicines = $stmt->fetchAll();
          ?>
          <?php if ($medicines): ?>
            <div class="row">
              <?php foreach ($medicines as $med): ?>
                <div class="col-md-4 mb-4">
                  <div class="card medicine-card">
                    <div class="card-body">
                      <h5 class="card-title"><?= htmlspecialchars($med['name']) ?></h5>
                      <p><strong>Price:</strong> ₱<?= number_format($med['unit_price'], 2) ?></p>
                      <p><strong>Stock:</strong> <?= $med['quantity'] ?></p>
                      <p><strong>Expiry:</strong> <?= $med['expiry_date'] ?></p>
                      <form method="post" action="update_stock.php" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="medicine_id" value="<?= $med['medicine_id'] ?>">
                        <input type="number" name="quantity" class="form-control form-control-sm" min="1" required>
                        <button type="submit" name="action" value="add" class="btn btn-success btn-sm">+</button>
                        <button type="submit" name="action" value="subtract" class="btn btn-danger btn-sm">−</button>
                      </form>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-muted"><em>No medicines found under this category.</em></p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

