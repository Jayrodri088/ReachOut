<?php
require_once __DIR__ . '/db_connection.php';

if (!function_exists('getFeaturedVideos')) {
    function getFeaturedVideos($limit = 6) {
        $videos = fetchAll("SELECT * FROM videos WHERE featured = 1 ORDER BY created_at DESC LIMIT ?", [$limit]);
        foreach ($videos as &$video) {
            $video['recent_comments'] = getVideoComments($video['id'], 1); // Limit to 1 for recent comment
            $video['comment_count'] = countVideoComments($video['id']);
        }
        return $videos;
    }
}

if (!function_exists('getVideoById')) {
    function getVideoById($videoId) {
        return fetchRow("SELECT * FROM videos WHERE id = ?", [$videoId]);
    }
}

if (!function_exists('getVideoComments')) {
    function getVideoComments($videoId, $limit = 10, $offset = 0) {
        $comments = fetchAll("SELECT c.*, u.username, u.avatar as user_avatar 
                            FROM comments c 
                            JOIN users u ON c.user_id = u.id 
                            WHERE c.video_id = ? 
                            ORDER BY c.created_at DESC 
                            LIMIT ? OFFSET ?", 
                            [$videoId, $limit, $offset]);
        
        // Transform the comments array to match the expected structure
        $transformedComments = [];
        foreach ($comments as $comment) {
            $transformedComments[] = [
                'username' => $comment['username'],
                'comment' => $comment['content'], // Map 'content' to 'comment'
                'user_avatar' => $comment['user_avatar'],
                'created_at' => $comment['created_at']
            ];
        }
        return $transformedComments;
    }
}

if (!function_exists('countVideoComments')) {
    function countVideoComments($videoId) {
        $result = fetchRow("SELECT COUNT(*) as count FROM comments WHERE video_id = ?", [$videoId]);
        return $result ? $result['count'] : 0;
    }
}

if (!function_exists('addVideoComment')) {
    function addVideoComment($videoId, $userId, $content) {
        $data = [
            'video_id' => $videoId,
            'user_id' => $userId,
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s')
        ];
        return insertRecord('comments', $data);
    }
}

if (!function_exists('getUserVideos')) {
    function getUserVideos($userId, $limit = 3) {
        $videos = fetchAll("SELECT * FROM videos WHERE user_id = ? ORDER BY created_at DESC LIMIT ?", [$userId, $limit]);
        foreach ($videos as &$video) {
            $video['recent_comments'] = getVideoComments($video['id'], 1);
            $video['comment_count'] = countVideoComments($video['id']);
        }
        return $videos;
    }
}

if (!function_exists('getUserProjects')) {
    function getUserProjects($userId, $limit = 3) {
        // Assuming a projects table with a user_id field or a join table for user-project relationships
        return fetchAll("SELECT p.* FROM projects p JOIN project_users pu ON p.id = pu.project_id WHERE pu.user_id = ? ORDER BY p.created_at DESC LIMIT ?", [$userId, $limit]);
    }
}

if (!function_exists('getUserRecentActivity')) {
    function getUserRecentActivity($userId, $limit = 5) {
        // Assuming an activity_log table to track user actions (e.g., comments, uploads)
        return fetchAll("SELECT * FROM activity_log WHERE user_id = ? ORDER BY created_at DESC LIMIT ?", [$userId, $limit]);
    }
}