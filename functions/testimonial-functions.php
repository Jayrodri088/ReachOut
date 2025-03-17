<?php
require_once __DIR__ . '/db_connection.php';

if (!function_exists('getTestimonials')) {
    function getTestimonials($limit = 4) {
        return fetchAll("SELECT t.*, u.username as user_name, u.avatar as user_avatar, u.role as user_role 
                        FROM testimonials t 
                        JOIN users u ON t.user_id = u.id 
                        WHERE t.status = 'approved' 
                        ORDER BY t.created_at DESC 
                        LIMIT ?", 
                        [$limit]);
    }
}