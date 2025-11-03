<?php
session_start();
require_once '../config/db.php';
if(!isset($_SESSION['admin_id'])) header('Location: admin_login.php');

$notify_keyword = trim($_POST['notify_keyword'] ?? '');
$subject = $_POST['subject'] ?? 'Alert';
$body = $_POST['body'] ?? '';

if($notify_keyword) {
    $stmt = $mysqli->prepare("SELECT email FROM subscribers WHERE keyword LIKE CONCAT('%', ?, '%')");
    $stmt->bind_param('s', $notify_keyword);
    $stmt->execute();
    $res = $stmt->get_result();
    $count = 0;
    while($row = $res->fetch_assoc()) {
        $to = $row['email'];
        $headers = "From: no-reply@localhost\r\n";
        if(@mail($to, $subject, $body, $headers)) $count++;
    }
    $_SESSION['notify_msg'] = "Notifications sent to $count subscriber(s)";
} else {
    $_SESSION['notify_msg'] = "Provide a keyword";
}

header("Location: admin.php");
exit;