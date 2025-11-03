<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config/db.php';

if (!isset($_SESSION['subscriber_email'])) {
    header('Location: subscribe.php');
    exit;
}

$email = $_SESSION['subscriber_email'];

// Fetch subscriber info
$stmt = $mysqli->prepare("SELECT * FROM subscribers WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$subscriber = $result->fetch_assoc();

// Fetch breaches that match subscriber's email or keyword
$keyword = $subscriber['keyword'];
$breaches = $mysqli->query("
    SELECT * FROM breaches 
    WHERE keyword LIKE '%$keyword%' OR keyword = '$email'
    ORDER BY leak_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard — Dark Web Monitor</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f4f4;
        padding: 20px;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        background: #fff;
        margin-top: 20px;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background: #333;
        color: #fff;
    }

    h1,
    h2 {
        color: #333;
    }

    .info {
        margin-bottom: 15px;
    }
    </style>
</head>

<body>
    <h1>Welcome, <?= htmlspecialchars($email) ?></h1>

    <div class="info">
        <p><strong>Plan:</strong> <?= htmlspecialchars($subscriber['plan']) ?></p>
        <p><strong>Subscribed at:</strong> <?= htmlspecialchars($subscriber['subscription_start']) ?></p>
        <p><strong>Expires at:</strong> <?= htmlspecialchars($subscriber['subscription_end'] ?? 'N/A') ?></p>
    </div>

    <h2>Your Matched Breaches</h2>

    <?php if ($breaches->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Keyword / Email</th>
                <th>Source</th>
                <th>Leak Date</th>
                <th>Description</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $breaches->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['keyword']) ?></td>
                <td><?= htmlspecialchars($row['source']) ?></td>
                <td><?= htmlspecialchars($row['leak_date']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p>No breaches found for your keyword/email.</p>
    <?php endif; ?>
</body>

</html>