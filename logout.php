<?php
require_once 'functions/config.php';
require_once 'functions/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_logout'])) {
    logoutUser();
    header("Location: index.php");
    exit;
}

header("Location: index.php");
exit;
?>