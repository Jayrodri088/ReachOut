<?php
require_once __DIR__ . '/db_connection.php';

if (!function_exists('getUpcomingEvents')) {
    function getUpcomingEvents($limit = 3) {
        return fetchAll("SELECT * FROM events 
                        WHERE event_date >= CURDATE() 
                        ORDER BY event_date ASC, event_time ASC 
                        LIMIT ?", 
                        [$limit]);
    }
}

if (!function_exists('getEventById')) {
    function getEventById($eventId) {
        return fetchRow("SELECT * FROM events WHERE id = ?", [$eventId]);
    }
}