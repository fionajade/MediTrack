<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["sql_file"])) {
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'medi';

    $sql_file = $_FILES["sql_file"]["tmp_name"];

    if (is_uploaded_file($sql_file)) {
        $command = "mysql -h $db_host -u $db_user -p$db_pass $db_name < $sql_file";
        system($command, $output);

        $message = $output === 0 ? "Restore successful." : "Restore failed.";
        echo "<script>alert('$message'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('File upload failed.'); window.location.href='admin_dashboard.php';</script>";
    }
}
?>
