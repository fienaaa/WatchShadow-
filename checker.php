<?php
require_once 'config/db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start(); // optional if you want to track logged-in users
$keyword_raw = $_GET['keyword'] ?? '';
$keyword = trim($keyword_raw);

if ($keyword === '') {
    header('Location: index.php');
    exit;
}

/* -------------------------
   1️⃣ CHECK LOCAL DATABASE
------------------------- */
$stmt = $mysqli->prepare("SELECT * FROM breaches WHERE keyword LIKE CONCAT('%', ?, '%')");
$stmt->bind_param('s', $keyword);
$stmt->execute();
$res = $stmt->get_result();
$found = $res->num_rows > 0;

/* -------------------------
   2️⃣ CHECK IF USER ALREADY SUBSCRIBED
------------------------- */
$user_email = 'izzrieqilhan@gmail.com'; // replace with session/email if logged in

$subscribed_stmt = $mysqli->prepare("SELECT id FROM subscribers WHERE email = ? AND keyword = ?");
$subscribed_stmt->bind_param('ss', $user_email, $keyword);
$subscribed_stmt->execute();
$subscribed_stmt->store_result();
$already_subscribed = $subscribed_stmt->num_rows > 0;
$subscribed_stmt->close();

/* -------------------------
   3️⃣ CALL AHMIA API (Dark Web Search)
------------------------- */
$ahmia_results = [];
$ahmia_url = "https://ahmia.fi/search/?q=" . urlencode($keyword);
$context = stream_context_create([
    "http" => [
        "header" => "User-Agent: DarkWebMonitorStudent/1.0\r\n"
    ]
]);

$ahmia_response = @file_get_contents($ahmia_url, false, $context);

if ($ahmia_response !== false) {
    $clean_html = html_entity_decode($ahmia_response, ENT_QUOTES | ENT_HTML5);
    preg_match_all('/<a\s+href="([^"]+)".*?>(.*?)<\ /i', $clean_html, $matches, PREG_SET_ORDER); foreach ($matches as
    $m) { $link=trim($m[1]); $title=trim(strip_tags($m[2])); if (strpos($link, '.onion' ) !==false) { if ($title==='' ||
    stripos($title, '.onion' ) !==false) { $title="Unnamed .onion site" ; } $ahmia_results[]=[ 'title'=> $title,
    'link' => $link
    ];
    }
    }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Search results for <?= htmlspecialchars($keyword) ?></title>
        <link rel="stylesheet" href="assets/css/style.css">
        <style>
        /* Minimal styles */
        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .alert.success {
            background: #4caf50;
            color: #fff;
        }

        .alert.info {
            background: #1b2838;
            color: #c5c6c7;
        }

        .ahmia-list li {
            margin: 5px 0;
        }

        .btn.primary {
            padding: 10px 20px;
            background: #45a29e;
            color: #0b0c10;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .btn.primary:hover {
            background: #66fcf1;
        }
        </style>
    </head>

    <body>
        <nav class="topnav">
            <div class="brand">Dark Web Monitor</div>
            <div class="nav-links">
                <a href="index.php">Home</a>
            </div>
        </nav>

        <main class="container">
            <h2>Search results for “<?= htmlspecialchars($keyword) ?>”</h2>

            <!-- Local Database -->
            <section class="card">
                <h3>Local Database</h3>
                <?php if ($found): ?>
                <table class="results-table">
                    <thead>
                        <tr>
                            <th>Keyword</th>
                            <th>Source</th>
                            <th>Date</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $res->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['keyword']) ?></td>
                            <td><?= htmlspecialchars($row['source']) ?></td>
                            <td><?= htmlspecialchars($row['leak_date']) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p class="alert info">✅ No matches found in local database.</p>
                <?php endif; ?>
            </section>

            <!-- Ahmia Dark Web -->
            <section class="card">
                <h3>Ahmia Dark Web Search</h3>
                <?php if (!empty($ahmia_results)): ?>
                <ul class="ahmia-list">
                    <?php foreach ($ahmia_results as $r): ?>
                    <li>
                        <a href="<?= htmlspecialchars($r['link']) ?>" target="_blank">🕵️
                            <?= htmlspecialchars($r['title']) ?></a><br>
                        <small>🔗 <?= htmlspecialchars($r['link']) ?></small>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p class="alert info">ℹ️ No related results found on Ahmia for this keyword.</p>
                <?php endif; ?>
            </section>

            <!-- Subscription Section -->
            <section class="card">
                <?php if ($already_subscribed): ?>
                <p class="alert success">✅ You are already subscribed to this keyword.</p>
                <?php else: ?>
                <form id="subscribeForm">
                    <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user_email) ?>" required>
                    <button class="btn primary" type="submit">Notify Me</button>
                    <div id="subscribeAlert"></div>
                </form>
                <?php endif; ?>
            </section>

            <script>
            // Ajax subscription
            const form = document.getElementById('subscribeForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const data = new FormData(form);
                    data.append('ajax', '1');

                    fetch('subscribe.php', {
                            method: 'POST',
                            body: data
                        })
                        .then(res => res.json())
                        .then(res => {
                            const alertDiv = document.getElementById('subscribeAlert');
                            alertDiv.innerHTML = '';
                            if (res.success) {
                                alertDiv.innerHTML = `<div class="alert success">${res.message}</div>`;
                                form.style.display = 'none';
                            } else {
                                alertDiv.innerHTML = `<div class="alert error">${res.message}</div>`;
                            }
                        });
                });
            }
            </script>

        </main>
    </body>

    </html>