<?php
session_start();
require_once '../config/db.php';
if(!isset($_SESSION['admin_id'])) header('Location: admin_login.php');

// Fetch subscribers
$subscribers = $mysqli->query("SELECT * FROM subscribers ORDER BY subscribed_at DESC");

// Counts
$total_subscribers = $mysqli->query("SELECT COUNT(*) as c FROM subscribers")->fetch_assoc()['c'];
$matched_subscribers = $mysqli->query("
    SELECT COUNT(DISTINCT s.id) AS c
    FROM subscribers s
    JOIN breaches b ON b.keyword LIKE CONCAT('%', s.keyword, '%')
")->fetch_assoc()['c'];

// Handle alert messages
$alert = '';
if(isset($_GET['msg'])){
    $alert = htmlspecialchars($_GET['msg'], ENT_QUOTES, 'UTF-8');
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
    <title>Subscribers List</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
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

    table {
        width: 100%;
        border-collapse: collapse;
        background: #1f2833;
        border-radius: 10px;
        overflow: hidden;
    }

    table th,
    table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #45a29e;
    }

    table th {
        background: #0b0c10;
        color: #66fcf1;
    }

    .btn {
        background: #45a29e;
        color: #0b0c10;
        border: none;
        border-radius: 5px;
        padding: 5px 10px;
        cursor: pointer;
    }

    .btn:hover {
        background: #66fcf1;
    }

    /* Alert */
    .alert {
        background-color: #45a29e;
        color: #0b0c10;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: bold;
        text-align: center;
        animation: fadeOut 3s forwards;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        padding-top: 100px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.6);
    }

    .modal-content {
        background-color: #1f2833;
        margin: auto;
        padding: 20px;
        border-radius: 10px;
        width: 400px;
        color: #fff;
    }

    .modal input,
    .modal textarea {
        width: 100%;
        padding: 8px;
        margin: 5px 0 10px 0;
        border-radius: 5px;
        border: 1px solid #45a29e;
        background: #0b0c10;
        color: #fff;
    }

    .modal button {
        padding: 8px 16px;
        margin-top: 10px;
        border: none;
        border-radius: 5px;
        background: #45a29e;
        color: #0b0c10;
        cursor: pointer;
    }

    .modal button:hover {
        background: #66fcf1;
    }

    /* Fade out animation */
    @keyframes fadeOut {
        0% {
            opacity: 1;
        }

        80% {
            opacity: 1;
        }

        100% {
            opacity: 0;
        }
    }
    </style>
</head>

<body>
    <div class="sidebar">
        <a href="admin.php">Home</a>
        <a href="breaches.php">Breaches List</a>
        <a href="subscribers.php" class="active">Subscribers List</a>
        <a href="admin_logout.php">Logout</a>
    </div>

    <div class="main">
        <h1>Subscribers List</h1>

        <!-- Alert -->
        <?php if($alert): ?>
        <div class="alert"><?= $alert ?></div>
        <?php endif; ?>

        <div class="cards">
            <div class="card">
                <h3>Total Subscribers</h3>
                <p><?= $total_subscribers ?></p>
            </div>
            <div class="card">
                <h3>Subscribers with Breaches</h3>
                <p><?= $matched_subscribers ?></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Keyword</th>
                    <th>Subscribed At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $subscribers->fetch_assoc()): ?>
                <tr>
                    <td><?= e($row['id']) ?></td>
                    <td><?= e($row['email']) ?></td>
                    <td><?= e($row['keyword']) ?></td>
                    <td><?= e($row['subscribed_at']) ?></td>
                    <td>
                        <button class="btn editBtn" data-id="<?= e($row['id']) ?>" data-email="<?= e($row['email']) ?>"
                            data-keyword="<?= e($row['keyword']) ?>">✏️</button>
                        <a href="delete_subscriber.php?id=<?= e($row['id']) ?>"
                            onclick="return confirm('Delete this subscriber?')" class="btn">🗑️</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Edit Subscriber</h3>
            <form method="post" action="update_subscriber.php" id="editForm">
                <input type="hidden" name="id" id="modal_id">
                <label>Email</label>
                <input type="email" name="email" id="modal_email" required>
                <label>Keyword</label>
                <input type="text" name="keyword" id="modal_keyword" required>
                <button type="submit">Save</button>
                <button type="button" onclick="closeModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
    // Open modal
    const editBtns = document.querySelectorAll('.editBtn');
    const modal = document.getElementById('editModal');
    editBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('modal_id').value = btn.dataset.id;
            document.getElementById('modal_email').value = btn.dataset.email;
            document.getElementById('modal_keyword').value = btn.dataset.keyword;
            modal.style.display = 'block';
        });
    });

    function closeModal() {
        modal.style.display = 'none';
    }
    window.onclick = function(event) {
        if (event.target == modal) modal.style.display = 'none';
    }
    </script>
</body>

</html>