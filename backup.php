<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'pill-and-pestle';

$backup_file = 'backup_' . date("Y-m-d_H-i-s") . '.sql';
$command = "mysqldump -h $db_host -u $db_user -p$db_pass $db_name > $backup_file";

system($command);

if (file_exists($backup_file)) {
    header('Content-Type: application/sql');
    header("Content-Disposition: attachment; filename=\"$backup_file\"");
    readfile($backup_file);
    unlink($backup_file); 
} else {
    echo "Backup failed.";
}
?>