<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'complaint_db');
define('ADMIN_EMAIL', 'admin@vit.edu');

function getDB(): mysqli {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) die('DB Error: ' . $conn->connect_error);
    return $conn;
}
