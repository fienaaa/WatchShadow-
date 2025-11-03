<?php
session_start();
require_once 'config/db.php';
if(!isset($_SESSION['user_id'])) {
    header('Location: subscribe.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $steps = $_POST['steps'];
    $userId = $_SESSION['user_id'];

    $stmt = $mysqli->prepare("INSERT INTO subscriber_problems (user_id,title,steps) VALUES (?,?,?)");
    $stmt->bind_param("iss", $userId, $title, $steps);
    $stmt->execute();
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Problem — Dark Web Monitor</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <main class="container">
        <h1>Add a New Problem</h1>
        <form method="post">
            <label>Title</label>
            <input type="text" name="title" required>

            <label>Steps to Reproduce</label>
            <textarea name="steps" required></textarea>

            <button type="submit" class="btn primary">Add</button>
        </form>
    </main>
</body>

</html>