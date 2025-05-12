<?php
require_once __DIR__ . '/../classes/users.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../include/layout.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($username) || empty($password) || empty($confirm)) {
        $message = "Semua field harus diisi.";
    } elseif ($password !== $confirm) {
        $message = "Password dan konfirmasi tidak sama.";
    } else {
        $db = new Database();
        $user = new Users($db->getConnection());

        $dataId = $user->getUserIdIncrement();
        $newid = $dataId['id'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        if ($user->register($username, $hashed_password,"user", $newid)) {
            header("Location: index.php?page=login&registration=success");
            exit;
        } else {
            $message = "Username sudah digunakan.";
        }
    }
}

renderPage(function() use ($message) {
?>
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title mb-4 text-center">Daftar Akun</h3>
                    <form method="POST" action="index.php?page=register">
                        <div class="mb-3">
                            <label for="username" class="form-label">Nama Pengguna</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Kata Sandi</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Konfirmasi Kata Sandi</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
                        <?php endif; ?>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Daftar</button>
                        </div>
                        <p class="text-center mt-3">Sudah punya akun? <a href="index.php?page=login">Masuk</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
});
