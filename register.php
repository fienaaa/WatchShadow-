<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/db.php'; // adjust path if needed

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm = trim($_POST['confirm'] ?? '');

    if ($username === '' || $email === '' || $password === '' || $confirm === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Check if username or email already exists
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'Username or email already exists.';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert = $mysqli->prepare("INSERT INTO users (username, email, password_hash, created_at) VALUES (?, ?, ?, NOW())");
            $insert->bind_param('sss', $username, $email, $hashed_password);

            if ($insert->execute()) {
                $success = 'Registration successful! You can now log in.';
            } else {
                $error = 'Registration failed. Please try again.';
            }

            $insert->close();
        }
        $stmt->close();
    }
}

/* Helper for escaping */
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
    <title>User Registration</title>
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
        box-shadow: 0 0 15px rgba(102, 252, 241, 0.2);
    }

    h2 {
        text-align: center;
        color: #66fcf1;
        margin-bottom: 20px;
    }

    input {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        border-radius: 5px;
        border: none;
        background: #c5c6c7;
        color: #0b0c10;
        font-size: 14px;
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
        font-size: 15px;
    }

    button:hover {
        background: #66fcf1;
    }

    .alert {
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
        text-align: center;
    }

    .alert.error {
        background: #ff4c4c;
    }

    .alert.success {
        background: #2ecc71;
    }

    .login-link {
        text-align: center;
        margin-top: 15px;
    }

    .login-link a {
        color: #66fcf1;
        text-decoration: none;
    }

    .login-link a:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Create Account</h2>
        <?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert success"><?= e($success) ?></div><?php endif; ?>

        <form method="post">
            <label>Username</label>
            <input type="text" name="username" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label>Confirm Password</label>
            <input type="password" name="confirm" required>

            <button type="submit">Register</button>
        </form>

        <div class="login-link">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>

</html>