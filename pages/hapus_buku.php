<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/book.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?page=login");
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    $db = new Database();
    $conn = $db->getConnection(); 
    $book = new Book($conn);

    $book->deleteBookById($id);
}

header("Location: index.php?page=admin_dashboard");
exit;
?>
