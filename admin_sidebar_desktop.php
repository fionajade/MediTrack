<link href="admin.css" rel="stylesheet">
<div class="col-lg-2 d-none d-lg-flex flex-column p-4 vh-100 sticky-top">
    <div class="sidebar-brand">Pill-and-Pestle</div>
    <nav class="nav flex-column w-100">
            <a href="admin_dashboard.php"
                class="nav-link <?= ($current_page == 'admin_dashboard.php') ? 'active' : '' ?>">
                Home
            </a>
            <a href="medicines_stock.php"
                class="nav-link <?= ($current_page == 'medicines_stock.php') ? 'active' : '' ?>">
                Medicine Stock
            </a>
            <a href="suppliers.php" class="nav-link <?= ($current_page == 'suppliers.php') ? 'active' : '' ?>">
                Suppliers
            </a>
            <a href="statistics.php" class="nav-link <?= ($current_page == 'statistics.php') ? 'active' : '' ?>">
                Statistics
            </a>
            <a href="reviews.php" class="nav-link <?= ($current_page == 'reviews.php') ? 'active' : '' ?>">
                Reviews
            </a>
    </nav>
    <div class="sidebar-footer">
        <nav class="nav flex-column w-100">
            <a class="nav-link" href="backup.php">Backup</a>
            <a class="nav-link" href="restore.php">Restore</a>
            <a class="nav-link" href="edit_acc_admin.php">Edit Account</a>
            <a class="nav-link" href="logout.php">Log Out</a>
        </nav>
    </div>
</div>