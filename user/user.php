<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../config/db.php';

// 🔒 Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// 🧩 Use the logged-in user's username or email as keyword
$keyword = trim($_SESSION['username'] ?? '');

// 🧠 Helper function
function generateResponseSteps($fields) {
    $steps = [];
    $fieldsLower = strtolower($fields);

    if (strpos($fieldsLower, 'password') !== false) {
        $steps[] = "Change your password immediately.";
        $steps[] = "Avoid reusing passwords on other accounts.";
        $steps[] = "Use a password manager for stronger credentials.";
    }

    if (strpos($fieldsLower, 'email') !== false || strpos($fieldsLower, 'username') !== false) {
        $steps[] = "Enable Two-Factor Authentication (2FA).";
        $steps[] = "Watch out for phishing emails.";
    }

    if (strpos($fieldsLower, 'credit') !== false || strpos($fieldsLower, 'card') !== false || strpos($fieldsLower, 'nric') !== false) {
        $steps[] = "Monitor bank accounts for suspicious activity.";
        $steps[] = "Contact your bank to request a fraud alert.";
    }

    if (empty($steps)) {
        $steps[] = "Monitor your account for unusual activity.";
        $steps[] = "Change your password if you notice anything suspicious.";
    }

    $output = "<ul>";
    foreach ($steps as $s) $output .= "<li>" . htmlspecialchars($s) . "</li>";
    $output .= "</ul>";
    return $output;
}

/* -------------------------
   1️⃣ LOCAL DATABASE
------------------------- */
$stmt = $mysqli->prepare("SELECT * FROM breaches WHERE keyword LIKE CONCAT('%', ?, '%')");
$stmt->bind_param('s', $keyword);
$stmt->execute();
$res = $stmt->get_result();
$found = $res->num_rows > 0;

/* -------------------------
   2️⃣ AHMIA DARK WEB SEARCH
------------------------- */
$ahmia_results = [];
$ahmia_url = "https://ahmia.fi/search/?q=" . urlencode($keyword);
$context = stream_context_create(["http" => ["header" => "User-Agent: DarkWebMonitor/1.0\r\n"]]);
$ahmia_response = @file_get_contents($ahmia_url, false, $context);

if ($ahmia_response !== false) {
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML($ahmia_response);
    $xpath = new DOMXPath($dom);
    $links = $xpath->query('//a[contains(@href,".onion")]');

    foreach ($links as $link) {
        /** @var DOMElement $link */
        $href = $link->getAttribute('href');
        $title = trim($link->textContent);
        if ($title === '' || stripos($title, '.onion') !== false) {
            $title = "Unnamed .onion site";
        }
        $ahmia_results[] = ['title' => $title, 'link' => $href];
    }
}

/* -------------------------
   3️⃣ BREACHDIRECTORY API
------------------------- */
$breach_results = [];
$breach_url = "https://breachdirectory.com/api/search?query=" . urlencode($keyword);
$breach_response = @file_get_contents($breach_url);

if ($breach_response !== false) {
    $breachData = json_decode($breach_response, true);
    if (isset($breachData['success']) && $breachData['success'] === true && !empty($breachData['result'])) {
        foreach ($breachData['result'] as $r) {
            $breach_results[] = [
                'source' => $r['source'] ?? 'Unknown Source',
                'date_added' => $r['date_added'] ?? 'N/A',
                'fields' => $r['fields'] ?? 'Unknown Data'
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Dashboard - Dark Web Monitor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #0b0c10;
        color: #fff;
        font-family: 'Poppins', sans-serif;
    }

    .container {
        margin-top: 40px;
        background: #1f2833;
        padding: 30px;
        border-radius: 12px;
    }

    h2 {
        color: #66fcf1;
        text-align: center;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 25px;
    }

    th {
        background-color: #45a29e;
        color: #0b0c10;
        padding: 10px;
    }

    td {
        padding: 10px;
        border-bottom: 1px solid #45a29e;
        vertical-align: top;
    }

    .alert {
        background: #16202a;
        border-left: 4px solid #45a29e;
        padding: 10px;
        border-radius: 8px;
    }

    nav {
        background: #1f2833;
        padding: 10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    nav a {
        color: #66fcf1;
        margin: 0 10px;
        text-decoration: none;
    }

    nav a:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <nav>
        <div><strong>Dark Web Monitor</strong></div>
        <div>
            <a href="user.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <h2>Dark Web Report for <?= htmlspecialchars($keyword) ?></h2>

        <h4>Local Database</h4>
        <table>
            <thead>
                <tr>
                    <th>Information</th>
                    <th>Steps</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($found): while ($row = $res->fetch_assoc()): ?>
                <tr>
                    <td>
                        <strong>Keyword:</strong> <?= htmlspecialchars($row['keyword']) ?><br>
                        <strong>Source:</strong> <?= htmlspecialchars($row['source']) ?><br>
                        <strong>Date:</strong> <?= htmlspecialchars($row['leak_date']) ?><br>
                        <strong>Description:</strong> <?= htmlspecialchars($row['description']) ?>
                    </td>
                    <td><?= generateResponseSteps($row['description']) ?></td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="2" class="alert">✅ No matches found in local database.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h4>Ahmia Dark Web Search</h4>
        <table>
            <thead>
                <tr>
                    <th>Result</th>
                    <th>Steps</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ahmia_results)): foreach ($ahmia_results as $r): ?>
                <tr>
                    <td><a href="<?= htmlspecialchars($r['link']) ?>" target="_blank">🕵️
                            <?= htmlspecialchars($r['title']) ?></a><br>
                        <small>🔗 <?= htmlspecialchars($r['link']) ?></small>
                    </td>
                    <td><?= generateResponseSteps($r['title']) ?></td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="2" class="alert">ℹ️ No related results found on Ahmia.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h4>BreachDirectory API</h4>
        <table>
            <thead>
                <tr>
                    <th>Leaked Info</th>
                    <th>Steps</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($breach_results)): foreach ($breach_results as $b): ?>
                <tr>
                    <td>
                        <strong>Leaked Site:</strong> <?= htmlspecialchars($b['source']) ?><br>
                        <strong>Date Added:</strong> <?= htmlspecialchars($b['date_added']) ?><br>
                        <strong>Data Exposed:</strong> <?= htmlspecialchars($b['fields']) ?>
                    </td>
                    <td><?= generateResponseSteps($b['fields']) ?></td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="2" class="alert">✅ No breaches found in BreachDirectory.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>