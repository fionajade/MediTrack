<link href="admin.css" rel="stylesheet">
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="mobile-header d-lg-none">
    <button class="hamburger-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
        &#9776;
    </button>
    <div class="fs-5 fw-bold" style="color: var(--primary-navy);">Pill-and-Pestle</div>
</div>

<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title sidebar-brand">Pill-and-Pestle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
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
        <div class="sidebar-footer mt-auto">
            <nav class="nav flex-column w-100">
                <a class="nav-link" href="backup.php">Backup</a>
                <a class="nav-link" href="restore.php">Restore</a>
                <a class="nav-link" href="edit_acc_admin.php">Edit Account</a>
                <a class="nav-link" href="logout.php">Log Out</a>
                </nav>
    </div>
</div>
</div>