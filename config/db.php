<?php

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = ''; 
$DB_NAME = 'watchshadow';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die("DB connection failed: " . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

function e($s) {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}