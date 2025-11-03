<?php
require_once '../config/db.php';
if(isset($_GET['id'])){
    $id = (int)$_GET['id'];
    $mysqli->query("DELETE FROM breaches WHERE id=$id");

    header("Location: breaches.php?msg=Delete breach completed");
    exit;
}