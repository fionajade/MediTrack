<?php
session_start();
include("connect.php");

// Fetch categories
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Assign a unique color to each category (Your original logic)
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

<?php include 'shared/admin/admin_header.php'; ?>

<body>

    <?php include 'admin_sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-end mb-3">
            <div>
                <p class="mb-0 text-muted">Management</p>
                <h1 class="page-title">Suppliers</h1>
            </div>
            <button class="btn btn-custom shadow-sm" data-bs-toggle="modal" data-bs-target="#supplierModal">
                + Add Supplier
            </button>
        </div>

        <div class="divider-line"></div>

        <!-- Cards Grid -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
            <?php foreach ($categories as $cat): ?>
                <?php
                $cat_id = $cat['id'];
                $cat_name = $cat['name'];
                $color = $category_colors[$cat_id];
                // Create a lighter version for background using opacity
                // Note: In production, better to use RGBA generator, but here we use the color as header
                ?>
                <div class="col">
                    <div class="category-wrapper">
                        <!-- Colored Header Strip -->
                        <div class="category-header" style="background-color: <?= $color ?>;">
                            <?= htmlspecialchars($cat_name) ?>
                        </div>
                        
                        <!-- List Suppliers -->
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
                                    <button class="btn btn-edit" onclick='editSupplier(<?= json_encode($sup) ?>)'>
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
            <?php endforeach; ?>
        </div>
        
        <div style="height: 50px;"></div>
    </div>

    <!-- Add/Edit Supplier Modal -->
    <div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="save_supplier.php" method="post" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supplierModalLabel">Add/Edit Supplier</h5>
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
                <div class="modal-footer border-top-0 pt-0 pe-4 pb-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
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
            
            // Update Modal Title
            document.getElementById('supplierModalLabel').innerText = "Edit Supplier";

            const modal = new bootstrap.Modal(document.getElementById('supplierModal'));
            modal.show();
        }

        // Reset modal on close (optional, keeps UI clean)
        const myModal = document.getElementById('supplierModal');
        myModal.addEventListener('hidden.bs.modal', function () {
            document.querySelector('form').reset();
            document.getElementById('supplier_id').value = '';
            document.getElementById('supplierModalLabel').innerText = "Add Supplier";
        });
    </script>
</body>

</html>