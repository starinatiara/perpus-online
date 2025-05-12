<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/borrowing.php';
require_once __DIR__ . '/../include/layout.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit;
}

$db = new Database();
$conn = $db->getConnection();
$borrowing = new Borrowing($conn);

if (isset($_GET['borrow_id'])) {
    $result = $borrowing->returnBook($_GET['borrow_id']);

    if ($result) {
        $_SESSION['message'] = "Buku berhasil dikembalikan.";
    } else {
        $_SESSION['error'] = "Gagal mengembalikan buku.";
    }

    header("Location: index.php?page=dashboard");
    exit;
}


$borrowingHistory = $borrowing->getBorrowingHistory($_SESSION['user_id']);

renderPage(function () use ($borrowingHistory) {
?>
    <div class="container">
        <div class="row mt-4 mb-4">
            <div class="col">
                <h2>Halo, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h2>
                <p class="text-muted">Berikut adalah riwayat peminjaman buku Anda.</p>
            </div>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['message']) ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if ($borrowingHistory['success'] && !empty($borrowingHistory['history'])): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Judul Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Batas Pengembalian</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($borrowingHistory['history'] as $record): ?>
                            <tr>
                                <td><?= htmlspecialchars($record['title']) ?></td>
                                <td><?= htmlspecialchars($record['borrow_date']) ?></td>
                                <td><?= htmlspecialchars($record['return_deadline']) ?></td>
                                <td>
                                    <?php if ($record['status'] === 'dipinjam'): ?>
                                        <span class="badge badge-warning">Dipinjam</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Dikembalikan</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($record['status'] === 'dipinjam'): ?>
                                        <a href="index.php?page=dashboard&borrow_id=<?= $record['id'] ?>&book_id=<?= $record['book_id'] ?>"
                                           class="btn btn-sm btn-outline-success"
                                           onclick="return confirm('Yakin ingin mengembalikan buku ini?')">
                                           Kembalikan
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Anda belum meminjam buku.</div>
        <?php endif; ?>

        <a href="index.php?page=logout" class="btn btn-danger mt-4">Logout</a>
    </div>
<?php
});
