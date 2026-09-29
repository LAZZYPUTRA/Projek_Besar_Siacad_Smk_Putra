<?php
session_start();
require_once __DIR__ . '/config/koneksi_database.php';

if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    header("Location: " . BASE_URL . $role . "/dashboard_" . $role . ".php");
} else {
    header("Location: " . BASE_URL . "auth/form_login.php");
}
exit();
?>