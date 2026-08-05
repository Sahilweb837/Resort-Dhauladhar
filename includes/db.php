<?php
// Database configuration
// For LOCAL XAMPP: root / no password
// For PRODUCTION: update these credentials accordingly
$host = 'localhost';
$dbname = 'muyfmbpgzm';
$username = 'muyfmbpgzm';
$password = 'ArawmMFQ8n';

// Create PDO connection function
function getDB() {
    static $pdo = null;
    static $connectionError = null;
    
    if ($connectionError !== null) {
        throw $connectionError;
    }

    if ($pdo === null) {
        $host = 'localhost';
        $dbname = 'muyfmbpgzm';
        $credentialsList = [
            ['user' => 'muyfmbpgzm', 'pass' => 'ArawmMFQ8n'],
            ['user' => 'root', 'pass' => '']
        ];
        
        $lastException = null;
        foreach ($credentialsList as $cred) {
            try {
                $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $cred['user'], $cred['pass']);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                break;
            } catch (PDOException $e) {
                $lastException = $e;
            }
        }
        
        if (!$pdo) {
            error_log("Database Connection Failed: " . ($lastException ? $lastException->getMessage() : 'Unknown error'));
            $connectionError = $lastException;
            throw $lastException;
        }
    }
    
    return $pdo;
}

// Keep a global variable name for older files
$pdo = null;
