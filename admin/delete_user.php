<?php
require_once '../config/db.php';
session_start();

if(!isset($_SESSION['admin_id'])) header('Location: admin_login.php');

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: users.php?msg=User+deleted+successfully");
    exit;
} else {
    header("Location: users.php?msg=Invalid+request");
    exit;
}
?>