<?php
function renderPage($content) {
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Informasi Perpustakaan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS modern -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Styling -->
    <style>
        body {
            background-color: #f4f6f9;
        }
        .navbar {
            background-color: #007bff;
        }
        .navbar a {
            color: white;
            margin-right: 15px;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        footer {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark px-4">
        <a class="navbar-brand fw-bold" href="index.php?page=dashboard">Perpustakaan</a>
        <div class="collapse navbar-collapse">
            <div class="navbar-nav">
                <a class="nav-link" href="index.php?page=dashboard">Dashboard</a>
                <a class="nav-link" href="index.php?page=book">Buku</a>
                <a class="nav-link" href="index.php?page=borrow">Pinjam Buku</a>
                <a class="nav-link" href="index.php?page=logout">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <?php $content(); ?>
    </main>

    <footer class="text-center mt-5 text-muted">
        &copy; <?= date('Y') ?> Sistem Informasi Perpustakaan
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
}
