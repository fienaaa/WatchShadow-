<?php
require_once '../config/db.php';
if(isset($_GET['id'])){
    $id = (int)$_GET['id'];
    $mysqli->query("DELETE FROM subscribers WHERE id=$id");

    header("Location: subscribers.php?msg=Delete subscriber completed");
    exit;
}