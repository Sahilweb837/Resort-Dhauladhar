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
    static $connectionAttempted = false;

    if ($pdo !== null) {
        return $pdo;
    }

    if ($connectionAttempted && $pdo === null) {
        return null;
    }

    $connectionAttempted = true;
    global $host, $dbname, $username, $password;

    $hosts = ['localhost', '127.0.0.1'];
    $targetDb = !empty($dbname) ? $dbname : 'muyfmbpgzm';
    
    $credentialsList = [];
    if (!empty($username)) {
        $credentialsList[] = ['user' => $username, 'pass' => $password ?? ''];
    }
    $credentialsList[] = ['user' => 'muyfmbpgzm', 'pass' => 'ArawmMFQ8n'];
    $credentialsList[] = ['user' => 'root', 'pass' => ''];

    $lastException = null;

    foreach ($hosts as $h) {
        foreach ($credentialsList as $cred) {
            try {
                // Try connecting directly to target database
                $pdo = new PDO("mysql:host=$h;dbname=$targetDb;charset=utf8mb4", $cred['user'], $cred['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_TIMEOUT => 2
                ]);
                return $pdo;
            } catch (PDOException $e) {
                $lastException = $e;
                // If database doesn't exist (Error code 1049), auto-create it!
                if ($e->getCode() == 1049 || strpos($e->getMessage(), 'Unknown database') !== false) {
                    try {
                        $tmpPdo = new PDO("mysql:host=$h;charset=utf8mb4", $cred['user'], $cred['pass']);
                        $tmpPdo->exec("CREATE DATABASE IF NOT EXISTS `$targetDb` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        
                        $pdo = new PDO("mysql:host=$h;dbname=$targetDb;charset=utf8mb4", $cred['user'], $cred['pass'], [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                        ]);
                        return $pdo;
                    } catch (PDOException $e2) {
                        $lastException = $e2;
                    }
                }
            }
        }
    }

    return null;
}

// Keep a global variable name for older files
$pdo = null;
