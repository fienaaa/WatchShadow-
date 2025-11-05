<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

// --- Dashboard Counts ---
$breaches_count = $mysqli->query("SELECT COUNT(*) AS c FROM breaches")->fetch_assoc()['c'] ?? 0;
$users_count = $mysqli->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'] ?? 0;

// Count users with breach data (from local breaches table)
$matched_users_count = $mysqli->query("
    SELECT COUNT(DISTINCT u.id) AS c
    FROM users u
    JOIN breaches b ON b.keyword LIKE CONCAT('%', u.email, '%')
")->fetch_assoc()['c'] ?? 0;

// --- Chart Data (Users vs Breaches) ---
$chart_query = $mysqli->query("
    SELECT u.email AS keyword, COUNT(b.id) AS breaches_count
    FROM users u
    LEFT JOIN breaches b ON b.keyword LIKE CONCAT('%', u.email, '%')
    GROUP BY u.id
");

$chart_labels = [];
$chart_data = [];
while ($row = $chart_query->fetch_assoc()) {
    $chart_labels[] = $row['keyword'];
    $chart_data[] = (int)$row['breaches_count'];
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

    .sidebar a:hover,
    .sidebar a.active {
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
        <a href="admin.php" class="active">Home</a>
        <a href="breaches.php">Breaches List</a>
        <a href="users.php">Users List</a>
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
                <h3>Total Users</h3>
                <p><?= $users_count ?></p>
            </div>
            <div class="card">
                <h3>Users with Breach Data</h3>
                <p><?= $matched_users_count ?></p>
            </div>
        </div>

        <div class="card">
            <h3>Users vs Breaches Chart</h3>
            <canvas id="userBreachChart" height="150"></canvas>
        </div>

        <?php
        // Fetch breach list for dropdown
        $breach_list = $mysqli->query("SELECT * FROM breaches ORDER BY created_at DESC");
        ?>
        <div class="card">
            <h3>Add New Breach</h3>
            <form method="post" action="add_breach.php" id="addBreachForm">
                <label for="keyword">Email / Keyword</label>
                <input type="text" name="keyword" id="keyword" placeholder="e.g. user@example.com" required>

                <label for="source">Source / Leak Site</label>
                <input type="text" name="source" id="source" placeholder="e.g. ExampleLeakSite1" required>

                <?php $today = date('Y-m-d'); ?>
                <label for="leak_date">Leak Date</label>
                <input type="date" name="leak_date" id="leak_date" value="<?= $today ?>" required>

                <label for="description">Description</label>
                <textarea name="description" id="description" rows="4"
                    placeholder="Brief description of the leak (what was exposed)"></textarea>

                <div style="margin-top:12px;">
                    <button type="submit" class="btn">Add Breach</button>
                    <button type="reset" class="btn"
                        style="background:#6b7280;color:#fff;margin-left:8px;">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    const ctx = document.getElementById('userBreachChart').getContext('2d');
    const userBreachChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_labels) ?>,
            datasets: [{
                label: 'Breaches per User',
                data: <?= json_encode($chart_data) ?>,
                borderColor: '#45a29e',
                backgroundColor: 'rgba(69, 162, 158, 0.2)',
                fill: true,
                tension: 0.3
            }]
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