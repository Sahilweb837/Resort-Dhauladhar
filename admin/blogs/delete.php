<?php
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$adminBase = function_exists('getBaseUrl') ? getBaseUrl() . '/admin/' : '../';
session_write_close();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // deleteBlog() handles image cleanup internally
    if (deleteBlog($id)) {
        header('Location: ' . $adminBase . 'blogs/index.php?msg=deleted');
    } else {
        header('Location: ' . $adminBase . 'blogs/index.php?msg=error');
    }
} else {
    header('Location: ' . $adminBase . 'blogs/index.php');
}
exit();
?>