<?php
// config/db.php
require_once __DIR__ . '/../classes/Database.php';

$dsn  = "mysql:host=localhost;dbname=training_db;charset=utf8mb4";
$user = "root";
$pass = "";

// Obtain the single shared PDO connection instance (Singleton Pattern)
$db = Database::getInstance($dsn, $user, $pass);