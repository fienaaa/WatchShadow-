<?php
require_once '../config/db.php';
if(isset($_POST['id'])){
    $id = (int)$_POST['id'];
    $keyword = $mysqli->real_escape_string($_POST['keyword']);
    $source = $mysqli->real_escape_string($_POST['source']);
    $leak_date = $_POST['leak_date'];
    $description = $mysqli->real_escape_string($_POST['description']);

    $mysqli->query("UPDATE breaches SET keyword='$keyword', source='$source', leak_date='$leak_date', description='$description' WHERE id=$id");

    header("Location: breaches.php?msg=Update breach completed");
    exit;
}