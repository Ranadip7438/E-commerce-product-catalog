<?php
declare(strict_types=1);

$host = '127.0.0.1';
$dbname = 'electro_catalog';
$username = 'root';
$password = '';

$mysqli = new mysqli(
    "localhost",
    "root",
    "",
    "electro_catalog"
);

if ($mysqli->connect_error) {
    die("Database connection failed");
}

$mysqli->set_charset("utf8mb4");
