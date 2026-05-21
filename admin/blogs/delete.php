<?php
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // deleteBlog() handles image cleanup internally
    if (deleteBlog($id)) {
        header('Location: index.php?msg=deleted');
    } else {
        header('Location: index.php?msg=error');
    }
} else {
    header('Location: index.php');
}
exit();
?>