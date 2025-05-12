<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/users.php';
require_once __DIR__ . '/../include/layout.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi.";
    } else {
        $db = new Database();
        $conn = $db->getConnection();
        $user = new Users($conn);

        $userData = $user->getUserByUsername($username);

        if ($userData && password_verify($password, $userData['password'])) {
            session_start();
            $_SESSION['username'] = $userData['username'];
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['role'] = $userData['role'];
            session_regenerate_id(true);

            if ($_SESSION['role'] === 'admin') {
                header("Location: index.php?page=admin_dashboard");
            } else {
                header("Location: index.php?page=dashboard");
            }
            exit;
        } else {
            $error = "Username atau password salah!";
        }

        $db->closeConnection();
    }
}

renderPage(function() use ($error) {
?>
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title mb-4 text-center">Login</h3>
                    <form method="POST" action="index.php?page=login">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                        <p class="text-center mt-3">Belum punya akun? <a href="index.php?page=register">Daftar</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
});
