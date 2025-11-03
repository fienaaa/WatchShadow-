<?php
// index.php
error_reporting(E_ALL);
require_once 'config/db.php';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dark Web Monitor — Check</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <nav class="topnav">
        <div class="brand">
            <!-- <img src="assets/img/logo.png" alt="logo" style="height:36px;vertical-align:middle;"> -->
            <span>Dark Web Monitor</span>
        </div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="subscribe.php">Subscribes</a>
            <a href="admin/admin_login.php">Admin</a>
        </div>
        <button class="nav-toggle" id="navToggle">☰</button>
    </nav>

    <main class="container">
        <header class="hero">
            <h1>Check if your email or username has been exposed</h1>
            <p>Enter an email, username or keyword and press Check</p>
        </header>

        <section class="search-card">
            <form action="result.php" method="get" id="searchForm">
                <input type="text" name="keyword" id="keyword" placeholder="e.g. alice@example.com or johndoe" required>
                <div class="controls">
                    <button type="submit" class="btn primary">Check Now</button>
                    <a href="checker.php" class="btn outline">Notify Me</a>
                </div>
            </form>
            <small class="note">We store only minimal metadata for research. This is a student project — do not rely on
                it for production security.</small>
        </section>

        <section class="features">
            <div class="card">
                <h3>How it works</h3>
                <p>Search the local breach dataset. The admin can import or add breach records for scanning.</p>
            </div>
            <div class="card">
                <h3>Privacy</h3>
                <p>This demo stores only search logs and optional subscriber emails for alerts.</p>
            </div>
            <div class="card">
                <h3>Extendable</h3>
                <p>Future: integration with Ahmia/SpiderFoot and password k-anonymity checks.</p>
            </div>
        </section>

        <footer class="footer">
            <p>&copy; 2025 Dark Web Monitor — Student Project</p>
        </footer>
    </main>

    <script src="assets/js/script.js"></script>
</body>

</html>