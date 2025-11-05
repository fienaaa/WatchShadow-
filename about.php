<?php
error_reporting(E_ALL);
require_once 'config/db.php';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>About — Dark Web Monitor</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <!-- Same navbar as index.php -->
    <nav class="topnav">
        <div class="brand">
            <span>Dark Web Monitor</span>
        </div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="about.php" class="active">About</a>
            <a href="login.php">Login</a>
        </div>
        <button class="nav-toggle" id="navToggle">☰</button>
    </nav>

    <main class="container">
        <section class="search-card">
            <h2 class="center-title">About the Dark Web</h2>
            <p>
                The Dark Web is a hidden layer of the internet not indexed by search engines. While it enables privacy
                and anonymous communication, it is also exploited for illegal activities, including the sale of leaked
                data, hacking tools, and other criminal services.
            </p>

            <div style="margin-top:16px;">
                <iframe width="100%" height="400" src="https://www.youtube.com/embed/ngT2Aq1VBFc"
                    title="Awareness about Dark Web" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
            </div>
        </section>

        <section class="features">
            <div class="card">
                <h3>Why You Should Care</h3>
                <p>When personal data appears on the Dark Web, it can lead to identity theft, scams, blackmail, or
                    financial loss. Staying aware and monitoring your data is the first step toward protection.</p>
            </div>

            <div class="card">
                <h3>How WatchShadow Helps</h3>
                <p>WatchShadow scans both local and external databases to detect if your email, username, or domain
                    appears in known breaches and dark web leaks.</p>
            </div>

            <div class="card">
                <h3>Awareness & Safety</h3>
                <p>Learn how to secure your online accounts with strong passwords, 2FA, and by monitoring suspicious
                    activities.</p>
            </div>
        </section>

        <section class="search-card">
            <h2 class="center-title" style="margin-bottom:10px;">Main Features</h2>
            <div class="features">
                <div class="card">
                    <h4>🔍 Data Breach Check</h4>
                    <p>Find if your credentials were leaked on the dark web.</p>
                </div>
                <div class="card">
                    <h4>🔔 Alerts</h4>
                    <p>Automatic email notifications when new breaches are detected.</p>
                </div>
                <div class="card">
                    <h4>🛡️ Security Guidance</h4>
                    <p>Steps to recover and secure compromised accounts.</p>
                </div>
                <div class="card">
                    <h4>📊 Reports</h4>
                    <p>View past searches, subscriptions, and breach alerts.</p>
                </div>
            </div>

            <div style="text-align:center; margin-top:20px;">
                <p class="note">Or contact support if you suspect your data has been leaked.</p>
            </div>
        </section>

        <footer class="footer">
            <p>&copy; 2025 Dark Web Monitor — Student Project</p>
        </footer>
    </main>

    <script src="assets/js/script.js"></script>
</body>

</html>