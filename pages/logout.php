<?php
require_once __DIR__ . '/../classes/users.php';
require_once __DIR__ . '/../config/database.php';

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
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if ($user->register($username, $hashed_password)) {
            header("Location: index.php?page=login&registration=success");
            exit;
        } else {
            $message = "Username sudah digunakan.";
        }
    }
}
?>

<div class="container">
    <h2>Register</h2>
    <form method="POST" action="index.php?page=register">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
        <p class="mt-3">
            Sudah punya akun? <a href="index.php?page=login">Login</a>
        </p>
        <p class="mt-3 text-danger"><?= htmlspecialchars($message) ?></p>
    </form>
</div>
