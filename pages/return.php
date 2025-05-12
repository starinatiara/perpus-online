<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/borrowing.php';
session_start();

if (!isset($_GET['borrow_id'])) {
    header("Location: index.php?page=dashboard");
    exit;
}

$borrow_id = $_GET['borrow_id'];

$db = new Database();
$conn = $db->getConnection();
$borrowing = new Borrowing($conn);

if ($borrowing->returnBook($borrow_id)) {
    $_SESSION['return_message'] = "Buku berhasil dikembalikan.";
} else {
    $_SESSION['return_message'] = "Gagal mengembalikan buku.";
}

header("Location: index.php?page=dashboard");
exit;
