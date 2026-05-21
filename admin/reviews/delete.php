<?php
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$id = $_GET['id'] ?? null;
if ($id) {
    deleteReview($id);
}

header('Location: index.php?msg=deleted');
exit();
?>
