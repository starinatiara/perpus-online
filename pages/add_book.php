<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/book.php';
require_once __DIR__ . '/../include/layout.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?page=login");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? '';
    $author = $_POST['author'] ?? '';
    $status = 'available';
    $cover_path = '';

    if (empty($title) || empty($author)) {
        $error = "Judul dan penulis buku harus diisi!";
    } else {
        if (!empty($_FILES['cover']['name'])) {
            $uploadDir = '/tmp/';
            $filename = basename($_FILES['cover']['name']);
            $targetPath = $uploadDir . time() . "_" . $filename;

            $fileType = mime_content_type($_FILES['cover']['tmp_name']);
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

            if (in_array($fileType, $allowedTypes)) {
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (move_uploaded_file($_FILES['cover']['tmp_name'], $targetPath)) {
                    $cover_path = $targetPath;
                } else {
                    $error = "Gagal mengupload cover buku.";
                }
            } else {
                $error = "Format cover tidak valid. Gunakan JPG, PNG, atau WebP.";
            }
        }

        if (empty($error)) {
            $db = new Database();
            $conn = $db->getConnection();
            $book = new Book($conn);

            if ($book->addBook($title, $author, $status, $cover_path)) {
                header("Location: index.php?page=admin_dashboard");
                exit;
            } else {
                $error = "Gagal menambahkan buku!";
            }
        }
    }
}

renderPage(function() use ($error) {
?>
    <div class="container mt-5">
        <h2 class="mb-4">Tambah Buku Baru</h2>
        <form method="POST" action="index.php?page=add_book" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Judul Buku</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="author" class="form-label">Penulis Buku</label>
                <input type="text" class="form-control" id="author" name="author" required>
            </div>
            <div class="mb-3">
                <label for="cover" class="form-label">Upload Cover Buku (opsional)</label>
                <input type="file" class="form-control" id="cover" name="cover" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Tambah Buku</button>
            <a href="index.php?page=admin_dashboard" class="btn btn-secondary ms-2">Kembali</a>

            <?php if ($error): ?>
                <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
        </form>
    </div>
<?php
});
?>
