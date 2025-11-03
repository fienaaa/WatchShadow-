<?php
// admin_login.php
require_once '../config/db.php';
session_start();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    if ($user && $pass) {
        $stmt = $mysqli->prepare("SELECT id, password_hash FROM admins WHERE username = ?");
        $stmt->bind_param('s', $user);
        $stmt->execute();
        $stmt->bind_result($id, $pw_hash);
        if ($stmt->fetch()) {
            if (hash('sha256', $pass) === $pw_hash) {
                $_SESSION['admin_id'] = $id;
                $_SESSION['admin_user'] = $user;
                header('Location: ../admin/admin.php');
                exit;
            } else {
                $msg = 'Invalid credentials.';
            }
        } else {
            $msg = 'Invalid credentials.';
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <main class="container">
        <section class="card narrow">
            <h2>Admin Login</h2>
            <?php if ($msg): ?><div class="alert error"><?= e($msg) ?></div><?php endif; ?>
            <form method="post">
                <label>Username</label>
                <input name="username" required>
                <label>Password</label>
                <input type="password" name="password" required>
                <div class="controls">
                    <button class="btn primary" type="submit">Login</button>
                    <a href="../index.php" class="btn outline">Back</a>
                </div>
            </form>
        </section>
    </main>
</body>

</html>