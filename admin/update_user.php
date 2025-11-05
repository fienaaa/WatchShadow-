<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    if (!$id || !$username || !$email) {
        echo "Invalid input.";
        exit;
    }

    $stmt = $mysqli->prepare("UPDATE users SET username=?, email=? WHERE id=?");
    $stmt->bind_param('ssi', $username, $email, $id);

    if ($stmt->execute()) {
        echo "User updated successfully!";
    } else {
        echo "Update failed. Try again.";
    }
}
?>