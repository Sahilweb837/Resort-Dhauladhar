<?php
// Database configuration
// For LOCAL XAMPP: root / no password
// For PRODUCTION: update these credentials accordingly
$host = 'localhost';
$dbname = 'muyfmbpgzm';
$username = 'root';
$password = '';

// Create PDO connection function
function getDB() {
    static $pdo = null;
    static $connectionError = null;
    
    if ($connectionError !== null) {
        throw $connectionError;
    }

    if ($pdo === null) {
        try {
            $host = 'localhost';
            $dbname = 'muyfmbpgzm';
            $username = 'root';
            $password = '';
            
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Connection Failed: " . $e->getMessage());
            $connectionError = $e;
            throw $e;
        }
    }
    
    return $pdo;
}

// Keep a global variable name for older files
$pdo = null;
