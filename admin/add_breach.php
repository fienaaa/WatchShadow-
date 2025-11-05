<?php
// admin/add_breach.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php?msg=' . urlencode('Invalid request method.'));
    exit;
}


$keyword = trim($_POST['keyword'] ?? '');
$source = trim($_POST['source'] ?? '');
$leak_date = trim($_POST['leak_date'] ?? '');
$description = trim($_POST['description'] ?? '');

// Validate
$errors = [];
if ($keyword === '') $errors[] = 'Keyword (email) is required.';
if ($source === '') $errors[] = 'Source is required.';
if ($leak_date === '') $errors[] = 'Leak date is required.';
else {
    $d = DateTime::createFromFormat('Y-m-d', $leak_date);
    if (!$d || $d->format('Y-m-d') !== $leak_date) {
        $errors[] = 'Leak date format is invalid.';
    }
}

if (!empty($errors)) {
    header('Location: admin.php?msg=' . urlencode(implode(' ', $errors)));
    exit;
}

// Insert breach
$stmt = $mysqli->prepare("INSERT INTO breaches (keyword, source, leak_date, description, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param('ssss', $keyword, $source, $leak_date, $description);

if ($stmt->execute()) {
    // --- Send notification email ---
    $to = $keyword;
    $subject = "Alert: Your email found in a breach";
    $message = "Hello,\n\nWe detected your email in a breach.\n\n"
             . "Source: {$source}\n"
             . "Leak Date: {$leak_date}\n"
             . "Description: {$description}\n\n"
             . "Please take security precautions immediately.\n\n-- Admin Team";
    $headers = "From: no-reply@example.com\r\n";

    @mail($to, $subject, $message, $headers); // suppress errors, optional logging

    header('Location: admin.php?msg=' . urlencode("Breach added and notification sent to {$keyword}."));
    exit;
} else {
    header('Location: admin.php?msg=' . urlencode('Failed to add breach: ' . $stmt->error));
    exit;
}