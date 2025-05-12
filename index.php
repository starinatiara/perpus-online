<?php
session_start(); 
$page = $_GET['page'] ?? 'login';

$admin_pages = ['admin_dashboard', 'add_book', 'edit_buku', 'hapus_buku'];

$user_pages = ['dashboard', 'book', 'borrow'];

function is_authenticated($role = null) {
    if (!isset($_SESSION['user_id'])) {
        return false; 
    }

    if ($role !== null && $_SESSION['role'] !== $role) {
        return false; 
    }

    return true; 
}
switch ($page) {
    case 'login':
        include 'pages/login.php';
        break;

    case 'register':
        include 'pages/register.php';
        break;

    case 'logout':
        session_destroy();
        header("Location: index.php?page=login");
        exit;
        break;

    default:
        if (in_array($page, $admin_pages) && !is_authenticated('admin')) {
            header("Location: index.php?page=login");
            exit;
        } elseif (in_array($page, $user_pages) && !is_authenticated('user')) {
            header("Location: index.php?page=login");
            exit;
        }

        if (file_exists("pages/$page.php")) {
            include "pages/$page.php";
        } else {
            echo "<h3>Halaman tidak ditemukan.</h3>";
        }
        break;
}
?>