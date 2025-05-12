<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../include/layout.php';

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT * FROM books");
$stmt->execute();
$result = $stmt->get_result();

renderPage(function () use ($result) {
?>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">📚 Daftar Buku Tersedia</h2>
        <div class="row">
            <?php while ($book = $result->fetch_assoc()): 
                $isBorrowed = ($book['status'] === 'borrowed');

                // Gunakan cover buku jika ada, fallback ke default
                $imagePath = !empty($book['cover_path']) && file_exists($book['cover_path'])
                    ? $book['cover_path']
                    : 'assets/images/default_book.png';
            ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="<?= htmlspecialchars($imagePath) ?>" class="card-img-top" alt="Cover Buku"
                             style="height: 250px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                            <p class="card-text mb-2">
                                <strong>Penulis:</strong> <?= htmlspecialchars($book['authors']) ?>
                            </p>
                            <div class="mt-auto">
                                <?php if ($isBorrowed): ?>
                                    <button class="btn btn-outline-secondary w-100" disabled>📕 Dipinjam</button>
                                <?php else: ?>
                                    <a href="index.php?page=borrow&book_id=<?= $book['id'] ?>" class="btn btn-primary w-100">📖 Pinjam</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <?php if ($result->num_rows === 0): ?>
            <div class="alert alert-info w-100 text-center">Belum ada buku yang tersedia.</div>
        <?php endif; ?>
    </div>
<?php
});
?>
