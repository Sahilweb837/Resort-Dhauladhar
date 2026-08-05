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

function getDefaultSampleBlogs() {
    return [
        [
            'id' => 1,
            'title' => 'Dream Destination Weddings in Dharamshala at Dhauladhar Heights',
            'slug' => 'destination-wedding-in-dharamshala',
            'category' => 'Wedding',
            'excerpt' => 'Exchange your vows surrounded by the majestic Dhauladhar mountains. Discover how our luxury resort makes your dream hill-station wedding unforgettable.',
            'content' => '<p>Planning a destination wedding in the serene hills of Himachal Pradesh? Hotel Dhauladhar Heights Resort offers breathtaking mountain vistas, exquisite banquet halls, open lawn venues, and five-star hospitality for your special day.</p><p>From royal outdoor mandaps overlooking tea gardens to customized catering and lavish guest suites, we turn every wedding celebration into a lifelong memory.</p>',
            'featured_image' => 'images/sangeet1.jpg',
            'author' => 'Admin',
            'status' => 'published',
            'created_at' => date('Y-m-d H:i:s'),
            'views' => 45
        ],
        [
            'id' => 2,
            'title' => 'Exploring Dharamshala: Top Attractions and Hill Station Experiences',
            'slug' => 'exploring-dharamshala-attractions',
            'category' => 'Travel',
            'excerpt' => 'From Kangra valley tea gardens to McLeod Ganj monasteries, discover the top places to visit during your stay at Dhauladhar Heights Resort.',
            'content' => '<p>Dharamshala is a sanctuary of peace, natural beauty, and vibrant culture. Located at the foothills of the Dhauladhar ranges, it offers everything from tranquil forest walks to historic temples and Tibetan heritage.</p><p>Key highlights include McLeod Ganj, Bhagsu Waterfall, Kunal Pathri Temple, and panoramic tea garden trails right outside our resort doors.</p>',
            'featured_image' => 'images/aboutbanner - Copy.jpg',
            'author' => 'Admin',
            'status' => 'published',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
            'views' => 38
        ],
        [
            'id' => 3,
            'title' => 'Luxury Accommodation Guide: Executive Suites & Presidential Living',
            'slug' => 'luxury-accommodation-guide-dharamshala',
            'category' => 'Lifestyle',
            'excerpt' => 'Experience ultimate luxury, mountain views, and modern comforts in our signature suites and executive rooms at Dhauladhar Heights.',
            'content' => '<p>Whether traveling for business or leisure, choosing the right room enhances your mountain getaway. Our resort features Executive Rooms, Executive Suites with separate living spaces, and exclusive Presidential Suites equipped with top-tier amenities, smart TVs, high-speed Wi-Fi, and private balconies facing the snow-capped peaks.</p>',
            'featured_image' => 'images/presidentialmain.jpg',
            'author' => 'Admin',
            'status' => 'published',
            'created_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
            'views' => 62
        ],
        [
            'id' => 4,
            'title' => 'Culinary Journey: Authentic Himachali & International Flavors',
            'slug' => 'authentic-himachali-food-guide',
            'category' => 'Food',
            'excerpt' => 'Indulge in delicious multi-cuisine dining, traditional Himachali specialties, and soothing teas served at our hill-view restaurant.',
            'content' => '<p>At Dhauladhar Heights Resort, dining is an extraordinary experience. Enjoy freshly prepared North Indian, Continental, and local Himachali delicacies crafted by our executive chefs using organic regional ingredients.</p>',
            'featured_image' => 'images/DSC09149-scaled.jpg',
            'author' => 'Admin',
            'status' => 'published',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 week')),
            'views' => 29
        ],
        [
            'id' => 5,
            'title' => 'Corporate Retreats & Event Venues in the Himalayas',
            'slug' => 'corporate-retreats-event-venues-dharamshala',
            'category' => 'Events',
            'excerpt' => 'Host high-impact corporate conferences, annual meets, and private celebrations in our versatile event venues with state-of-the-art facilities.',
            'content' => '<p>Combine business with nature\'s tranquility. Our resort provides spacious conference halls, high-speed connectivity, break-out zones, and curated team-building activities set against the backdrop of Dharamshala\'s mountain wilderness.</p>',
            'featured_image' => 'images/DSC09631-scaled.jpg',
            'author' => 'Admin',
            'status' => 'published',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 weeks')),
            'views' => 19
        ]
    ];
}

function getAllBlogs($limit = null, $offset = 0, $status = null, $category = null, $author = null, $search = null) {
    try {
        $pdo = getDB();
        if ($pdo) {
            $sql = "SELECT * FROM blogs WHERE 1=1";
            $params = [];
            
            if ($status) {
                $sql .= " AND status = ?";
                $params[] = $status;
            }

            if ($category) {
                $sql .= " AND (LOWER(category) = LOWER(?) OR LOWER(REPLACE(category, ' ', '-')) = LOWER(?))";
                $params[] = $category;
                $params[] = $category;
            }

            if ($author) {
                $sql .= " AND (LOWER(author) = LOWER(?) OR LOWER(REPLACE(author, ' ', '-')) = LOWER(?))";
                $params[] = $author;
                $params[] = $author;
            }

            if ($search) {
                $sql .= " AND (title LIKE ? OR content LIKE ? OR excerpt LIKE ?)";
                $searchTerm = '%' . $search . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            if ($limit) {
                $limit = intval($limit);
                $offset = intval($offset);
                $sql .= " LIMIT $limit OFFSET $offset";
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll();
            if (!empty($results)) {
                return $results;
            }
        }
    } catch (Throwable $e) {
        // Silent catch
    }

    $defaults = getDefaultSampleBlogs();
    if ($status) {
        $defaults = array_values(array_filter($defaults, function($b) use ($status) {
            return $b['status'] === $status;
        }));
    }
    if ($category) {
        $catClean = strtolower(str_replace(' ', '-', $category));
        $defaults = array_values(array_filter($defaults, function($b) use ($catClean) {
            return strtolower(str_replace(' ', '-', $b['category'])) === $catClean;
        }));
    }
    if ($author) {
        $authClean = strtolower(str_replace(' ', '-', $author));
        $defaults = array_values(array_filter($defaults, function($b) use ($authClean) {
            return strtolower(str_replace(' ', '-', $b['author'] ?? 'Admin')) === $authClean;
        }));
    }
    if ($search) {
        $searchClean = strtolower($search);
        $defaults = array_values(array_filter($defaults, function($b) use ($searchClean) {
            return strpos(strtolower($b['title']), $searchClean) !== false ||
                   strpos(strtolower($b['content']), $searchClean) !== false ||
                   strpos(strtolower($b['excerpt'] ?? ''), $searchClean) !== false;
        }));
    }

    if ($limit) {
        return array_slice($defaults, $offset, $limit);
    }
    return $defaults;
}

function getBlogById($id) {
    try {
        $pdo = getDB();
        if ($pdo) {
            $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
            $stmt->execute([$id]);
            $blog = $stmt->fetch();
            if ($blog) return $blog;
        }
    } catch (Throwable $e) {
        // Silent catch
    }

    foreach (getDefaultSampleBlogs() as $b) {
        if ($b['id'] == $id) return $b;
    }
    return false;
}

function getBlogBySlug($slug) {
    if (empty($slug)) return false;
    try {
        $pdo = getDB();
        if ($pdo) {
            $stmt = $pdo->prepare("SELECT * FROM blogs WHERE (slug = ? OR id = ?) AND status = 'published'");
            $stmt->execute([$slug, $slug]);
            $blog = $stmt->fetch();
            if ($blog) return $blog;
        }
    } catch (Throwable $e) {
        // Silent catch
    }

    foreach (getDefaultSampleBlogs() as $b) {
        if ($b['slug'] === $slug || (string)$b['id'] === (string)$slug) return $b;
    }
    return false;
}

function createBlog($data) {
    try {
        $pdo = getDB();
        if (!$pdo) return false;
        
        $rawSlug = !empty($data['slug']) ? $data['slug'] : $data['title'];
        $slug = generateUniqueSlug($rawSlug);
        
        $sql = "INSERT INTO blogs (title, slug, content, excerpt, featured_image, category, author, meta_description, meta_keywords, status, sections, content_format) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $data['title'],
            $slug,
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

        if ($result) {
            generateSitemapXML();
        }
        return $result;
    } catch (Throwable $e) {
        return false;
    }
}

function updateBlog($id, $data) {
    try {
        $pdo = getDB();
        if (!$pdo) return false;
        
        $rawSlug = !empty($data['slug']) ? $data['slug'] : $data['title'];
        $slug = generateUniqueSlug($rawSlug, $id);
        
        $sql = "UPDATE blogs SET 
                title = ?, slug = ?, content = ?, excerpt = ?, 
                featured_image = ?, category = ?, author = ?, 
                meta_description = ?, meta_keywords = ?, status = ?,
                sections = ?, content_format = ?
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $data['title'],
            $slug,
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

        if ($result) {
            generateSitemapXML();
        }
        return $result;
    } catch (Throwable $e) {
        return false;
    }
}

function deleteBlog($id) {
    try {
        $pdo = getDB();
        if (!$pdo) return false;
        
        // Get blog to delete image
        $blog = getBlogById($id);
        if ($blog && !empty($blog['featured_image'])) {
            deleteImage($blog['featured_image']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
        $result = $stmt->execute([$id]);
        if ($result) {
            generateSitemapXML();
        }
        return $result;
    } catch (Throwable $e) {
        return false;
    }
}

function getBlogCount($status = null, $category = null, $author = null, $search = null) {
    try {
        $pdo = getDB();
        if ($pdo) {
            $sql = "SELECT COUNT(*) FROM blogs WHERE 1=1";
            $params = [];
            
            if ($status) {
                $sql .= " AND status = ?";
                $params[] = $status;
            }

            if ($category) {
                $sql .= " AND (LOWER(category) = LOWER(?) OR LOWER(REPLACE(category, ' ', '-')) = LOWER(?))";
                $params[] = $category;
                $params[] = $category;
            }

            if ($author) {
                $sql .= " AND (LOWER(author) = LOWER(?) OR LOWER(REPLACE(author, ' ', '-')) = LOWER(?))";
                $params[] = $author;
                $params[] = $author;
            }

            if ($search) {
                $sql .= " AND (title LIKE ? OR content LIKE ? OR excerpt LIKE ?)";
                $searchTerm = '%' . $search . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $count = $stmt->fetchColumn();
            if ($count > 0) return $count;
        }
    } catch (Throwable $e) {
        // Silent catch
    }

    $blogs = getAllBlogs(null, 0, $status, $category, $author, $search);
    return count($blogs);
}

function getRecentBlogs($limit = 5) {
    try {
        $pdo = getDB();
        if ($pdo) {
            $limit = intval($limit);
            $stmt = $pdo->query("SELECT * FROM blogs WHERE status = 'published' ORDER BY created_at DESC LIMIT $limit");
            $results = $stmt->fetchAll();
            if (!empty($results)) return $results;
        }
    } catch (Throwable $e) {
        // Silent catch
    }

    return array_slice(getDefaultSampleBlogs(), 0, $limit);
}

function getPopularBlogs($limit = 5) {
    try {
        $pdo = getDB();
        if ($pdo) {
            $limit = intval($limit);
            $stmt = $pdo->query("SELECT * FROM blogs WHERE status = 'published' ORDER BY views DESC LIMIT $limit");
            $results = $stmt->fetchAll();
            if (!empty($results)) return $results;
        }
    } catch (Throwable $e) {
        // Silent catch
    }

    return array_slice(getDefaultSampleBlogs(), 0, $limit);
}

// ==================== HELPER FUNCTIONS ====================

function createSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

/**
 * Generate unique slug, preventing duplicates (e.g. post, post-1, post-2)
 */
function generateUniqueSlug($text, $excludeId = null) {
    $baseSlug = createSlug($text);
    if (empty($baseSlug)) {
        $baseSlug = 'post-' . time();
    }
    
    $slug = $baseSlug;
    $counter = 1;
    
    try {
        $pdo = getDB();
        if ($pdo) {
            while (true) {
                if ($excludeId) {
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM blogs WHERE slug = ? AND id != ?");
                    $stmt->execute([$slug, $excludeId]);
                } else {
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM blogs WHERE slug = ?");
                    $stmt->execute([$slug]);
                }
                
                if ($stmt->fetchColumn() == 0) {
                    break;
                }
                
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            return $slug;
        }
    } catch (Throwable $e) {
        // Fallback
    }

    $defaults = getDefaultSampleBlogs();
    while (true) {
        $found = false;
        foreach ($defaults as $b) {
            if ($b['slug'] === $slug && (!$excludeId || $b['id'] != $excludeId)) {
                $found = true;
                break;
            }
        }
        if (!$found) break;
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }

    return $slug;
}

/**
 * Dynamically generate/update sitemap.xml
 */
function generateSitemapXML() {
    try {
        $siteDomain = 'https://dhauladharheightsresort.com';
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?xml-stylesheet type="text/css" href="https://www.xml-sitemaps.com/css/sitemap.css"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n\n";

        $dateNow = date('c');

        $staticPages = [
            '/' => ['priority' => '1.0000', 'freq' => 'daily'],
            '/about.php' => ['priority' => '0.8000', 'freq' => 'daily'],
            '/rooms' => ['priority' => '0.9000', 'freq' => 'daily'],
            '/rooms/executive-room' => ['priority' => '0.8500', 'freq' => 'daily'],
            '/rooms/executive-suite' => ['priority' => '0.8500', 'freq' => 'daily'],
            '/rooms/presidential-suite' => ['priority' => '0.8500', 'freq' => 'daily'],
            '/rooms/twin-bed' => ['priority' => '0.8500', 'freq' => 'daily'],
            '/rooms/deluxe-room' => ['priority' => '0.8500', 'freq' => 'daily'],
            '/blog' => ['priority' => '0.9000', 'freq' => 'daily'],
            '/destination-wedding.php' => ['priority' => '0.8000', 'freq' => 'daily'],
            '/events.html' => ['priority' => '0.8000', 'freq' => 'daily'],
            '/contact.html' => ['priority' => '0.8000', 'freq' => 'daily'],
            '/gallery.html' => ['priority' => '0.8000', 'freq' => 'daily'],
        ];

        foreach ($staticPages as $path => $meta) {
            $xml .= "  <url>\n";
            $xml .= "       <loc>" . $siteDomain . $path . "</loc>\n";
            $xml .= "       <lastmod>" . $dateNow . "</lastmod>\n";
            $xml .= "       <changefreq>" . $meta['freq'] . "</changefreq>\n";
            $xml .= "       <priority>" . $meta['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }

        $publishedBlogs = getAllBlogs(null, 0, 'published');
        foreach ($publishedBlogs as $b) {
            $catSlug = !empty($b['category']) ? createSlug($b['category']) : 'general';
            $blogSlug = !empty($b['slug']) ? $b['slug'] : createSlug($b['title']);
            $blogPath = '/blog/' . $catSlug . '/' . $blogSlug;

            $lastmod = !empty($b['updated_at']) ? date('c', strtotime($b['updated_at'])) : (!empty($b['created_at']) ? date('c', strtotime($b['created_at'])) : $dateNow);

            $xml .= "  <url>\n";
            $xml .= "       <loc>" . $siteDomain . $blogPath . "</loc>\n";
            $xml .= "       <lastmod>" . $lastmod . "</lastmod>\n";
            $xml .= "       <changefreq>daily</changefreq>\n";
            $xml .= "       <priority>0.8000</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        $projectRoot = dirname(__DIR__);
        $sitemapPath = $projectRoot . '/sitemap.xml';
        file_put_contents($sitemapPath, $xml);
        return true;
    } catch (Throwable $e) {
        error_log("Failed to generate sitemap XML: " . $e->getMessage());
        return false;
    }
}

/**
 * Generate SEO clean URL for a blog post
 * Output: /blog/category-slug/blog-slug or /blog/blog-slug
 */
function getBlogUrl($blog) {
    $baseUrl = getBaseUrl();
    if (is_array($blog)) {
        $slug = $blog['slug'] ?? createSlug($blog['title'] ?? '');
        $cat = !empty($blog['category']) ? createSlug($blog['category']) : 'general';
        return $baseUrl . '/blog/' . $cat . '/' . $slug;
    }
    return $baseUrl . '/blog/' . $blog;
}

/**
 * Generate SEO clean URL for a room page
 * Output: /rooms/room-slug
 */
function getRoomUrl($roomSlug, $catSlug = null) {
    $baseUrl = getBaseUrl();
    $roomSlug = ltrim($roomSlug, '/');
    if ($catSlug) {
        return $baseUrl . '/rooms/' . createSlug($catSlug) . '/' . $roomSlug;
    }
    return $baseUrl . '/rooms/' . $roomSlug;
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
        if ($pdo) {
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
            $results = $stmt->fetchAll();
            if (!empty($results)) return $results;
        }
    } catch (Throwable $e) {
        error_log("Error getting categories: " . $e->getMessage());
    }

    return [
        ['id' => 1, 'name' => 'Events', 'slug' => 'events'],
        ['id' => 2, 'name' => 'Food', 'slug' => 'food'],
        ['id' => 3, 'name' => 'Lifestyle', 'slug' => 'lifestyle'],
        ['id' => 4, 'name' => 'Travel', 'slug' => 'travel'],
        ['id' => 5, 'name' => 'Wedding', 'slug' => 'wedding']
    ];
}

function getDashboardStats() {
    $stats = ['total_blogs' => 0, 'published_blogs' => 0, 'draft_blogs' => 0];
    
    try {
        $pdo = getDB();
        if ($pdo) {
            // Total blogs
            $stmt = $pdo->query("SELECT COUNT(*) FROM blogs");
            $stats['total_blogs'] = $stmt->fetchColumn() ?: 0;
            
            // Published blogs
            $stmt = $pdo->query("SELECT COUNT(*) FROM blogs WHERE status = 'published'");
            $stats['published_blogs'] = $stmt->fetchColumn() ?: 0;
            
            // Draft blogs
            $stmt = $pdo->query("SELECT COUNT(*) FROM blogs WHERE status = 'draft'");
            $stats['draft_blogs'] = $stmt->fetchColumn() ?: 0;
        }
    } catch (Throwable $e) {
        // Fallback
    }
    
    return $stats;
}

// ==================== ADMIN AUTH FUNCTIONS ====================

function authenticateAdmin($username, $password) {
    try {
        $pdo = getDB();
        if (!$pdo) return false;
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
    } catch (Throwable $e) {
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
    try {
        $pdo = getDB();
        if (!$pdo) return false;
        $stmt = $pdo->prepare("SELECT id, username, email, name FROM admins WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (Throwable $e) {
        return false;
    }
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
function seedDefaultBlogs($pdo) {
    try {
        $defaultPosts = [
            [
                'title' => 'Dream Destination Weddings in Dharamshala at Dhauladhar Heights',
                'slug' => 'destination-wedding-in-dharamshala',
                'category' => 'Wedding',
                'excerpt' => 'Exchange your vows surrounded by the majestic Dhauladhar mountains. Discover how our luxury resort makes your dream hill-station wedding unforgettable.',
                'content' => '<p>Planning a destination wedding in the serene hills of Himachal Pradesh? Hotel Dhauladhar Heights Resort offers breathtaking mountain vistas, exquisite banquet halls, open lawn venues, and five-star hospitality for your special day.</p><p>From royal outdoor mandaps overlooking tea gardens to customized catering and lavish guest suites, we turn every wedding celebration into a lifelong memory.</p>',
                'featured_image' => 'images/sangeet1.jpg'
            ],
            [
                'title' => 'Exploring Dharamshala: Top Attractions and Hill Station Experiences',
                'slug' => 'exploring-dharamshala-attractions',
                'category' => 'Travel',
                'excerpt' => 'From Kangra valley tea gardens to McLeod Ganj monasteries, discover the top places to visit during your stay at Dhauladhar Heights Resort.',
                'content' => '<p>Dharamshala is a sanctuary of peace, natural beauty, and vibrant culture. Located at the foothills of the Dhauladhar ranges, it offers everything from tranquil forest walks to historic temples and Tibetan heritage.</p><p>Key highlights include McLeod Ganj, Bhagsu Waterfall, Kunal Pathri Temple, and panoramic tea garden trails right outside our resort doors.</p>',
                'featured_image' => 'images/aboutbanner - Copy.jpg'
            ],
            [
                'title' => 'Luxury Accommodation Guide: Executive Suites & Presidential Living',
                'slug' => 'luxury-accommodation-guide-dharamshala',
                'category' => 'Lifestyle',
                'excerpt' => 'Experience ultimate luxury, mountain views, and modern comforts in our signature suites and executive rooms at Dhauladhar Heights.',
                'content' => '<p>Whether traveling for business or leisure, choosing the right room enhances your mountain getaway. Our resort features Executive Rooms, Executive Suites with separate living spaces, and exclusive Presidential Suites equipped with top-tier amenities, smart TVs, high-speed Wi-Fi, and private balconies facing the snow-capped peaks.</p>',
                'featured_image' => 'images/presidentialmain.jpg'
            ],
            [
                'title' => 'Culinary Journey: Authentic Himachali & International Flavors',
                'slug' => 'authentic-himachali-food-guide',
                'category' => 'Food',
                'excerpt' => 'Indulge in delicious multi-cuisine dining, traditional Himachali specialties, and soothing teas served at our hill-view restaurant.',
                'content' => '<p>At Dhauladhar Heights Resort, dining is an extraordinary experience. Enjoy freshly prepared North Indian, Continental, and local Himachali delicacies crafted by our executive chefs using organic regional ingredients.</p>',
                'featured_image' => 'images/DSC09149-scaled.jpg'
            ],
            [
                'title' => 'Corporate Retreats & Event Venues in the Himalayas',
                'slug' => 'corporate-retreats-event-venues-dharamshala',
                'category' => 'Events',
                'excerpt' => 'Host high-impact corporate conferences, annual meets, and private celebrations in our versatile event venues with state-of-the-art facilities.',
                'content' => '<p>Combine business with nature\'s tranquility. Our resort provides spacious conference halls, high-speed connectivity, break-out zones, and curated team-building activities set against the backdrop of Dharamshala\'s mountain wilderness.</p>',
                'featured_image' => 'images/DSC09631-scaled.jpg'
            ]
        ];

        $stmt = $pdo->prepare("INSERT IGNORE INTO blogs (title, slug, content, excerpt, featured_image, category, author, status, views) VALUES (?, ?, ?, ?, ?, ?, 'Admin', 'published', 15)");
        foreach ($defaultPosts as $post) {
            $stmt->execute([
                $post['title'],
                $post['slug'],
                $post['content'],
                $post['excerpt'],
                $post['featured_image'],
                $post['category']
            ]);
        }
    } catch (PDOException $e) {
        error_log("Error seeding blogs: " . $e->getMessage());
    }
}

function ensureBlogTableColumns() {
    try {
        $pdo = getDB();
        if (!$pdo) return;
        $stmt = $pdo->query("SHOW TABLES LIKE 'blogs'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("CREATE TABLE IF NOT EXISTS blogs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL UNIQUE,
                content LONGTEXT NOT NULL,
                excerpt TEXT,
                featured_image VARCHAR(255) DEFAULT '',
                category VARCHAR(100) DEFAULT 'General',
                author VARCHAR(100) DEFAULT 'Admin',
                meta_description TEXT,
                meta_keywords TEXT,
                status VARCHAR(20) DEFAULT 'published',
                views INT DEFAULT 0,
                sections JSON DEFAULT NULL,
                content_format VARCHAR(20) DEFAULT 'html',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            
            seedDefaultBlogs($pdo);
        } else {
            // Check if table has columns
            $stmtCol = $pdo->query("SHOW COLUMNS FROM blogs LIKE 'views'");
            if ($stmtCol->rowCount() == 0) {
                $pdo->exec("ALTER TABLE blogs ADD COLUMN views INT DEFAULT 0 AFTER status");
            }
            $stmtCol = $pdo->query("SHOW COLUMNS FROM blogs LIKE 'sections'");
            if ($stmtCol->rowCount() == 0) {
                $pdo->exec("ALTER TABLE blogs ADD COLUMN sections JSON DEFAULT NULL AFTER views");
            }
            $stmtCol = $pdo->query("SHOW COLUMNS FROM blogs LIKE 'content_format'");
            if ($stmtCol->rowCount() == 0) {
                $pdo->exec("ALTER TABLE blogs ADD COLUMN content_format VARCHAR(20) DEFAULT 'html' AFTER sections");
            }
            
            // Check if 0 rows in blogs table
            $countStmt = $pdo->query("SELECT COUNT(*) FROM blogs");
            if ($countStmt->fetchColumn() == 0) {
                seedDefaultBlogs($pdo);
            }
        }
    } catch (Throwable $e) {
        // Silent catch for DB connection error
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
        if (!$pdo) return;
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
    } catch (Throwable $e) {
        // Silent catch
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
        if ($pdo) {
            $limit = intval($limit);
            $stmt = $pdo->query("SELECT * FROM google_reviews WHERE status = 1 ORDER BY display_order ASC, review_date DESC LIMIT $limit");
            return $stmt->fetchAll();
        }
    } catch (Throwable $e) {
        // Fallback
    }
    return [];
}

function getAllReviews() {
    try {
        ensureReviewsTable();
        $pdo = getDB();
        if ($pdo) {
            $stmt = $pdo->query("SELECT * FROM google_reviews WHERE status = 1 ORDER BY display_order ASC, review_date DESC");
            return $stmt->fetchAll();
        }
    } catch (Throwable $e) {
        // Fallback
    }
    return [];
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
        if ($pdo) {
            $stmt = $pdo->query("SELECT COUNT(*) as total, AVG(rating) as avg_rating FROM google_reviews WHERE status = 1");
            return $stmt->fetch();
        }
    } catch (Throwable $e) {
        // Fallback
    }
    return ['total' => 0, 'avg_rating' => 0];
}

function getAllAdminReviews() {
    try {
        ensureReviewsTable();
        $pdo = getDB();
        if ($pdo) {
            $stmt = $pdo->query("SELECT * FROM google_reviews ORDER BY created_at DESC");
            return $stmt->fetchAll();
        }
    } catch (Throwable $e) {
        // Fallback
    }
    return [];
}

function getReviewById($id) {
    try {
        $pdo = getDB();
        if ($pdo) {
            $stmt = $pdo->prepare("SELECT * FROM google_reviews WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        }
    } catch (Throwable $e) {
        // Fallback
    }
    return false;
}

function createReview($data) {
    try {
        $pdo = getDB();
        if (!$pdo) return false;
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
    } catch (Throwable $e) {
        return false;
    }
}

function updateReview($id, $data) {
    try {
        $pdo = getDB();
        if (!$pdo) return false;
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
    } catch (Throwable $e) {
        return false;
    }
}

function deleteReview($id) {
    try {
        $pdo = getDB();
        if (!$pdo) return false;
        
        $review = getReviewById($id);
        if ($review && !empty($review['reviewer_image'])) {
            deleteImage($review['reviewer_image']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM google_reviews WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (Throwable $e) {
        return false;
    }
}
?>
