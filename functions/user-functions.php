<?php
require_once __DIR__ . '/db_connection.php';

if (!function_exists('getUserById')) {
    function getUserById($userId) {
        return fetchRow("SELECT * FROM users WHERE id = ?", [$userId]);
    }
}

if (!function_exists('getUserByUsername')) {
    function getUserByUsername($username) {
        return fetchRow("SELECT * FROM users WHERE username = ? OR email = ?", [$username, $username]);
    }
}

if (!function_exists('registerUser')) {
    function registerUser($userData) {
        $userData['password'] = password_hash($userData['password'], PASSWORD_BCRYPT, ['cost' => HASH_COST]);
        $userData['created_at'] = date('Y-m-d H:i:s');
        $userData['status'] = 'active';
        return insertRecord('users', $userData);
    }
}

if (!function_exists('loginUser')) {
    function loginUser($username, $password) {
        $user = getUserByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            updateRecord('users', ['last_login' => date('Y-m-d H:i:s')], 'id', $user['id']);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            return $user;
        }
        return false;
    }
}

if (!function_exists('logoutUser')) {
    function logoutUser() {
        $_SESSION = [];
        session_destroy();
    }
}

if (!function_exists('isUserLoggedIn')) {
    function isUserLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}