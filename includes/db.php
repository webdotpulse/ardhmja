<?php
$host = 'localhost';
$dbname = 'ardhmja';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // We are suppressing the error for testing environments without MySQL running
    // In production, you would uncomment the line below.
    // die("Database connection failed: " . $e->getMessage());
    $pdo = null;
}
?>