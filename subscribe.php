<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $keyword = $_POST['keyword'];
    $plan = $_POST['plan'];
    $start = date('Y-m-d H:i:s');

    // Calculate end date
    switch ($plan) {
        case 'weekly':
            $end = date('Y-m-d H:i:s', strtotime('+7 days'));
            break;
        case 'monthly':
            $end = date('Y-m-d H:i:s', strtotime('+1 month'));
            break;
        case 'yearly':
            $end = date('Y-m-d H:i:s', strtotime('+1 year'));
            break;
        default:
            $end = date('Y-m-d H:i:s', strtotime('+7 days'));
    }

    $stmt = $mysqli->prepare("INSERT INTO subscribers (email, keyword, plan, subscribed_at, subscription_end) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $email, $keyword, $plan, $start, $end);
    $stmt->execute();

    $_SESSION['subscriber_email'] = $email;
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Subscribe — Dark Web Monitor</title>
</head>

<body>
    <h1>Subscribe</h1>
    <form method="post">
        <label>Email:</label>
        <input type="email" name="email" required><br>

        <label>Keyword:</label>
        <input type="text" name="keyword" required><br>

        <label>Plan:</label><br>
        <input type="radio" name="plan" value="weekly" checked> Weekly<br>
        <input type="radio" name="plan" value="monthly"> Monthly<br>
        <input type="radio" name="plan" value="yearly"> Yearly<br>

        <button type="submit">Subscribe</button>
    </form>
</body>

</html>