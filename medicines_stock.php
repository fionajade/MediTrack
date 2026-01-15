<?php 
$title = "Medicine Stock"; 
$page_title = "Medicine Stock"; 

session_start();
include("connect.php");

if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['category_name']);
    if ($name !== '') {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$name]);
            header("Location: " . $_SERVER['PHP_SELF']); 
            exit;
        } catch (PDOException $e) {
            $error_msg = "Error adding category: " . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_category'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$_POST['category_id']]);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (PDOException $e) {
        $error_msg = "Cannot delete category (it likely contains medicines).";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_medicine'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO medicines (name, unit_price, quantity, expiry_date, category_id, supplier_id, unit) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $unit = $_POST['unit'] ?? 'Box'; 
        
        $stmt->execute([
            $_POST['medicine_name'],
            $_POST['unit_price'],
            $_POST['quantity'],
            $_POST['expiry_date'],
            $_POST['category_id'],
            $_POST['supplier_id'],
            $unit
        ]);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (PDOException $e) {
        $error_msg = "Error adding medicine: " . $e->getMessage();
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$suppliers = $pdo->query("SELECT * FROM suppliers ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include("admin_header.php"); ?>


<body>

  <?php include("admin_sidebar_mobile.php"); ?>


  <div class="container-fluid">
    <div class="row">

      <?php include("admin_sidebar_desktop.php"); ?>


      <main class="col-lg-10 col-12 p-4">
        
        <p class="page-title-pre">Inventory Management</p>
        <h1 class="page-title">Medicine Stock</h1>
        <hr>

        <?php if(isset($error_msg)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <div class="row">

            <div class="col-lg-4 col-12">

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
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-2">
                                    <span style="color: var(--text-gray); font-weight:500;">
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </span>
                                    <form method="POST" onsubmit="return confirm('Delete category: <?= addslashes($cat['name']) ?>?')" style="margin:0;">
                                        <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                                        <button name="delete_category" class="btn btn-outline-danger btn-sm py-0 px-2" style="font-size: 0.7rem;">Del</button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="light-card">
                    <h5>Add New Medicine</h5>
                    <form method="POST" class="row g-3">
                        <div class="col-12">
                            <label class="form-label small text-muted mb-1">Medicine Name</label>
                            <input type="text" name="medicine_name" class="form-control" placeholder="e.g. Paracetamol" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted mb-1">Price (₱)</label>
                            <input type="number" name="unit_price" step="0.01" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted mb-1">Quantity</label>
                            <input type="number" name="quantity" class="form-control" placeholder="0" required>
                        </div>
                        <div class="col-12">
                             <label class="form-label small text-muted mb-1">Unit</label>
                             <input type="text" name="unit" class="form-control" placeholder="e.g. Box/Strip">
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-muted mb-1">Expiry Date</label>
                            <input type="date" name="expiry_date" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-muted mb-1">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-muted mb-1">Supplier</label>
                            <select name="supplier_id" class="form-select" required>
                                <option value="">Select Supplier</option>
                                <?php foreach ($suppliers as $sup): ?>
                                    <option value="<?= $sup['id'] ?>"><?= htmlspecialchars($sup['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 mt-4">
                            <button name="add_medicine" class="btn btn-custom-dark w-100">Add Medicine</button>
                        </div>
                    </form>
                </div>

            </div>

            <div class="col-lg-8 col-12">
                
                <div class="cat-navbar">
                    <?php foreach ($categories as $category): ?>
                        <a class="cat-nav-link" href="#category-<?= $category['id'] ?>">
                            <?= htmlspecialchars($category['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <?php foreach ($categories as $category): ?>
                    <div id="category-<?= $category['id'] ?>" class="mb-5">
                        <h3 class="med-section-title"><?= htmlspecialchars($category['name']) ?></h3>

                        <?php
                        $stmt = $pdo->prepare("SELECT * FROM medicines WHERE category_id = ? ORDER BY name");
                        $stmt->execute([$category['id']]);
                        $medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>

                        <?php if ($medicines): ?>
                            <div class="row g-3">
                                <?php foreach ($medicines as $med): ?>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="medicine-card">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h4 class="med-name" title="<?= htmlspecialchars($med['name']) ?>">
                                                    <?= htmlspecialchars($med['name']) ?>
                                                </h4>
                                                <span class="badge bg-light text-secondary border">#<?= $med['medicine_id'] ?></span>
                                            </div>

                                            <div class="med-info">
                                                Price: <span>₱<?= number_format($med['unit_price'], 2) ?></span>
                                            </div>
                                            <div class="med-info">
                                                Stock: <span class="<?= $med['quantity'] < 20 ? 'text-danger' : 'text-success' ?>">
                                                    <?= $med['quantity'] ?>
                                                </span>
                                            </div>
                                            <div class="med-info">
                                                Expiry: <span class="<?= (strtotime($med['expiry_date']) < strtotime('+30 days')) ? 'text-danger fw-bold' : '' ?>">
                                                    <?= $med['expiry_date'] ?>
                                                </span>
                                            </div>

                                            <hr class="my-3 opacity-25">

                                            <form method="post" action="update_stock.php" class="d-flex align-items-center gap-2 mt-2">
                                                <input type="hidden" name="medicine_id" value="<?= $med['medicine_id'] ?>">
                                                <input type="number" name="quantity" class="form-control form-control-sm"
                                                    style="width: 70px;" min="1" placeholder="Qty" required>
                                                
                                                <button type="submit" name="action" value="add" title="Add Stock"
                                                    class="btn btn-success btn-sm rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 32px; height: 32px;">+</button>
                                                
                                                <button type="submit" name="action" value="subtract" title="Reduce Stock"
                                                    class="btn btn-danger btn-sm rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 32px; height: 32px;">−</button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-light text-center border text-muted small">
                                No medicines available in this category.
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>