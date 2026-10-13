<?php
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['admin']);
header("Location: " . BASE_URL . "profile.php");
exit();
?>