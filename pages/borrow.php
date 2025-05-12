<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/book.php';
require_once __DIR__ . '/../classes/borrowing.php';
require_once __DIR__ . '/../include/layout.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit;
}

$db = new Database();
$conn = $db->getConnection();
$book = new Book($conn);

$bookDetails = null;
if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];
    $bookDetails = $book->getBookById($book_id);
    if (!$bookDetails) {
        $_SESSION['borrow_error'] = "Buku tidak ditemukan.";
        header("Location: index.php?page=book");
        exit;
    }
} else {
    $_SESSION['borrow_error'] = "ID buku tidak valid.";
    header("Location: index.php?page=book");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_borrow'])) {
    $user_id = $_SESSION['user_id'];
    $borrowing = new Borrowing($conn);
    if ($borrowing->borrowBook($user_id, $book_id)) {
        $_SESSION['borrow_message'] = "Buku berhasil dipinjam.";
    } else {
        $_SESSION['borrow_message'] = "Gagal meminjam buku. Mungkin sudah dipinjam.";
    }
    
    header("Location: index.php?page=dashboard");
    exit;
}

renderPage(function () use ($bookDetails) {
?>
    <div class="container mt-5">
        <h2 class="text-center mb-4">📘 Konfirmasi Peminjaman Buku</h2>

        <?php if (isset($_SESSION['borrow_error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['borrow_error']) ?></div>
            <?php unset($_SESSION['borrow_error']); ?>
        <?php endif; ?>

        <?php if ($bookDetails): ?>
            <div class="card mx-auto" style="max-width: 600px;">
                <div class="card-body">
                    <h4 class="card-title text-primary"><?= htmlspecialchars($bookDetails['title']) ?></h4>
                    <p class="card-text"><strong>Pengarang:</strong> <?= htmlspecialchars($bookDetails['authors']) ?></p>
                    <p class="card-text"><strong>Tahun Terbit:</strong> <?= htmlspecialchars($bookDetails['year']) ?></p>
                    <p class="card-text"><strong>Tanggal Pinjam:</strong> <?= date('Y-m-d') ?></p>
                    <p class="card-text"><strong>Tanggal Kembali:</strong> <?= date('Y-m-d', strtotime('+7 days')) ?></p>

                    <form method="POST" action="index.php?page=borrow&book_id=<?= $bookDetails['id'] ?>">
                        <input type="hidden" name="book_id" value="<?= $bookDetails['id'] ?>">
                        <div class="d-flex justify-content-between mt-4">
                            <a href="index.php?page=book" class="btn btn-outline-secondary">← Batal</a>
                            <button type="submit" name="confirm_borrow" class="btn btn-success">📖 Konfirmasi Pinjam</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['borrow_message'])): ?>
            <div class="alert alert-success mt-4"><?= htmlspecialchars($_SESSION['borrow_message']) ?></div>
            <?php unset($_SESSION['borrow_message']); ?>
        <?php endif; ?>
    </div>
<?php
});
