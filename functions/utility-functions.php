<?php
require_once __DIR__ . '/db_connection.php';

if (!function_exists('sanitizeInput')) {
    function sanitizeInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('generateToken')) {
    function generateToken($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date, $format = 'F j, Y') {
        return date($format, strtotime($date));
    }
}

if (!function_exists('isValidUrl')) {
    function isValidUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}

if (!function_exists('createSlug')) {
    function createSlug($string) {
        return strtolower(trim(preg_replace('/[^a-zA-Z0-9-]+/', '-', $string), '-'));
    }
}

if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: $url");
        exit;
    }
}

if (!function_exists('getEnumValues')) {
    function getEnumValues($column, $table) {
        $conn = getDbConnection();
        $sql = "SHOW COLUMNS FROM $table LIKE '$column'";
        $stmt = $conn->query($sql);
        $type = $stmt->fetch(PDO::FETCH_ASSOC)['Type'];
        preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
        return explode("','", $matches[1]);
    }
}