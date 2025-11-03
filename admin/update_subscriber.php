<?php
require_once '../config/db.php';
if(isset($_POST['id'])){
    $id = (int)$_POST['id'];
    $email = $mysqli->real_escape_string($_POST['email']);
    $keyword = $mysqli->real_escape_string($_POST['keyword']);

    $mysqli->query("UPDATE subscribers SET email='$email', keyword='$keyword' WHERE id=$id");

    header("Location: subscribers.php?msg=Update subscriber completed");
    exit;
}