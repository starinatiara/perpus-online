<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/book.php';
require_once __DIR__ . '/../include/layout.php';

$db = new Database();
$conn = $db->getConnection(); 
$book = new Book($conn);      

$books = Book::getAllBooks($conn);

renderPage(function() use ($books) {
?>
<div class="container mt-5">
    <h2 class="mb-4">Dashboard Admin</h2>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Daftar Buku</h4>
        <a href="index.php?page=add_book" class="btn btn-success">+ Tambah Buku Baru</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Status</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($books) > 0): ?>
                            <?php foreach ($books as $bookItem): ?>
                                <tr>
                                    <td><?= htmlspecialchars($bookItem['title']) ?></td>
                                    <td><?= htmlspecialchars($bookItem['authors']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($bookItem['status']) ?>
                                        <?= $bookItem['status'] === 'dipinjam' ? ' (borrowed)' : '' ?>
                                    </td>
                                    <td>
                                        <a href="index.php?page=edit_buku&id=<?= $bookItem['id'] ?>" class="btn btn-warning btn-sm mb-1">Edit</a>
                                        <a href="index.php?page=hapus_buku&id=<?= $bookItem['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada buku yang ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <a href="index.php?page=logout" class="btn btn-danger mt-4">Logout</a>
</div>
<?php
});
?>
