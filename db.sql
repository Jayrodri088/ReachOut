-- Create database
CREATE DATABASE IF NOT EXISTS reachout_media;
USE reachout_media;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    skills ENUM('videographer','content_writer','editor','graphic_designer','photographer','social_media_manager','audio_engineer','web_developer') NOT NULL,
    role VARCHAR(30) DEFAULT 'member',
    avatar VARCHAR(255) DEFAULT 'assets/images/default-avatar.png',
    bio TEXT,
    location VARCHAR(100),
    website VARCHAR(255),
    social_media JSON,
    created_at DATETIME NOT NULL,
    last_login DATETIME,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    INDEX (username),
    INDEX (email),
    INDEX (status)
);

-- Videos table
CREATE TABLE IF NOT EXISTS videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    video_url VARCHAR(255) NOT NULL,
    thumbnail_url VARCHAR(255) NOT NULL,
    duration VARCHAR(10) NOT NULL,
    views INT DEFAULT 0,
    likes INT DEFAULT 0,
    featured TINYINT(1) DEFAULT 0,
    category VARCHAR(50),
    tags VARCHAR(255),
    user_id INT,
    created_at DATETIME NOT NULL,
    updated_at DATETIME,
    status ENUM('published', 'draft', 'private') DEFAULT 'published',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX (category),
    INDEX (featured),
    INDEX (status)
);

-- Comments table
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    video_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    status ENUM('approved', 'pending', 'spam') DEFAULT 'approved',
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (video_id),
    INDEX (user_id),
    INDEX (status)
);

-- Events table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    end_date DATE,
    end_time TIME,
    location VARCHAR(255) NOT NULL,
    thumbnail_url VARCHAR(255),
    user_id INT,
    created_at DATETIME NOT NULL,
    updated_at DATETIME,
    status ENUM('upcoming', 'ongoing', 'past', 'canceled') DEFAULT 'upcoming',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX (event_date),
    INDEX (status)
);

-- Gallery table
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    thumbnail_url VARCHAR(255) NOT NULL,
    full_image_url VARCHAR(255) NOT NULL,
    user_id INT,
    category VARCHAR(50),
    tags VARCHAR(255),
    created_at DATETIME NOT NULL,
    status ENUM('published', 'draft', 'private') DEFAULT 'published',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX (category),
    INDEX (status)
);

-- Testimonials table
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    rating TINYINT(1),
    created_at DATETIME NOT NULL,
    status ENUM('approved', 'pending', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (status)
);

-- Sample data for testing (optional)
-- Insert sample user
INSERT INTO users (username, email, password, first_name, last_name, role, created_at, status)
VALUES 
('admin', 'admin@reachoutworld.com', '1234567890', 'Admin', 'User', 'admin', NOW(), 'active'),
('johndoe', 'john@example.com', '1234567890', 'John', 'Doe', 'member', NOW(), 'active'),
('janesmith', 'jane@example.com', '1234567890', 'Jane', 'Smith', 'member', NOW(), 'active');

-- Insert sample videos
INSERT INTO videos (title, description, video_url, thumbnail_url, duration, views, likes, featured, category, user_id, created_at, status)
VALUES 
('Welcome to ReachOut World', 'An introduction to our media network and mission', 'videos/welcome.mp4', 'assets/images/videos/welcome-thumb.jpg', '5:30', 1245, 87, 1, 'Introduction', 1, NOW(), 'published'),
('Advanced Video Editing Techniques', 'Learn professional editing techniques for your media projects', 'videos/editing.mp4', 'assets/images/videos/editing-thumb.jpg', '12:45', 865, 52, 1, 'Tutorial', 1, NOW(), 'published'),
('Interview with Media Expert', 'Exclusive interview with renowned media professional', 'videos/interview.mp4', 'assets/images/videos/interview-thumb.jpg', '18:22', 632, 41, 1, 'Interview', 2, NOW(), 'published'),
('Behind the Scenes: Studio Tour', 'Take a tour of our professional studio facilities', 'videos/studio-tour.mp4','assets/images/videos/studio-thumb.jpg', '15:00', 432, 29, 0, 'Behind the Scenes', 3, NOW(), 'published');

-- Insert sample comments
INSERT INTO comments (video_id, user_id, content, created_at, status)
VALUES 
(1, 2, 'Great introduction video! Really explains our mission well.', NOW(), 'approved'),
(2, 3, 'These editing tips are game-changers, thanks for sharing!', NOW(), 'approved'),
(3, 1, 'Fantastic interview, learned a lot about media trends.', NOW(), 'approved');

-- Insert sample events
INSERT INTO events (title, description, event_date, event_time, end_date, end_time, location, thumbnail_url, user_id, created_at, status)
VALUES 
('Media Workshop 2024', 'Annual media production workshop', '2024-03-15', '09:00:00', '2024-03-17', '17:00:00', 'Main Auditorium', 'assets/images/events/workshop-thumb.jpg', 1, NOW(), 'upcoming'),
('Live Streaming Seminar', 'Masterclass on live streaming techniques', '2024-04-02', '14:00:00', NULL, NULL, 'Online Zoom Meeting', 'assets/images/events/streaming-thumb.jpg', 2, NOW(), 'upcoming');

-- Insert gallery items
INSERT INTO gallery (title, description, thumbnail_url, full_image_url, user_id, category, created_at, status)
VALUES 
('Studio Setup 2024', 'Our new studio configuration', 'assets/images/gallery/studio-thumb.jpg', 'assets/images/gallery/studio-full.jpg', 1, 'Studio', NOW(), 'published'),
('Team Retreat', 'Annual team building activities', 'assets/images/gallery/retreat-thumb.jpg', 'assets/images/gallery/retreat-full.jpg', 2, 'Events', NOW(), 'published');

-- Insert testimonials
INSERT INTO testimonials (user_id, content, rating, created_at, status)
VALUES 
(2, 'ReachOut Media has transformed how we create content!', 5, NOW(), 'approved'),
(3, 'Excellent resources and supportive community.', 5, NOW(), 'approved');

-- Add media_categories table for better organization
CREATE TABLE IF NOT EXISTS media_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    parent_category_id INT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (parent_category_id) REFERENCES media_categories(id) ON DELETE SET NULL,
    INDEX (name)
);

-- Add notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('system', 'comment', 'like', 'event') NOT NULL,
    message TEXT NOT NULL,
    reference_id INT,
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (type),
    INDEX (is_read)
);

-- Add user subscriptions table
CREATE TABLE IF NOT EXISTS subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('active', 'expired', 'canceled') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (status),
    INDEX (plan)
);

-- Create full-text search indexes
ALTER TABLE videos ADD FULLTEXT(title, description);
ALTER TABLE events ADD FULLTEXT(title, description);
ALTER TABLE gallery ADD FULLTEXT(title, description);

-- Create view for active content
CREATE VIEW active_content AS
SELECT 'video' AS type, id, title, created_at FROM videos WHERE status = 'published'
UNION
SELECT 'event' AS type, id, title, created_at FROM events WHERE status = 'upcoming'
UNION
SELECT 'gallery' AS type, id, title, created_at FROM gallery WHERE status = 'published';

-- Verify inserts
SELECT * FROM users;
SELECT * FROM videos;
SELECT * FROM comments;

ALTER TABLE users 
ADD verified TINYINT(1) DEFAULT 0;

CREATE TABLE verification_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    token VARCHAR(64),
    expiration DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id)
);