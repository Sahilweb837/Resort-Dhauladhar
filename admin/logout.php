<?php
require_once __DIR__ . '/../includes/functions.php';
logout();
$basePath = getBaseUrl();
session_write_close();
header('Location: ' . $basePath . '/admin/index.php');
exit();
?>