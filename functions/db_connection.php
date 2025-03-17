<?php

if (defined('DB_CONNECTION_LOADED')) {
    return;
}
define('DB_CONNECTION_LOADED', true);

if (!defined('DB_HOST')) {
    require_once __DIR__ . '/config.php';
}

function getDbConnection() {
    static $conn = null;
    
    if ($conn === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            die("Sorry, there was a problem connecting to the database. Please try again later.");
        }
    }
    
    return $conn;
}

if (!function_exists('executeQuery')) {
    function executeQuery($sql, $params = []) {
        try {
            $conn = getDbConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query Execution Error: " . $e->getMessage() . " - SQL: " . $sql);
            return null;
        }
    }
}

if (!function_exists('fetchRow')) {
    function fetchRow($sql, $params = []) {
        $stmt = executeQuery($sql, $params);
        return $stmt ? $stmt->fetch() : null;
    }
}

if (!function_exists('fetchAll')) {
    function fetchAll($sql, $params = []) {
        $stmt = executeQuery($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }
}

if (!function_exists('insertRecord')) {
    function insertRecord($table, $data) {
        try {
            $conn = getDbConnection();
            $columns = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));
            
            $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
            $stmt = $conn->prepare($sql);
            
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->execute();
            return $conn->lastInsertId();
            
        } catch (PDOException $e) {
            // Log detailed error
            error_log("INSERT ERROR: " . $e->getMessage());
            error_log("SQL: $sql");
            error_log("DATA: " . print_r($data, true));
            return false;
        }
    }
}

if (!function_exists('updateRecord')) {
    function updateRecord($table, $data, $whereColumn, $whereValue) {
        $setClause = [];
        $params = [];
        
        foreach ($data as $column => $value) {
            $setClause[] = "$column = ?";
            $params[] = $value;
        }
        
        $params[] = $whereValue;
        $sql = "UPDATE " . $table . " SET " . implode(', ', $setClause) . " WHERE " . $whereColumn . " = ?";
        
        try {
            $conn = getDbConnection();
            $stmt = $conn->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Update Error: " . $e->getMessage() . " - SQL: " . $sql);
            return false;
        }
    }
}
