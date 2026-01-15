<?php
$title = "Restore Database";
include("admin_header.php");

$current_page = basename($_SERVER['PHP_SELF']);  // For active nav link
$displayName = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin';
// Restore logic
$restoreMessage = '';
$restoreSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['sql_file'])) {
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'pill-and-pestle';
    $mysql = 'C:\\xampp\\mysql\\bin\\mysql.exe';

    if ($_FILES['sql_file']['error'] !== UPLOAD_ERR_OK) {
        $restoreMessage = '❌ SQL file upload failed.';
    } else {
        $sql_file = $_FILES['sql_file']['tmp_name'];

        $command = "\"$mysql\" -h $db_host -u $db_user " .
            ($db_pass !== '' ? "-p$db_pass " : '') .
            "\"$db_name\" < \"$sql_file\" 2>&1";

        exec($command, $output, $result);

        if ($result === 0) {
            $restoreMessage = '✅ Database restored successfully!';
            $restoreSuccess = true;
        } else {
            $restoreMessage = "<pre>❌ Restore failed.\n\nCommand:\n$command\n\nOutput:\n" .
                implode("\n", $output) . "</pre>";
        }
    }
}
?>

<body>

    <?php include("admin_sidebar_mobile.php"); ?>

    <div class="container-fluid">
        <div class="row">
            <?php include("admin_sidebar_desktop.php"); ?>

            <main class="col-lg-10 col-12 p-4">
                <p class="page-title-pre">Welcome back, <?= $displayName ?></p>
                <h1 class="page-title">Restore Database</h1>
                <hr>

                <?php if ($restoreMessage): ?>
                    <div class="alert <?= $restoreSuccess ? 'alert-success' : 'alert-danger' ?>" role="alert">
                        <?= $restoreMessage ?>
                    </div>
                <?php endif; ?>

                <div class="dark-card" style="max-width: 100%; height: auto; min-height: 120px; padding: 20px;">
                    <form method="post" enctype="multipart/form-data" onsubmit="return confirm('This will overwrite the database. Continue?');">
                        <label for="sql_file" class="form-label fw-semibold" style="color: var(--text-light);">Select SQL file to restore:</label>
                        <input type="file" id="sql_file" name="sql_file" accept=".sql" class="form-control mb-3" required>
                        <button type="submit" class="btn btn-outline-primary w-100">Restore Database</button>
                    </form>
                </div>

            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>