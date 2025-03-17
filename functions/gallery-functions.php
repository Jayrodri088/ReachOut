<?php
require_once __DIR__ . '/db_connection.php';

if (!function_exists('getGalleryImages')) {
    function getGalleryImages($limit = 8) {
        return fetchAll("SELECT * FROM gallery ORDER BY created_at DESC LIMIT ?", [$limit]);
    }
}

if (!function_exists('getGalleryImageById')) {
    function getGalleryImageById($imageId) {
        return fetchRow("SELECT * FROM gallery WHERE id = ?", [$imageId]);
    }
}