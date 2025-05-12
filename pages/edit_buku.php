<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/book.php';
require_once __DIR__ . '/../include/layout.php';

// Memastikan hanya admin yang bisa mengakses halaman ini
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?page=login");
    exit;
}

$error = '';
$bookData = null;

if (isset($_GET['id'])) {
    // Ambil data buku berdasarkan ID
    $db = new Database();
    $conn = $db->getConnection(); 
    $book = new Book($conn);
    $bookData = $book->getBookById($_GET['id']);
    
    if (!$bookData) {
        $error = "Data buku tidak ditemukan.";
    }
} else {
    header("Location: index.php?page=admin_dashboard");
    exit;
}

// Menangani pengiriman form untuk memperbarui buku
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $author = $_POST['author'] ?? '';
    $status = $_POST['status'] ?? 'available';  // Default status adalah available

    if (empty($title) || empty($author)) {
        $error = "Judul dan penulis tidak boleh kosong!";
    } else {
        if ($book->updateBook($_GET['id'], $title, $author, $status)) {
            header("Location: index.php?page=admin_dashboard");
            exit;
        } else {
            $error = "Gagal mengupdate data buku.";
        }
    }
}

renderPage(function() use ($bookData, $error) {
?>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">📝 Edit Buku</h2>
        <form method="POST">
            <!-- Input Judul Buku -->
            <div class="mb-3">
                <label for="title" class="form-label">Judul Buku</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($bookData['title']) ?>" required>
            </div>

            <!-- Input Penulis Buku -->
            <div class="mb-3">
                <label for="author" class="form-label">Penulis Buku</label>
                <input type="text" class="form-control" id="author" name="author" value="<?= htmlspecialchars($bookData['authors']) ?>" required>
            </div>

            <!-- Input Status Buku -->
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" name="status" id="status" required>
                    <option value="available" <?= $bookData['status'] == 'available' ? 'selected' : '' ?>>Tersedia</option>
                    <option value="borrowed" <?= $bookData['status'] == 'borrowed' ? 'selected' : '' ?>>Dipinjam</option>
                </select>
            </div>

            <!-- Tombol untuk Simpan Perubahan -->
            <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
            <a href="index.php?page=admin_dashboard" class="btn btn-secondary mt-2 w-100">Kembali</a>

            <!-- Menampilkan pesan error jika ada -->
            <?php if ($error): ?>
                <p class="mt-3 text-danger"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
        </form>
    </div>
<?php
});
?>
