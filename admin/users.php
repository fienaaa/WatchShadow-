<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION['admin_id'])) header('Location: admin_login.php');

// Fetch all users
$users = $mysqli->query("SELECT * FROM users ORDER BY id DESC");

// Count total users
$total_users = $mysqli->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];

// Count total users with breach data
$total_breached_users = $mysqli->query("
    SELECT COUNT(DISTINCT u.email) AS c
    FROM users u
    INNER JOIN breaches b ON u.email = b.keyword
")->fetch_assoc()['c'] ?? 0;

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
    <title>Users List</title>
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

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.6);
    }

    .modal-content {
        background-color: #1f2833;
        margin: 10% auto;
        padding: 20px;
        border-radius: 10px;
        width: 400px;
        color: #fff;
    }

    .modal-content input {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: none;
        border-radius: 5px;
    }

    .close {
        float: right;
        font-size: 24px;
        cursor: pointer;
        color: #66fcf1;
    }

    .close:hover {
        color: #45a29e;
    }
    </style>
</head>

<body>
    <div class="sidebar">
        <a href="admin.php">Home</a>
        <a href="breaches.php">Breaches List</a>
        <a href="users.php" class="active">Users List</a>
        <a href="admin_logout.php">Logout</a>
    </div>

    <div class="main">
        <h1>Users List</h1>

        <?php if($alert): ?>
        <div class="alert"><?= $alert ?></div>
        <?php endif; ?>

        <div class="cards">
            <div class="card">
                <h3>Total Users</h3>
                <p><?= $total_users ?></p>
            </div>
            <div class="card">
                <h3>Total Users with Breach Data</h3>
                <p><?= $total_breached_users ?></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $users->fetch_assoc()): ?>
                <tr data-id="<?= e($row['id']) ?>" data-username="<?= e($row['username']) ?>"
                    data-email="<?= e($row['email']) ?>">
                    <td><?= e($row['id']) ?></td>
                    <td><?= e($row['username']) ?></td>
                    <td><?= e($row['email']) ?></td>
                    <td>
                        <button class="btn edit-btn">✏️</button>
                        <a href="delete_user.php?id=<?= e($row['id']) ?>" onclick="return confirm('Delete this user?')"
                            class="btn">🗑️</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit User</h2>
            <form id="editForm">
                <input type="hidden" name="id" id="edit-id">
                <label>Username</label>
                <input type="text" name="username" id="edit-username" required>
                <label>Email</label>
                <input type="email" name="email" id="edit-email" required>
                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
    const modal = document.getElementById("editModal");
    const closeBtn = document.querySelector(".close");
    const editForm = document.getElementById("editForm");

    document.querySelectorAll(".edit-btn").forEach(btn => {
        btn.addEventListener("click", function() {
            const row = this.closest("tr");
            document.getElementById("edit-id").value = row.dataset.id;
            document.getElementById("edit-username").value = row.dataset.username;
            document.getElementById("edit-email").value = row.dataset.email;
            modal.style.display = "block";
        });
    });

    closeBtn.onclick = () => modal.style.display = "none";
    window.onclick = e => {
        if (e.target == modal) modal.style.display = "none";
    }

    editForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const formData = new FormData(editForm);

        const res = await fetch("update_user.php", {
            method: "POST",
            body: formData
        });
        const data = await res.text();

        alert(data);
        modal.style.display = "none";
        location.reload();
    });
    </script>
</body>

</html>