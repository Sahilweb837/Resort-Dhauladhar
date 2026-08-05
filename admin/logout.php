<?php
require_once __DIR__ . '/../includes/functions.php';
logout();
$basePath = getBaseUrl();
header('Location: ' . $basePath . '/admin/index.php');
exit();
?>