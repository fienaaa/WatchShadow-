<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/db.php'; 

if (isset($_SESSION['admin_id'])) {
    header('Location: admin.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Enter username and password.';
    } else {
        $stmt = $mysqli->prepare("SELECT id, password_hash FROM admins WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $hashed_password);

        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                $_SESSION['admin_id'] = $id;
                $_SESSION['admin_username'] = $username;
                header('Location: admin.php');
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } else {
            $error = 'Invalid username or password.';
        }
        $stmt->close();
    }
}

// Helper function
if (!function_exists('e')) {
    function e($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
    body {
        background: #0b0c10;
        color: #fff;
        font-family: Poppins, sans-serif;
    }

    .container {
        max-width: 400px;
        margin: 100px auto;
        padding: 20px;
        background: #1f2833;
        border-radius: 10px;
    }

    h2 {
        text-align: center;
        color: #66fcf1;
        margin-bottom: 20px;
    }

    input,
    textarea {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        border-radius: 5px;
        border: none;
    }

    button {
        width: 100%;
        padding: 10px;
        background: #45a29e;
        color: #0b0c10;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }

    button:hover {
        background: #66fcf1;
    }

    .alert.error {
        background: #ff4c4c;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Admin Login</h2>
        <?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
        <form method="post">
            <label>Username</label>
            <input type="text" name="username" required>
            <label>Password</label>
            <input type="password" name="password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>

</html>