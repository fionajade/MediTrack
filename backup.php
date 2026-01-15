<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'pill-and-pestle';

$backup_file = __DIR__ . '\\backup_' . date('Y-m-d_H-i-s') . '.sql';

$mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';

$command = "\"$mysqldump\" -h $db_host -u $db_user " .
           ($db_pass !== '' ? "-p$db_pass " : '') .
           "\"$db_name\" > \"$backup_file\" 2>&1";

exec($command, $output, $result);

if ($result === 0 && file_exists($backup_file) && filesize($backup_file) > 0) {
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="pill-and-pestle.sql"');
    header('Content-Length: ' . filesize($backup_file));
    readfile($backup_file);
    unlink($backup_file);
    exit;
} else {
    echo "<pre>Backup failed.\n\nCommand:\n$command\n\nOutput:\n";
    print_r($output);
    echo "</pre>";
}
