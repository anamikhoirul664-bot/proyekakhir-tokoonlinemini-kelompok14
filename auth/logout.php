<?php
session_start();

$isAdmin = isset($_SESSION['admin']);

session_unset();
session_destroy();

if ($isAdmin) {
    header("Location: login_admin.php");
} else {
    header("Location: login.php");
}

exit;
?>