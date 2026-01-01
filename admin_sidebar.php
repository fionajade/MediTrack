<!-- sidebar.php -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="partials/style.css">

<?php
  $currentPage = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
  <h2>MediTrack</h2>
  <a href="admin_dashboard.php" class="<?= $currentPage == 'admin_dashboard.php' ? 'active' : '' ?>">Home</a>
  <a href="medicines_stock.php" class="<?= $currentPage == 'medicines_stock.php' ? 'active' : '' ?>">Medicine Stock</a>
  <a href="suppliers.php" class="<?= $currentPage == 'suppliers.php' ? 'active' : '' ?>">Suppliers</a>
  <a href="statistics.php" class="<?= $currentPage == 'statistics.php' ? 'active' : '' ?>">Statistics</a>

  <div class="settings-section">
    <a href="backup.php">Back up</a>
    <a href="#" data-bs-toggle="modal" data-bs-target="#restoreModal">Restore</a>
    <a href="edit_acc_admin.php">Edit Account</a>
    <a href="logout.php">Log Out</a>
  </div>
</div>

<!-- 🟡 Restore Modal placed outside sidebar -->
<div class="modal fade" id="restoreModal" tabindex="-1" aria-labelledby="restoreModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-danger">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="restoreModalLabel">Restore Database</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form method="POST" enctype="multipart/form-data" action="process_restore.php">
        <div class="modal-body">
          <p class="mb-2">Select a `.sql` file to restore your database:</p>
          <input type="file" name="sql_file" class="form-control" accept=".sql" required>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Restore Now</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ✅ Bootstrap JS should be outside the sidebar too -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
