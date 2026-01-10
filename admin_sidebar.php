<link href="shared/admin/admin.css" rel="stylesheet">

<?php
// Get the current file name (e.g., "dashboard.php")
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar">
    <a href="dashboard.php" class="brand-logo">Pill and Pestle</a>

    <ul class="nav-links">
        <li class="nav-item">
            <a href="admin_dashboard.php"
                class="nav-link <?= ($current_page == 'admin_dashboard.php') ? 'active' : '' ?>">
                Home
            </a>
        </li>
        <li class="nav-item">
            <a href="medicines_stock.php"
                class="nav-link <?= ($current_page == 'medicines_stock.php') ? 'active' : '' ?>">
                Medicine Stock
            </a>
        </li>
        <li class="nav-item">
            <a href="suppliers.php" class="nav-link <?= ($current_page == 'suppliers.php') ? 'active' : '' ?>">
                Suppliers
            </a>
        </li>
        <li class="nav-item">
            <a href="statistics.php" class="nav-link <?= ($current_page == 'statistics.php') ? 'active' : '' ?>">
                Statistics
            </a>
        </li>
        <li class="nav-item">
            <a href="reviews.php" class="nav-link <?= ($current_page == 'reviews.php') ? 'active' : '' ?>">
                Reviews
            </a>
        </li>
    </ul>

    <ul class="bottom-links">
        <li><a href="backup.php">Backup</a></li>
        <li><a href="restore.php">Restore</a></li>
        <li><a href="edit_account.php">Edit Account</a></li>
        <li><a href="logout.php">Log Out</a></li>
    </ul>
</aside>