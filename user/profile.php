<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../config/db.php';

// 🔒 Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// 🧠 Fetch user details
$stmt = $mysqli->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($current_username);
$stmt->fetch();
$stmt->close();

// ✏️ Update Username
if (isset($_POST['update_username'])) {
    $new_username = trim($_POST['new_username']);

    if ($new_username === '') {
        $error = "Username cannot be empty.";
    } else {
        // Check if username exists
        $check = $mysqli->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $check->bind_param("si", $new_username, $user_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Username already taken.";
        } else {
            $update = $mysqli->prepare("UPDATE users SET username = ? WHERE id = ?");
            $update->bind_param("si", $new_username, $user_id);
            $update->execute();
            $update->close();

            $_SESSION['username'] = $new_username;
            $current_username = $new_username;
            $success = "Username updated successfully.";
        }
        $check->close();
    }
}

// 🔑 Change Password
if (isset($_POST['change_password'])) {
    $current_pass = trim($_POST['current_password']);
    $new_pass = trim($_POST['new_password']);
    $confirm_pass = trim($_POST['confirm_password']);

    if ($current_pass === '' || $new_pass === '' || $confirm_pass === '') {
        $error = "Please fill all password fields.";
    } elseif ($new_pass !== $confirm_pass) {
        $error = "New passwords do not match.";
    } else {
        $stmt = $mysqli->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($stored_hash);
        $stmt->fetch();
        $stmt->close();

        if (!password_verify($current_pass, $stored_hash)) {
            $error = "Current password is incorrect.";
        } else {
            $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $update = $mysqli->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $update->bind_param("si", $new_hash, $user_id);
            $update->execute();
            $update->close();
            $success = "Password changed successfully.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile - Dark Web Monitor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #0b0c10;
        color: #fff;
        font-family: 'Poppins', sans-serif;
    }

    .container {
        margin-top: 40px;
        background: #1f2833;
        padding: 30px;
        border-radius: 12px;
        max-width: 600px;
    }

    h2 {
        color: #66fcf1;
        text-align: center;
        margin-bottom: 30px;
    }

    label {
        color: #c5c6c7;
    }

    input {
        background: #c5c6c7;
        color: #0b0c10;
    }

    .btn-custom {
        background: #45a29e;
        color: #0b0c10;
        font-weight: bold;
    }

    .btn-custom:hover {
        background: #66fcf1;
    }

    .alert {
        border-radius: 8px;
    }

    nav {
        background: #1f2833;
        padding: 10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    nav a {
        color: #66fcf1;
        margin: 0 10px;
        text-decoration: none;
    }

    nav a:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <nav>
        <div><strong>Dark Web Monitor</strong></div>
        <div>
            <a href="user.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <h2>Manage Profile</h2>

        <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Update Username -->
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label>Current Username:</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($current_username) ?>" disabled>
            </div>
            <div class="mb-3">
                <label>New Username:</label>
                <input type="text" class="form-control" name="new_username" placeholder="Enter new username" required>
            </div>
            <button type="submit" name="update_username" class="btn btn-custom w-100">Update Username</button>
        </form>

        <hr style="border-color:#45a29e;">

        <!-- Change Password -->
        <form method="POST">
            <div class="mb-3">
                <label>Current Password:</label>
                <input type="password" class="form-control" name="current_password" required>
            </div>
            <div class="mb-3">
                <label>New Password:</label>
                <input type="password" class="form-control" name="new_password" required>
            </div>
            <div class="mb-3">
                <label>Confirm New Password:</label>
                <input type="password" class="form-control" name="confirm_password" required>
            </div>
            <button type="submit" name="change_password" class="btn btn-custom w-100">Change Password</button>
        </form>
    </div>
</body>

</html>