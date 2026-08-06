<?php
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$adminBase = function_exists('getBaseUrl') ? getBaseUrl() . '/admin/' : '../';

$id = $_GET['id'] ?? null;
if ($id) {
    deleteReview($id);
}

session_write_close();
header('Location: ' . $adminBase . 'reviews/index.php?msg=deleted');
exit();
?>
