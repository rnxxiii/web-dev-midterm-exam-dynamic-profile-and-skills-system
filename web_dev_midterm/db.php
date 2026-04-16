<?php
/**
 * db.php - Database connection using PDO
 */
$host = '127.0.0.1';
$dbname = 'adminpanel'; //database name
$username = 'root'; // Default for Ubuntu/XAMPP
$password = '';     // Default is empty for XAMPP; Ubuntu may vary

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>