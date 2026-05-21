-- ============================================================
-- Resort Dhauladhar - Complete Database Schema
-- Database: muyfmbpgzm (or your local database name)
-- ============================================================

-- ==================== ADMINS TABLE ====================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(150) NOT NULL DEFAULT 'Admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin (password: admin123 - change after first login)
INSERT INTO admins (username, email, password, name) VALUES 
('admin', 'admin@dhauladharheightsresort.com', '$2y$10$iRShaEGI.qV9VmwOZeWKDeOB8W4T0t8B0lAM9IOaoQWdvEft5rdoa', 'Admin')
ON DUPLICATE KEY UPDATE username = username;


-- ==================== BLOGS TABLE ====================
CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT,
    excerpt TEXT,
    featured_image VARCHAR(500) DEFAULT '',
    category VARCHAR(100) DEFAULT '',
    author VARCHAR(150) DEFAULT 'Admin',
    meta_description VARCHAR(500) DEFAULT '',
    meta_keywords VARCHAR(500) DEFAULT '',
    status ENUM('published', 'draft') DEFAULT 'published',
    views INT DEFAULT 0,
    sections JSON DEFAULT NULL,
    content_format VARCHAR(20) DEFAULT 'html',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_views (views)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ==================== CATEGORIES TABLE ====================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default categories
INSERT IGNORE INTO categories (name, slug) VALUES 
('Wedding', 'wedding'),
('Travel', 'travel'),
('Food', 'food'),
('Events', 'events'),
('Lifestyle', 'lifestyle');


-- ==================== GOOGLE REVIEWS TABLE ====================
CREATE TABLE IF NOT EXISTS google_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reviewer_name VARCHAR(150) NOT NULL,
    reviewer_image VARCHAR(500) DEFAULT '',
    rating INT DEFAULT 5,
    review_text TEXT NOT NULL,
    review_source VARCHAR(100) DEFAULT 'Website',
    review_date DATE DEFAULT NULL,
    guest_type VARCHAR(100) DEFAULT '',
    status TINYINT DEFAULT 1,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed default reviews
INSERT INTO google_reviews (reviewer_name, reviewer_image, rating, review_text, review_source, review_date, guest_type, display_order) VALUES 
('Karan M.', '', 5, 'The hotel has a great location and the rooms were very clean and spacious. It has an old English charm and the staff is very cooperative.', 'Google', CURDATE() - INTERVAL 14 DAY, 'Holiday | Family', 1),
('Pooja S.', '', 5, 'Excellent location in the heart of Dharamshala with easy access to amenities. Warm and calm place to stay with beautiful views of the tea gardens.', 'Google', CURDATE() - INTERVAL 30 DAY, 'Holiday | Couple', 2),
('Ravi T.', '', 5, 'Staff was courteous and the food was amazing, especially the butter chicken! Most of the food is Indian style and really yummy. Highly recommended.', 'Google', CURDATE() - INTERVAL 60 DAY, 'Holiday | Friends', 3),
('Neha G.', '', 5, 'Hotel Dhauladhar Dharamshala is one of the best accommodations when visiting Dharamshala. Situated near the market, very convenient and peaceful.', 'Google', CURDATE() - INTERVAL 90 DAY, 'Business | Solo', 4),
('Vikram J.', '', 5, 'Overall I had a very good and pleasant stay. The hotel staff and services were all convenient, and the location near the tea gardens is perfect for a morning walk.', 'Google', CURDATE() - INTERVAL 120 DAY, 'Holiday | Family', 5)
ON DUPLICATE KEY UPDATE reviewer_name = reviewer_name;
