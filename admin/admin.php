<?php
session_start();
require_once '../config/db.php';
if(!isset($_SESSION['admin_id'])) header('Location: admin_login.php');

$breaches_count = $mysqli->query("SELECT COUNT(*) as c FROM breaches")->fetch_assoc()['c'];
$subscribers_count = $mysqli->query("SELECT COUNT(*) as c FROM subscribers")->fetch_assoc()['c'];

$matched_subscribers_count = $mysqli->query("
    SELECT COUNT(DISTINCT s.id) AS c
    FROM subscribers s
    JOIN breaches b ON b.keyword LIKE CONCAT('%', s.keyword, '%')
")->fetch_assoc()['c'];

$chart_query = $mysqli->query("
    SELECT s.keyword, COUNT(b.id) AS breaches_count
    FROM subscribers s
    LEFT JOIN breaches b ON b.keyword LIKE CONCAT('%', s.keyword, '%')
    GROUP BY s.id
");
$chart_labels = [];
$chart_data = [];
while($row = $chart_query->fetch_assoc()) {
    $chart_labels[] = $row['keyword'];
    $chart_data[] = (int)$row['breaches_count'];
}

if (!function_exists('e')) {
    function e($str) { return htmlspecialchars($str, ENT_QUOTES, 'UTF-8'); }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    body {
        font-family: "Poppins", sans-serif;
        margin: 0;
        background: #1f1f2e;
        color: #fff;
    }

    .sidebar {
        width: 220px;
        background: #0b0c10;
        position: fixed;
        height: 100%;
        padding-top: 20px;
    }

    .sidebar a {
        display: block;
        padding: 12px 20px;
        color: #fff;
        text-decoration: none;
        margin-bottom: 5px;
    }

    .sidebar a:hover {
        background: #45a29e;
        color: #0b0c10;
        border-radius: 5px;
    }

    .main {
        margin-left: 240px;
        padding: 20px;
    }

    .cards {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .card {
        background: #1f2833;
        padding: 20px;
        border-radius: 10px;
        flex: 1;
        min-width: 200px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    }

    .card h3 {
        margin-top: 0;
        color: #66fcf1;
    }

    .card p {
        font-size: 2em;
        margin: 10px 0;
    }

    form input,
    form textarea {
        width: 100%;
        padding: 10px;
        margin: 5px 0 15px 0;
        border-radius: 5px;
        border: 1px solid #45a29e;
        background: #0b0c10;
        color: #fff;
    }

    form button {
        padding: 10px 20px;
        background: #45a29e;
        color: #0b0c10;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    form button:hover {
        background: #66fcf1;
    }
    </style>
</head>

<body>

    <div class="sidebar">
        <a href="index.php">Home</a>
        <a href="breaches.php">Breaches List</a>
        <a href="subscribers.php">Subscribers List</a>
        <a href="admin_logout.php">Logout</a>
    </div>

    <div class="main">
        <h1>Welcome, <?= e($_SESSION['admin_username'] ?? 'Admin') ?></h1>

        <div class="cards">
            <div class="card">
                <h3>Total Breaches</h3>
                <p><?= $breaches_count ?></p>
            </div>
            <div class="card">
                <h3>Total Subscribers</h3>
                <p><?= $subscribers_count ?></p>
            </div>
            <div class="card">
                <h3>Subscribers with Breaches</h3>
                <p><?= $matched_subscribers_count ?></p>
            </div>
        </div>

        <div class="card">
            <h3>Subscribers vs Breaches</h3>
            <canvas id="subBreachChart" height="150"></canvas>
        </div>

        <div class="card">
            <h3>Send Manual Notification</h3>
            <form method="post" action="send_notify.php">
                <label>Keyword</label>
                <input name="notify_keyword" placeholder="example.com or keyword" required>
                <label>Email Subject</label>
                <input name="subject" value="Alert: possible leak match" required>
                <label>Email Body</label>
                <textarea name="body">We detected your monitored keyword in a new breach. Please take action.</textarea>
                <button type="submit">Send</button>
            </form>
        </div>
    </div>

    <script>
    const ctx = document.getElementById('subBreachChart').getContext('2d');
    const subBreachChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_labels) ?>,
            datasets: [{
                    label: 'Breaches per Subscriber',
                    data: <?= json_encode($chart_data) ?>,
                    borderColor: '#45a29e',
                    backgroundColor: 'rgba(69, 162, 158, 0.2)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Subscriber Exists (1=Yes,0=No)',
                    data: <?= json_encode(array_map(fn($x)=>$x>0?1:0, $chart_data)) ?>,
                    borderColor: '#66fcf1',
                    backgroundColor: 'rgba(102, 252, 241, 0.2)',
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    </script>

</body>

</html>