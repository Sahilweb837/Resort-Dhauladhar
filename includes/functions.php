<?php
// Include database connection FIRST
require_once __DIR__ . '/db.php';

// Start session if not started
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    ini_set('session.cookie_path', '/');
    session_start();
}

// ==================== AUTHENTICATION FUNCTIONS ====================

function requireLogin() {
    if (!isset($_SESSION['admin_id']) && !isset($_SESSION['admin_logged_in'])) {
        $basePath = getBaseUrl();
        header('Location: ' . $basePath . '/admin/index.php');
        exit();
    }
}

function isLoggedIn() {
    return isset($_SESSION['admin_id']) || (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true);
}

// ==================== BLOG FUNCTIONS ====================

function getAllBlogs($limit = null, $offset = 0, $status = null) {
    try {
        $pdo = getDB();
        $sql = "SELECT * FROM blogs";
        $params = [];
        
        if ($status) {
            $sql .= " WHERE status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        if ($limit) {
            $limit = intval($limit);
            $offset = intval($offset);
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error in getAllBlogs: " . $e->getMessage());
        return [];
    }
}

function getBlogById($id) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error in getBlogById: " . $e->getMessage());
        return false;
    }
}

function getBlogBySlug($slug) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM blogs WHERE slug = ? AND status = 'published'");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error in getBlogBySlug: " . $e->getMessage());
        return false;
    }
}

function createBlog($data) {
    $pdo = getDB();
    
    $sql = "INSERT INTO blogs (title, slug, content, excerpt, featured_image, category, author, meta_description, meta_keywords, status, sections, content_format) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['title'],
        $data['slug'],
        $data['content'],
        $data['excerpt'] ?? '',
        $data['featured_image'] ?? '',
        $data['category'] ?? '',
        $data['author'] ?? 'Admin',
        $data['meta_description'] ?? '',
        $data['meta_keywords'] ?? '',
        $data['status'] ?? 'published',
        $data['sections'] ?? null,
        $data['content_format'] ?? 'html'
    ]);
}

function updateBlog($id, $data) {
    $pdo = getDB();
    
    $sql = "UPDATE blogs SET 
            title = ?, slug = ?, content = ?, excerpt = ?, 
            featured_image = ?, category = ?, author = ?, 
            meta_description = ?, meta_keywords = ?, status = ?,
            sections = ?, content_format = ?
            WHERE id = ?";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['title'],
        $data['slug'],
        $data['content'],
        $data['excerpt'] ?? '',
        $data['featured_image'] ?? '',
        $data['category'] ?? '',
        $data['author'] ?? 'Admin',
        $data['meta_description'] ?? '',
        $data['meta_keywords'] ?? '',
        $data['status'] ?? 'published',
        $data['sections'] ?? null,
        $data['content_format'] ?? 'html',
        $id
    ]);
}

function deleteBlog($id) {
    $pdo = getDB();
    
    // Get blog to delete image
    $blog = getBlogById($id);
    if ($blog && !empty($blog['featured_image'])) {
        deleteImage($blog['featured_image']);
    }
    
    $stmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
    return $stmt->execute([$id]);
}

function getBlogCount($status = null) {
    try {
        $pdo = getDB();
        
        if ($status) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM blogs WHERE status = ?");
            $stmt->execute([$status]);
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM blogs");
            $stmt->execute();
        }
        
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Error in getBlogCount: " . $e->getMessage());
        return 0;
    }
}

// FIXED: getRecentBlogs function - no parameter binding for LIMIT
function getRecentBlogs($limit = 5) {
    try {
        $pdo = getDB();
        $limit = intval($limit);
        $stmt = $pdo->query("SELECT * FROM blogs WHERE status = 'published' ORDER BY created_at DESC LIMIT $limit");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error in getRecentBlogs: " . $e->getMessage());
        return [];
    }
}

function getPopularBlogs($limit = 5) {
    try {
        $pdo = getDB();
        $limit = intval($limit);
        $stmt = $pdo->query("SELECT * FROM blogs WHERE status = 'published' ORDER BY views DESC LIMIT $limit");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error in getPopularBlogs: " . $e->getMessage());
        return [];
    }
}

// ==================== HELPER FUNCTIONS ====================

function createSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

function uploadImage($file, $uploadDir = 'uploads/') {
    // Create absolute path - always store in project root
    $projectRoot = dirname(__DIR__);
    $fullUploadDir = $projectRoot . '/' . $uploadDir;
    
    // Create directory if not exists
    if (!file_exists($fullUploadDir)) {
        if (!mkdir($fullUploadDir, 0777, true)) {
            error_log("Failed to create directory: " . $fullUploadDir);
            return false;
        }
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        error_log("Upload error code: " . $file['error']);
        return false;
    }
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        error_log("Invalid file type: " . $fileType);
        return false;
    }
    
    // Validate file size (5MB max)
    if ($file['size'] > 5 * 1024 * 1024) {
        error_log("File too large: " . $file['size']);
        return false;
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = time() . '_' . uniqid() . '.' . $extension;
    $targetPath = $fullUploadDir . $fileName;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $uploadDir . $fileName;
    } else {
        error_log("Failed to move uploaded file to: " . $targetPath);
        return false;
    }
}

function deleteImage($imagePath) {
    if (empty($imagePath)) {
        return false;
    }
    
    $filename = basename($imagePath);
    
    // Define all candidate paths on the server
    $candidates = [
        'admin/uploads/blogs/' . $filename,
        'uploads/blogs/' . $filename,
        'admin/uploads/reviews/' . $filename,
        'uploads/reviews/' . $filename,
        'admin/uploads/' . $filename,
        'uploads/' . $filename,
        ltrim($imagePath, '/'),
        ltrim(str_replace('../', '', $imagePath), '/')
    ];
    
    $projectRoot = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');
    
    foreach ($candidates as $cand) {
        $fullPath = $projectRoot . '/' . ltrim($cand, '/');
        if (file_exists($fullPath) && is_file($fullPath)) {
            @unlink($fullPath);
            return true;
        }
    }
    return false;
}

function getImageUrl($imagePath) {
    $basePath = getBaseUrl();

    if (empty($imagePath)) {
        return $basePath . '/images/default-blog.jpg';
    }
    
    // Remove leading slash if present
    $imagePath = ltrim($imagePath, '/');
    
    // Return relative path from root
    return $basePath . '/' . $imagePath;
}

/**
 * Get the base URL path for the project (e.g. /Resort-Dhauladhar)
 * Works whether called from root pages or admin subdirectory pages
 */
function getBaseUrl() {
    static $cachedBase = null;
    if ($cachedBase !== null) return $cachedBase;
    
    $docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
    // __DIR__ is /includes, so parent is the project root
    $projectRoot = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');
    
    if ($docRoot && $projectRoot !== $docRoot && strpos($projectRoot, $docRoot) === 0) {
        $cachedBase = substr($projectRoot, strlen($docRoot));
    } else {
        $cachedBase = '';
    }
    return $cachedBase;
}

/**
 * Get the correct browser-accessible URL for a blog image.
 * Checks multiple possible upload locations on disk to find the actual file.
 */
function getBlogImageUrl($imagePath) {
    if (empty($imagePath)) {
        return getBaseUrl() . '/images/default-blog.jpg';
    }
    
    // External URLs pass through
    if (preg_match('/^https?:\/\//', $imagePath)) {
        return $imagePath;
    }
    
    $basePath = getBaseUrl();
    $filename = basename($imagePath);
    $projectRoot = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');
    
    // All possible locations where blog images might exist on disk
    $candidates = [
        'uploads/blogs/' . $filename,
        'admin/uploads/blogs/' . $filename,
        'uploads/' . $filename,
        'admin/uploads/' . $filename,
    ];
    
    // Also try the raw stored path itself
    $rawPath = ltrim(str_replace('\\', '/', $imagePath), '/');
    if (!in_array($rawPath, $candidates)) {
        $candidates[] = $rawPath;
    }
    
    foreach ($candidates as $cand) {
        $fullPath = $projectRoot . '/' . $cand;
        if (file_exists($fullPath)) {
            return $basePath . '/' . $cand;
        }
    }
    
    // Fallback: return the stored path prefixed with base URL
    return $basePath . '/' . $rawPath;
}

/**
 * Get the correct browser-accessible URL for a reviewer image.
 * Checks multiple possible upload locations on disk.
 */
function getReviewerImageUrl($imagePath) {
    if (empty($imagePath)) {
        return getBaseUrl() . '/images/default-blog.jpg';
    }
    
    if (preg_match('/^https?:\/\//', $imagePath)) {
        return $imagePath;
    }
    
    $basePath = getBaseUrl();
    $filename = basename($imagePath);
    $projectRoot = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');
    
    $candidates = [
        'uploads/reviews/' . $filename,
        'admin/uploads/reviews/' . $filename,
        'uploads/' . $filename,
        'admin/uploads/' . $filename,
    ];
    
    $rawPath = ltrim(str_replace('\\', '/', $imagePath), '/');
    if (!in_array($rawPath, $candidates)) {
        $candidates[] = $rawPath;
    }
    
    foreach ($candidates as $cand) {
        $fullPath = $projectRoot . '/' . $cand;
        if (file_exists($fullPath)) {
            return $basePath . '/' . $cand;
        }
    }
    
    return $basePath . '/' . $rawPath;
}

/**
 * Increment blog view count
 */
function incrementBlogViews($blogId) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("UPDATE blogs SET views = views + 1 WHERE id = ?");
        $stmt->execute([$blogId]);
    } catch (PDOException $e) {
        error_log("Error incrementing blog views: " . $e->getMessage());
    }
}

function sanitize($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

function getCategories() {
    try {
        $pdo = getDB();
        // Check if categories table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'categories'");
        if ($stmt->rowCount() == 0) {
            // Create categories table
            $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL UNIQUE,
                slug VARCHAR(100) NOT NULL UNIQUE,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            
            // Insert default categories
            $default_categories = ['Wedding', 'Travel', 'Food', 'Events', 'Lifestyle'];
            $insertStmt = $pdo->prepare("INSERT IGNORE INTO categories (name, slug) VALUES (?, ?)");
            foreach ($default_categories as $cat) {
                $slug = createSlug($cat);
                $insertStmt->execute([$cat, $slug]);
            }
        }
        
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error getting categories: " . $e->getMessage());
        return [];
    }
}

function getDashboardStats() {
    $stats = [];
    
    try {
        $pdo = getDB();
        // Total blogs
        $stmt = $pdo->query("SELECT COUNT(*) FROM blogs");
        $stats['total_blogs'] = $stmt->fetchColumn() ?: 0;
        
        // Published blogs
        $stmt = $pdo->query("SELECT COUNT(*) FROM blogs WHERE status = 'published'");
        $stats['published_blogs'] = $stmt->fetchColumn() ?: 0;
        
        // Draft blogs
        $stmt = $pdo->query("SELECT COUNT(*) FROM blogs WHERE status = 'draft'");
        $stats['draft_blogs'] = $stmt->fetchColumn() ?: 0;
        
        return $stats;
    } catch (PDOException $e) {
        $stats['total_blogs'] = 0;
        $stats['published_blogs'] = 0;
        $stats['draft_blogs'] = 0;
    }
    
    return $stats;
}

// ==================== ADMIN AUTH FUNCTIONS ====================

function authenticateAdmin($username, $password) {
    $pdo = getDB();
    try {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_email'] = $user['email'];
            return true;
        }
        return false;
    } catch (PDOException $e) {
        error_log("Authentication error: " . $e->getMessage());
        return false;
    }
}

function logout() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    return true;
}

function getAdminById($id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id, username, email, name FROM admins WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// ==================== ENSURE UPLOAD DIRECTORY EXISTS ====================
function ensureUploadDirectory() {
    $projectRoot = dirname(__DIR__);
    
    // Ensure main uploads directory
    $mainUploadDir = $projectRoot . '/uploads/';
    if (!file_exists($mainUploadDir)) {
        mkdir($mainUploadDir, 0777, true);
        $indexFile = $mainUploadDir . 'index.php';
        if (!file_exists($indexFile)) {
            file_put_contents($indexFile, '<?php header("HTTP/1.0 403 Forbidden"); exit; ?>');
        }
    }
    
    // Ensure blog uploads directory
    $blogUploadDir = $projectRoot . '/uploads/blogs/';
    if (!file_exists($blogUploadDir)) {
        mkdir($blogUploadDir, 0777, true);
        $indexFile = $blogUploadDir . 'index.php';
        if (!file_exists($indexFile)) {
            file_put_contents($indexFile, '<?php header("HTTP/1.0 403 Forbidden"); exit; ?>');
        }
    }
    
    // Ensure reviews uploads directory
    $reviewUploadDir = $projectRoot . '/uploads/reviews/';
    if (!file_exists($reviewUploadDir)) {
        mkdir($reviewUploadDir, 0777, true);
        $indexFile = $reviewUploadDir . 'index.php';
        if (!file_exists($indexFile)) {
            file_put_contents($indexFile, '<?php header("HTTP/1.0 403 Forbidden"); exit; ?>');
        }
    }
    
    return $blogUploadDir;
}

// Call this function to ensure upload directory exists
ensureUploadDirectory();

// ==================== DATABASE UPDATE FUNCTIONS ====================
function ensureBlogTableColumns() {
    try {
        $pdo = getDB();
        // Check if blogs table exists first
        $stmt = $pdo->query("SHOW TABLES LIKE 'blogs'");
        if ($stmt->rowCount() > 0) {
            // Check if views column exists
            $stmt = $pdo->query("SHOW COLUMNS FROM blogs LIKE 'views'");
            if ($stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE blogs ADD COLUMN views INT DEFAULT 0 AFTER status");
            }
            // Check if sections column exists
            $stmt = $pdo->query("SHOW COLUMNS FROM blogs LIKE 'sections'");
            if ($stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE blogs ADD COLUMN sections JSON DEFAULT NULL AFTER views");
            }
            // Check if content_format column exists
            $stmt = $pdo->query("SHOW COLUMNS FROM blogs LIKE 'content_format'");
            if ($stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE blogs ADD COLUMN content_format VARCHAR(20) DEFAULT 'html' AFTER sections");
            }
        }
    } catch (PDOException $e) {
        // Table might not exist yet - that's OK
        error_log("Blog table not found, skipping column updates: " . $e->getMessage());
    }
}

 try {
    ensureBlogTableColumns();
} catch (Exception $e) {
 }

// ==================== GOOGLE REVIEWS SYSTEM ====================

function ensureReviewsTable() {
    try {
        $pdo = getDB();
        $stmt = $pdo->query("SHOW TABLES LIKE 'google_reviews'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("CREATE TABLE google_reviews (
                id INT AUTO_INCREMENT PRIMARY KEY,
                reviewer_name VARCHAR(150) NOT NULL,
                reviewer_image VARCHAR(500) DEFAULT '',
                rating INT DEFAULT 5,
                review_text TEXT NOT NULL,
                review_source VARCHAR(100) DEFAULT 'Google',
                review_date DATE DEFAULT NULL,
                guest_type VARCHAR(100) DEFAULT '',
                status TINYINT DEFAULT 1,
                display_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // Seed with existing resort reviews
            $seed = $pdo->prepare("INSERT INTO google_reviews 
                (reviewer_name, reviewer_image, rating, review_text, review_source, review_date, guest_type, display_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            $seed->execute([
                'Karan M.', '', 5,
                'The hotel has a great location and the rooms were very clean and spacious. It has an old English charm and the staff is very cooperative.',
                'Google', date('Y-m-d', strtotime('-2 weeks')), 'Holiday | Family', 1
            ]);
            $seed->execute([
                'Pooja S.', '', 5,
                'Excellent location in the heart of Dharamshala with easy access to amenities. Warm and calm place to stay with beautiful views of the tea gardens.',
                'Google', date('Y-m-d', strtotime('-1 month')), 'Holiday | Couple', 2
            ]);
            $seed->execute([
                'Ravi T.', '', 5,
                'Staff was courteous and the food was amazing, especially the butter chicken! Most of the food is Indian style and really yummy. Highly recommended.',
                'Google', date('Y-m-d', strtotime('-2 months')), 'Holiday | Friends', 3
            ]);
            $seed->execute([
                'Neha G.', '', 5,
                'Hotel Dhauladhar Dharamshala is one of the best accommodations when visiting Dharamshala. Situated near the market, very convenient and peaceful.',
                'Google', date('Y-m-d', strtotime('-3 months')), 'Business | Solo', 4
            ]);
            $seed->execute([
                'Vikram J.', '', 5,
                'Overall I had a very good and pleasant stay. The hotel staff and services were all convenient, and the location near the tea gardens is perfect for a morning walk.',
                'Google', date('Y-m-d', strtotime('-4 months')), 'Holiday | Family', 5
            ]);
        }
    } catch (PDOException $e) {
        error_log("Reviews table error: " . $e->getMessage());
    }
}

require_once __DIR__ . '/google-reviews.php';

function getTopReviews($limit = 2) {
    // 1. Try to fetch live reviews from Google API first
    $liveReviews = fetchLiveGoogleReviews($limit);
    if (!empty($liveReviews)) {
        return $liveReviews;
    }

    // 2. Fallback to Database seeded reviews
    try {
        ensureReviewsTable();
        $pdo = getDB();
        $limit = intval($limit);
        $stmt = $pdo->query("SELECT * FROM google_reviews WHERE status = 1 ORDER BY display_order ASC, review_date DESC LIMIT $limit");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching reviews: " . $e->getMessage());
        return [];
    }
}

function getAllReviews() {
    try {
        ensureReviewsTable();
        $pdo = getDB();
        $stmt = $pdo->query("SELECT * FROM google_reviews WHERE status = 1 ORDER BY display_order ASC, review_date DESC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching all reviews: " . $e->getMessage());
        return [];
    }
}

function getReviewStats() {
    // 1. Try to fetch live stats from Google API first
    $liveStats = fetchLiveGoogleStats();
    if ($liveStats !== null) {
        return $liveStats;
    }

    // 2. Fallback to Database seeded reviews
    try {
        ensureReviewsTable();
        $pdo = getDB();
        $stmt = $pdo->query("SELECT COUNT(*) as total, AVG(rating) as avg_rating FROM google_reviews WHERE status = 1");
        return $stmt->fetch();
    } catch (PDOException $e) {
        return ['total' => 0, 'avg_rating' => 0];
    }
}

function getAllAdminReviews() {
    try {
        ensureReviewsTable();
        $pdo = getDB();
        $stmt = $pdo->query("SELECT * FROM google_reviews ORDER BY created_at DESC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching all admin reviews: " . $e->getMessage());
        return [];
    }
}

function getReviewById($id) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM google_reviews WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error in getReviewById: " . $e->getMessage());
        return false;
    }
}

function createReview($data) {
    $pdo = getDB();
    $sql = "INSERT INTO google_reviews (reviewer_name, reviewer_image, rating, review_text, guest_type, status, review_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['reviewer_name'],
        $data['reviewer_image'] ?? '',
        $data['rating'] ?? 5,
        $data['review_text'],
        $data['guest_type'] ?? '',
        $data['status'] ?? 1,
        $data['review_date'] ?? date('Y-m-d')
    ]);
}

function updateReview($id, $data) {
    $pdo = getDB();
    $sql = "UPDATE google_reviews SET 
            reviewer_name = ?, reviewer_image = ?, rating = ?, 
            review_text = ?, guest_type = ?, status = ?, review_date = ?
            WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['reviewer_name'],
        $data['reviewer_image'] ?? '',
        $data['rating'] ?? 5,
        $data['review_text'],
        $data['guest_type'] ?? '',
        $data['status'] ?? 1,
        $data['review_date'] ?? date('Y-m-d'),
        $id
    ]);
}

function deleteReview($id) {
    $pdo = getDB();
    
    // Get review to delete image
    $review = getReviewById($id);
    if ($review && !empty($review['reviewer_image'])) {
        deleteImage($review['reviewer_image']);
    }
    
    $stmt = $pdo->prepare("DELETE FROM google_reviews WHERE id = ?");
    return $stmt->execute([$id]);
}
?>
