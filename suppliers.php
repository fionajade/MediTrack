<?php 
$title = "Pill and Pestle | Suppliers"; 
$page_title = "Suppliers"; 

session_start();
include("connect.php");

if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit();
}


$categories = [];
try {
    $categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching categories: " . $e->getMessage());
}

$baseColors = ['#FFDEE9', '#D0F4DE', '#E4C1F9', '#C1E1C1', '#FAD6A5', '#A0CED9', '#FFDAC1', '#D5AAFF'];
$category_colors = [];
$index = 0;
foreach ($categories as $cat) {
    $category_colors[$cat['id']] = $baseColors[$index % count($baseColors)];
    $index++;
}

$suppliers = [];
try {
    $stmt = $pdo->query("
        SELECT suppliers.*, categories.name AS category_name
        FROM suppliers
        LEFT JOIN categories ON suppliers.category_id = categories.id
        ORDER BY categories.name, suppliers.name
    ");
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching suppliers: " . $e->getMessage());
}

$grouped = [];
foreach ($suppliers as $sup) {
    $cat_id = $sup['category_id'] ?? 'uncategorized';
    if (!isset($grouped[$cat_id]))
        $grouped[$cat_id] = [];
    $grouped[$cat_id][] = $sup;
}

$displayName = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin';
?>

<?php include("admin_header.php"); ?>

<body>

  <?php include("admin_sidebar_mobile.php"); ?>


  <div class="container-fluid">
    <div class="row">

      <?php include("admin_sidebar_desktop.php"); ?>


      <main class="col-lg-10 col-12 p-4">
        
        <div class="d-flex justify-content-between align-items-end mb-3">
            <div>
                <p class="page-title-pre">Management</p>
                <h1 class="page-title mb-0">Suppliers</h1>
            </div>
            <button class="btn btn-custom shadow-sm mb-2" data-bs-toggle="modal" data-bs-target="#supplierModal">
                + Add Supplier
            </button>
        </div>
        <hr>

        <div class="row g-4">
            <?php foreach ($categories as $cat): ?>
                <?php
                $cat_id = $cat['id'];
                $cat_name = $cat['name'];
                $color = $category_colors[$cat_id] ?? '#f0f0f0';
                ?>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="category-card">
                        <div class="category-header" style="background-color: <?= $color ?>;">
                            <?= htmlspecialchars($cat_name) ?>
                        </div>

                        <div class="supplier-list-container">
                        <?php if (!empty($grouped[$cat_id])): ?>
                            <?php foreach ($grouped[$cat_id] as $sup): ?>
                                <div class="supplier-item">
                                    <div class="supplier-name"><?= htmlspecialchars($sup['name']) ?></div>
                                    <div class="supplier-detail">
                                        <strong>Addr:</strong> <?= htmlspecialchars($sup['address']) ?>
                                    </div>
                                    <div class="supplier-detail">
                                        <strong>Tel:</strong> <?= htmlspecialchars($sup['contact']) ?>
                                    </div>
                                    <div class="supplier-detail">
                                        <strong>Email:</strong> <?= htmlspecialchars($sup['email']) ?>
                                    </div>
                                    <button class="btn btn-edit-sm" onclick='editSupplier(<?= json_encode($sup) ?>)'>
                                        Edit Details
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="p-4 text-center text-muted small">
                                No suppliers listed for this category.
                            </div>
                        <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="mb-5"></div>
      </main>
    </div>
  </div>

  <div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="save_supplier.php" method="post" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supplierModalLabel">Add Supplier</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="supplier_id" id="supplier_id">

                    <div class="mb-3">
                        <label for="name" class="form-label text-muted small">Supplier Name</label>
                        <input type="text" class="form-control" name="name" id="name" required placeholder="Company Name">
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label text-muted small">Category</label>
                        <select name="category_id" id="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contact" class="form-label text-muted small">Contact Number</label>
                            <input type="text" class="form-control" name="contact" id="contact" required placeholder="0912...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label text-muted small">Email Address</label>
                            <input type="email" class="form-control" name="email" id="email" required placeholder="contact@example.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label text-muted small">Address</label>
                        <textarea class="form-control" name="address" id="address" rows="3" required placeholder="Full office address"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-custom">Save Supplier</button>
                </div>
            </form>
        </div>
    </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
        function editSupplier(supplier) {
            document.getElementById('supplier_id').value = supplier.id;
            document.getElementById('name').value = supplier.name;
            document.getElementById('address').value = supplier.address;
            document.getElementById('contact').value = supplier.contact;
            document.getElementById('email').value = supplier.email;
            document.getElementById('category_id').value = supplier.category_id;

            document.getElementById('supplierModalLabel').innerText = "Edit Supplier";

            const modal = new bootstrap.Modal(document.getElementById('supplierModal'));
            modal.show();
        }

        const myModal = document.getElementById('supplierModal');
        myModal.addEventListener('hidden.bs.modal', function () {
            document.querySelector('form').reset();
            document.getElementById('supplier_id').value = '';
            document.getElementById('supplierModalLabel').innerText = "Add Supplier";
        });
  </script>
</body>

</html>