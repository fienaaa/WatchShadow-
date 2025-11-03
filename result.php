<?php
error_reporting(E_ALL);
require_once 'config/db.php'; // Database connection

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

// Log search
$log_stmt = $mysqli->prepare("INSERT INTO search_logs (keyword, found) VALUES (?, ?)");
$found_int = $found ? 1 : 0;
$log_stmt->bind_param('si', $keyword, $found_int);
$log_stmt->execute();
$log_stmt->close();

/* -------------------------
   2️⃣ CALL AHMIA API (Dark Web Search)
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
    libxml_use_internal_errors(true); // suppress HTML warnings
    $dom = new DOMDocument();
    $dom->loadHTML($ahmia_response);
    $xpath = new DOMXPath($dom);

    // Select all <a> tags that contain .onion
    $links = $xpath->query('//a[contains(@href,".onion")]');

    /** @var DOMElement $link */
   foreach ($links as $link) {
    /** @var DOMElement $link */
    $href = $link->getAttribute('href');
    $title = trim($link->textContent);

    $parent = $link->parentNode;

    // Only call getElementsByTagName if parent is DOMElement
    if ($parent instanceof DOMElement) {
        $spans = $parent->getElementsByTagName('span');
        foreach ($spans as $span) {
            if (trim($span->textContent) !== '') {
                $title = trim($span->textContent);
                break;
            }
        }
    }

    if ($title === '' || stripos($title, '.onion') !== false) {
        $title = "Unnamed .onion site";
    }

    $ahmia_results[] = [
        'title' => $title,
        'link' => $href
    ];
}

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Search results for <?= htmlspecialchars($keyword) ?></title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
    body {
        background: #0b0c10;
        color: #fff;
        font-family: "Poppins", sans-serif;
        margin: 0;
    }

    .topnav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #1f2833;
        padding: 10px 20px;
        flex-wrap: wrap;
    }

    .brand {
        font-size: 1.3rem;
        font-weight: bold;
        color: #66fcf1;
    }

    .nav-links a {
        color: #c5c6c7;
        margin-left: 15px;
        text-decoration: none;
        font-weight: 500;
    }

    .nav-links a:hover {
        color: #66fcf1;
    }

    .container {
        max-width: 900px;
        margin: 30px auto;
        padding: 15px;
    }

    .card {
        background: #1f2833;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }

    .card h3 {
        border-bottom: 2px solid #45a29e;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .results-table {
        width: 100%;
        border-collapse: collapse;
        color: #fff;
    }

    .results-table th,
    .results-table td {
        padding: 10px;
        border-bottom: 1px solid #45a29e;
    }

    .results-table th {
        background: #45a29e;
        color: #0b0c10;
    }

    .alert {
        padding: 10px;
        border-radius: 8px;
    }

    .alert.success {
        background: #0b5345;
        color: #a8ffb1;
    }

    .alert.info {
        background: #1b2838;
        color: #c5c6c7;
    }

    .ahmia-list {
        list-style-type: none;
        padding: 0;
    }

    .ahmia-list li {
        background: rgba(255, 255, 255, 0.05);
        margin: 8px 0;
        padding: 10px;
        border-radius: 8px;
        transition: 0.2s ease;
    }

    .ahmia-list li:hover {
        background: rgba(102, 252, 241, 0.1);
    }

    .ahmia-list a {
        color: #66fcf1;
        font-weight: bold;
        text-decoration: none;
    }

    .ahmia-list small {
        color: #c5c6c7;
        font-size: 13px;
    }

    .btn.primary {
        display: inline-block;
        background: #45a29e;
        color: #0b0c10;
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
        transition: 0.2s;
    }

    .btn.primary:hover {
        background: #66fcf1;
    }

    @media (max-width: 600px) {
        .container {
            padding: 10px;
        }

        .results-table th,
        .results-table td {
            padding: 8px;
            font-size: 14px;
        }

        .btn.primary {
            width: 100%;
            text-align: center;
            padding: 12px;
        }
    }
    </style>
</head>

<body>
    <nav class="topnav">
        <div class="brand">Dark Web Monitor</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="admin_login.php">Admin</a>
        </div>
    </nav>

    <main class="container">
        <h2>Search results for “<?= htmlspecialchars($keyword) ?>”</h2>

        <!-- 1️⃣ Local Database -->
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
            <p class="alert success">✅ No matches found in local database.</p>
            <?php endif; ?>
        </section>

        <!-- 2️⃣ Ahmia Dark Web Results -->
        <section class="card">
            <h3>Ahmia Dark Web Search</h3>
            <?php if (!empty($ahmia_results)): ?>
            <ul class="ahmia-list">
                <?php foreach ($ahmia_results as $r): ?>
                <li>
                    <a href="<?= htmlspecialchars($r['link']) ?>" target="_blank">
                        🕵️ <?= htmlspecialchars($r['title']) ?>
                    </a><br>
                    <small>🔗 <?= htmlspecialchars($r['link']) ?></small>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <p class="alert info">ℹ️ No related results found on Ahmia for this keyword.</p>
            <?php endif; ?>
        </section>

        <section class="subscribe-cta">
            <a href="subscribe.php?keyword=<?= urlencode($keyword) ?>" class="btn primary">Notify Me</a>
        </section>
    </main>
</body>

</html>