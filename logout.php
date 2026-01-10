<?php
session_start();
session_destroy();
session_start();

header("Location: index.php");  // Your login page
exit();
?>