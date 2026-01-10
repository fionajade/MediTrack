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

// Get User Name for Header
$displayName = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin';
?>

<?php include 'shared/admin/admin_header.php'; ?>

<body>

    <?php include 'admin_sidebar.php'; ?>

  <!-- MAIN CONTENT -->
  <div class="main-content">

            <!-- Header -->
        <div class="d-flex justify-content-between align-items-end mb-3">
            <div>
                <p class="mb-0 text-muted">Manage Inventory</p>
                <h1 class="page-title">Medicine Stock</h1>
            </div>
        </div>

    <div class="divider-line"></div>

    <!-- CATEGORY NAVBAR (Sticky) -->
    <nav class="cat-navbar d-flex justify-content-center flex-wrap gap-2">
      <?php foreach ($categories as $category): ?>
          <a class="cat-nav-link" href="#category-<?= $category['id'] ?>">
            <?= htmlspecialchars($category['name']) ?>
          </a>
      <?php endforeach; ?>
    </nav>

    <div class="row">
      
      <!-- LEFT COLUMN: Management Forms -->
      <div class="col-lg-4">
        
        <!-- Add Category Form -->
        <div class="light-card">
            <h5>Manage Categories</h5>
            <form method="POST" class="mb-3">
                <div class="input-group">
                    <input type="text" name="category_name" class="form-control" placeholder="New Category Name" required>
                    <button name="add_category" class="btn btn-custom-dark">Add</button>
                </div>
            </form>
            
            <div style="max-height: 200px; overflow-y: auto;">
                <ul class="list-group list-group-flush">
                <?php foreach ($categories as $cat): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                    <?= htmlspecialchars($cat['name']) ?>
                    <form method="POST" onsubmit="return confirm('Delete this category?')" style="margin:0;">
                        <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                        <button name="delete_category" class="btn btn-outline-danger btn-sm rounded-pill" style="font-size: 0.7rem;">Delete</button>
                    </form>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- Add Medicine Form -->
        <div class="light-card">
            <h5>Add New Medicine</h5>
            <form method="POST" class="row g-3">
                <div class="col-12">
                    <input type="text" name="medicine_name" class="form-control" placeholder="Medicine Name" required>
                </div>
                <div class="col-6">
                    <input type="number" name="unit_price" step="0.01" class="form-control" placeholder="Price (₱)" required>
                </div>
                <div class="col-6">
                    <input type="number" name="quantity" class="form-control" placeholder="Qty" required>
                </div>
                <div class="col-12">
                    <label class="form-label text-muted small ms-1">Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-control" required>
                </div>
                <div class="col-12">
                    <select name="category_id" class="form-select" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                    <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <select name="supplier_id" class="form-select" required>
                    <option value="">Select Supplier</option>
                    <?php foreach ($suppliers as $sup): ?>
                        <option value="<?= $sup['id'] ?>"><?= $sup['name'] ?></option>
                    <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 mt-4">
                    <button name="add_medicine" class="btn btn-custom-dark w-100">Add Medicine</button>
                </div>
            </form>
        </div>

      </div>

      <!-- RIGHT COLUMN: Stock Display -->
      <div class="col-lg-8">
        <?php foreach ($categories as $category): ?>
            <div id="category-<?= $category['id'] ?>" class="mb-5">
            <h3 class="med-section-title"><?= htmlspecialchars($category['name']) ?></h3>
            
            <?php
                $stmt = $pdo->prepare("SELECT * FROM medicines WHERE category_id = ?");
                $stmt->execute([$category['id']]);
                $medicines = $stmt->fetchAll();
            ?>

            <?php if ($medicines): ?>
                <div class="row g-3">
                <?php foreach ($medicines as $med): ?>
                    <div class="col-md-6">
                        <div class="medicine-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <h4 class="med-name"><?= htmlspecialchars($med['name']) ?></h4>
                                <span class="badge bg-light text-dark border">ID: <?= $med['medicine_id'] ?></span>
                            </div>
                            
                            <p class="med-info">Price: <span>₱<?= number_format($med['unit_price'], 2) ?></span></p>
                            <p class="med-info">Stock: <span><?= $med['quantity'] ?></span></p>
                            <p class="med-info">Expiry: <span class="<?= (strtotime($med['expiry_date']) < strtotime('+30 days')) ? 'text-danger' : '' ?>"><?= $med['expiry_date'] ?></span></p>
                            
                            <hr class="my-3 opacity-25">
                            
                            <form method="post" action="update_stock.php" class="d-flex align-items-center gap-2">
                                <input type="hidden" name="medicine_id" value="<?= $med['medicine_id'] ?>">
                                <input type="number" name="quantity" class="form-control form-control-sm" style="width: 70px;" min="1" placeholder="Qty" required>
                                <button type="submit" name="action" value="add" class="btn btn-success btn-sm rounded-circle" style="width: 32px; height: 32px; padding: 0;">+</button>
                                <button type="submit" name="action" value="subtract" class="btn btn-danger btn-sm rounded-circle" style="width: 32px; height: 32px; padding: 0;">−</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-light text-center border text-muted">No medicines in this category.</div>
            <?php endif; ?>
            </div>
        <?php endforeach; ?>
      </div>

    </div> 

  </div> 

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>