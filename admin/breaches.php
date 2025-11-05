<?php
session_start();
require_once '../config/db.php';
if(!isset($_SESSION['admin_id'])) header('Location: admin_login.php');

$breaches = $mysqli->query("SELECT * FROM breaches ORDER BY leak_date DESC");

$alert = '';
if(isset($_GET['msg'])){
    $alert = htmlspecialchars($_GET['msg'], ENT_QUOTES, 'UTF-8');
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Breaches List</title>
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

    table {
        width: 100%;
        border-collapse: collapse;
        background: #1f2833;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 20px;
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
        <a href="breaches.php" class="active">Breaches List</a>
        <a href="users.php">Users List</a>
        <a href="admin_logout.php">Logout</a>
    </div>

    <div class="main">
        <h1>Breaches List</h1>

        <?php if($alert): ?>
        <div class="alert"><?= $alert ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Keyword</th>
                    <th>Source</th>
                    <th>Leak Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $breaches->fetch_assoc()): ?>
                <tr>
                    <td><?= e($row['id']) ?></td>
                    <td><?= e($row['keyword']) ?></td>
                    <td><?= e($row['source']) ?></td>
                    <td><?= e($row['leak_date']) ?></td>
                    <td>
                        <button class="btn editBtn" data-id="<?= e($row['id']) ?>"
                            data-keyword="<?= e($row['keyword']) ?>" data-source="<?= e($row['source']) ?>"
                            data-leak="<?= e($row['leak_date']) ?>">✏️</button>
                        <a href="delete_breach.php?id=<?= e($row['id']) ?>"
                            onclick="return confirm('Delete this breach?')" class="btn">🗑️</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Edit Breach</h3>
            <form method="post" action="update_breach.php" id="editForm">
                <input type="hidden" name="id" id="modal_id">
                <label>Keyword</label>
                <input type="text" name="keyword" id="modal_keyword" required>
                <label>Source</label>
                <input type="text" name="source" id="modal_source">
                <label>Leak Date</label>
                <input type="date" name="leak_date" id="modal_leak">
                <label>Description</label>
                <textarea name="description" id="modal_description"></textarea>
                <button type="submit">Save</button>
                <button type="button" onclick="closeModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
    const editBtns = document.querySelectorAll('.editBtn');
    const modal = document.getElementById('editModal');
    editBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('modal_id').value = btn.dataset.id;
            document.getElementById('modal_keyword').value = btn.dataset.keyword;
            document.getElementById('modal_source').value = btn.dataset.source;
            document.getElementById('modal_leak').value = btn.dataset.leak;
            document.getElementById('modal_description').value = ''; // optional
            modal.style.display = 'block';
        });
    });

    function closeModal() {
        modal.style.display = 'none';
    }

    // Close modal when clicking outside content
    window.onclick = function(event) {
        if (event.target == modal) modal.style.display = 'none';
    }
    </script>
</body>

</html>