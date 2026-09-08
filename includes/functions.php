<?php
// Include database connection FIRST
require_once __DIR__ . '/db.php';

// Start session if not started with persistent session settings across all paths
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @ini_set('session.cookie_lifetime', 2592000); // 30 days
    @ini_set('session.gc_maxlifetime', 2592000); // 30 days
    @ini_set('session.cookie_path', '/');
    if (PHP_VERSION_ID >= 70300) {
        @session_set_cookie_params([
            'lifetime' => 2592000,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    } else {
        @session_set_cookie_params(2592000, '/', '', false, true);
    }
    @session_start();
}

// ==================== AUTHENTICATION FUNCTIONS ====================

function requireLogin() {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        @session_start();
    }
    if (!isLoggedIn()) {
        $basePath = getBaseUrl();
        session_write_close();
        header('Location: ' . $basePath . '/admin/index.php');
        exit();
    }
}

function isLoggedIn() {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        @session_start();
    }
    
    if (!empty($_SESSION['admin_id']) || !empty($_SESSION['admin_logged_in'])) {
        return true;
    }
    
    // Fallback: Check persistent auth cookie if session was cleared or lost
    if (!empty($_COOKIE['admin_auth_token'])) {
        $expectedToken = md5('dhauladhar_admin_secure_salt_2026');
        if ($_COOKIE['admin_auth_token'] === $expectedToken) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = !empty($_COOKIE['admin_auth_id']) ? intval($_COOKIE['admin_auth_id']) : 1;
            $_SESSION['admin_name'] = !empty($_COOKIE['admin_auth_name']) ? $_COOKIE['admin_auth_name'] : 'Admin';
            $_SESSION['admin_email'] = !empty($_COOKIE['admin_auth_email']) ? $_COOKIE['admin_auth_email'] : 'admin@dhauladharheightsresort.com';
            return true;
        }
    }
    
    return false;
}

function logout() {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        @session_start();
    }
    $_SESSION = [];
    
    $cookiePath = function_exists('getBaseUrl') ? getBaseUrl() . '/' : '/';
    if (empty($cookiePath)) $cookiePath = '/';
    
    setcookie('admin_auth_token', '', time() - 3600, $cookiePath, '', false, true);
    setcookie('admin_auth_id', '', time() - 3600, $cookiePath, '', false, true);
    setcookie('admin_auth_name', '', time() - 3600, $cookiePath, '', false, true);
    setcookie('admin_auth_email', '', time() - 3600, $cookiePath, '', false, true);
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    @session_destroy();
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
        ],
        [
            'id' => 6,
            'title' => 'World Environment Day 2026: Dhauladhar Heights Resort Leads a Cleanliness Drive for a Greener Dharamshala',
            'slug' => 'world-environment-day-2026-dhauladhar-heights-resort-leads-a-cleanliness-drive-for-a-greener-dharamshala',
            'category' => 'Events',
            'excerpt' => 'Celebrating World Environment Day 2026 with an inspiring eco-drive across Dharamshala and tea garden trails surrounding Dhauladhar Heights Resort.',
            'content' => '<p>In celebration of World Environment Day 2026, Hotel Dhauladhar Heights Resort organized a major eco-cleanliness drive and tree plantation campaign across Kangra valley tea gardens and local trail routes in Dharamshala.</p><p>Guests, resort team members, and local volunteers gathered to spread awareness on eco-tourism, plastic waste reduction, and preserving the serene Himalayan ecosystem for future generations.</p>',
            'featured_image' => 'images/DSC09631-scaled.jpg',
            'author' => 'Admin',
            'status' => 'published',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'views' => 84
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

    // Fallback to sample blogs when DB is empty, disconnected, or unreachable
    $sampleBlogs = getDefaultSampleBlogs();
    $filtered = [];
    foreach ($sampleBlogs as $b) {
        if ($status && isset($b['status']) && $b['status'] !== $status) {
            continue;
        }
        if ($category) {
            $bCat = strtolower(str_replace(' ', '-', $b['category'] ?? ''));
            $cFilter = strtolower(str_replace(' ', '-', $category));
            if ($bCat !== $cFilter) continue;
        }
        if ($author && isset($b['author'])) {
            $bAuth = strtolower(str_replace(' ', '-', $b['author']));
            $aFilter = strtolower(str_replace(' ', '-', $author));
            if ($bAuth !== $aFilter) continue;
        }
        if ($search) {
            $sLower = strtolower($search);
            $match = (stripos($b['title'] ?? '', $sLower) !== false) ||
                     (stripos($b['content'] ?? '', $sLower) !== false) ||
                     (stripos($b['excerpt'] ?? '', $sLower) !== false);
            if (!$match) continue;
        }
        $filtered[] = $b;
    }

    if ($offset > 0 || $limit !== null) {
        $offset = max(0, intval($offset));
        $limit = $limit !== null ? intval($limit) : null;
        return array_slice($filtered, $offset, $limit);
    }

    return $filtered;
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

    // Fallback to sample blogs
    $sampleBlogs = getDefaultSampleBlogs();
    foreach ($sampleBlogs as $b) {
        if ((string)$b['id'] === (string)$id) {
            return $b;
        }
    }

    return false;
}

function getBlogBySlug($slug) {
    if (empty($slug)) return false;
    $cleanSlug = createSlug($slug);
    
    try {
        $pdo = getDB();
        if ($pdo) {
            $stmt = $pdo->prepare("SELECT * FROM blogs WHERE (slug = ? OR slug = ? OR id = ? OR LOWER(slug) = LOWER(?)) AND status = 'published'");
            $stmt->execute([$slug, $cleanSlug, $slug, $slug]);
            $blog = $stmt->fetch();
            if ($blog) return $blog;
        }
    } catch (Throwable $e) {
        // Silent catch
    }

    // Fallback to sample blogs
    $sampleBlogs = getDefaultSampleBlogs();
    foreach ($sampleBlogs as $b) {
        if ($b['slug'] === $slug || $b['slug'] === $cleanSlug || (string)$b['id'] === (string)$slug) {
            return $b;
        }
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
            $newId = $pdo->lastInsertId();
            $newBlog = getBlogById($newId);
            if ($newBlog) {
                syncBlogPhysicalPages($newBlog);
            }
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
        
        $oldBlog = getBlogById($id);
        $oldSlug = $oldBlog ? ($oldBlog['slug'] ?? '') : '';
        $oldCategory = $oldBlog ? ($oldBlog['category'] ?? '') : '';

        $rawSlug = !empty($data['slug']) ? $data['slug'] : (!empty($oldSlug) ? $oldSlug : $data['title']);
        $slug = generateUniqueSlug($rawSlug, $id);
        
        // Preserve featured image if empty in $data and not explicitly removed
        $featured_image = !empty($data['featured_image']) ? $data['featured_image'] : ($oldBlog['featured_image'] ?? '');
        $status = !empty($data['status']) ? $data['status'] : ($oldBlog['status'] ?? 'published');

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
            $featured_image,
            $data['category'] ?? '',
            $data['author'] ?? 'Admin',
            $data['meta_description'] ?? '',
            $data['meta_keywords'] ?? '',
            $status,
            $data['sections'] ?? null,
            $data['content_format'] ?? 'html',
            $id
        ]);

        if ($result) {
            $updatedBlog = getBlogById($id);
            if ($updatedBlog) {
                // If slug changed, keep old directory redirect so old links don't break
                if (!empty($oldSlug) && $oldSlug !== $slug) {
                    preserveOldSlugRedirect($oldSlug, $slug, $oldCategory, $data['category'] ?? $oldCategory);
                }
                syncBlogPhysicalPages($updatedBlog);
            }
            generateSitemapXML();
        }
        return $result;
    } catch (Throwable $e) {
        return false;
    }
}

function preserveOldSlugRedirect($oldSlug, $newSlug, $oldCategory = '', $newCategory = '') {
    if (empty($oldSlug) || empty($newSlug) || $oldSlug === $newSlug) return;
    $projectRoot = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');
    $oldCatSlug = !empty($oldCategory) ? createSlug($oldCategory) : 'general';
    $newCatSlug = !empty($newCategory) ? createSlug($newCategory) : $oldCatSlug;
    
    $dirs = [
        $projectRoot . '/blog/' . $oldSlug,
        $projectRoot . '/blog/' . $oldCatSlug . '/' . $oldSlug
    ];
    
    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            @mkdir($dir, 0777, true);
        }
        $relativeProjectSubpath = str_replace($projectRoot, '', str_replace('\\', '/', $dir));
        $depth = substr_count(trim($relativeProjectSubpath, '/'), '/');
        $relPath = str_repeat('../', max(1, $depth + 1)) . 'blog/' . $newCatSlug . '/' . $newSlug;
        
        $redirectCode = "<?php\n";
        $redirectCode .= "header('Location: " . $relPath . "', true, 301);\n";
        $redirectCode .= "exit();\n";
        @file_put_contents($dir . '/index.php', $redirectCode);
    }
}

function deleteBlog($id) {
    try {
        $pdo = getDB();
        if (!$pdo) return false;
        
        // Get blog to delete image & physical pages
        $blog = getBlogById($id);
        if ($blog) {
            if (!empty($blog['featured_image'])) {
                deleteImage($blog['featured_image']);
            }
            removeBlogPhysicalPages($blog);
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
            if ($count !== false && (int)$count > 0) {
                return (int)$count;
            }
        }
    } catch (Throwable $e) {
        // Silent catch
    }

    $all = getAllBlogs(null, 0, $status, $category, $author, $search);
    return count($all);
}

function getRecentBlogs($limit = 5) {
    try {
        $pdo = getDB();
        if ($pdo) {
            $limit = intval($limit);
            $stmt = $pdo->query("SELECT * FROM blogs WHERE status = 'published' ORDER BY created_at DESC LIMIT $limit");
            $results = $stmt->fetchAll();
            if (!empty($results)) {
                return $results;
            }
        }
    } catch (Throwable $e) {
        // Silent catch
    }

    return getAllBlogs($limit, 0, 'published');
}

function getPopularBlogs($limit = 5) {
    try {
        $pdo = getDB();
        if ($pdo) {
            $limit = intval($limit);
            $stmt = $pdo->query("SELECT * FROM blogs WHERE status = 'published' ORDER BY views DESC LIMIT $limit");
            $results = $stmt->fetchAll();
            if (!empty($results)) {
                return $results;
            }
        }
    } catch (Throwable $e) {
        // Silent catch
    }

    return getAllBlogs($limit, 0, 'published');
}

function createSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

/**
 * Helper to recursively delete a directory and all its contents
 */
function deleteDirectoryRecursive($dir) {
    if (!file_exists($dir)) return true;
    if (!is_dir($dir)) return @unlink($dir);
    $items = scandir($dir);
    if ($items === false) return false;
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        if (!deleteDirectoryRecursive($dir . '/' . $item)) {
            return false;
        }
    }
    return @rmdir($dir);
}

/**
 * Generate physical PHP pages for a blog post to support direct file loading
 * Creates blog/{category_slug}/{blog_slug}/index.php AND blog/{blog_slug}/index.php
 */
function syncBlogPhysicalPages($blog) {
    if (empty($blog) || empty($blog['slug'])) return false;
    
    // If status is draft or not published, remove physical pages
    if (isset($blog['status']) && $blog['status'] !== 'published') {
        removeBlogPhysicalPages($blog);
        return true;
    }
    
    $projectRoot = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');
    $slug = createSlug($blog['slug']);
    $catSlug = !empty($blog['category']) ? createSlug($blog['category']) : 'general';
    
    // Ensure category index landing page exists
    $catDir = $projectRoot . '/blog/' . $catSlug;
    if (!file_exists($catDir)) {
        @mkdir($catDir, 0777, true);
    }
    $catIndexFile = $catDir . '/index.php';
    if (!file_exists($catIndexFile)) {
        $catFileContent = "<?php\n";
        $catFileContent .= "\$_GET['category'] = " . var_export($catSlug, true) . ";\n";
        $catFileContent .= "require_once __DIR__ . '/../blog.php';\n";
        @file_put_contents($catIndexFile, $catFileContent);
    }
    
    $dirs = [
        $projectRoot . '/blog/' . $slug,
        $projectRoot . '/blog/' . $catSlug . '/' . $slug
    ];
    
    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            @mkdir($dir, 0777, true);
        }
        
        $relativeProjectSubpath = str_replace($projectRoot, '', str_replace('\\', '/', $dir));
        $depth = substr_count(trim($relativeProjectSubpath, '/'), '/');
        $relPath = str_repeat('../', max(1, $depth + 1)) . 'blog-detail.php';
        
        $fileContent = "<?php\n";
        $fileContent .= "\$_GET['category'] = " . var_export($catSlug, true) . ";\n";
        $fileContent .= "\$_GET['slug'] = " . var_export($slug, true) . ";\n";
        $fileContent .= "require_once __DIR__ . '/" . $relPath . "';\n";
        
        $filePath = $dir . '/index.php';
        @file_put_contents($filePath, $fileContent);
    }
    return true;
}

/**
 * Remove physical PHP pages and directories for a blog post
 */
function removeBlogPhysicalPages($blog) {
    if (empty($blog) || empty($blog['slug'])) return;
    $projectRoot = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');
    $slug = createSlug($blog['slug']);
    $catSlug = !empty($blog['category']) ? createSlug($blog['category']) : 'general';
    
    $dirs = [
        $projectRoot . '/blog/' . $catSlug . '/' . $slug,
        $projectRoot . '/blog/' . $slug
    ];
    
    foreach ($dirs as $dir) {
        deleteDirectoryRecursive($dir);
        // If parent category folder is now empty, remove it too
        $parentDir = dirname($dir);
        if (basename($parentDir) !== 'blog' && file_exists($parentDir) && is_dir($parentDir)) {
            $files = array_diff(scandir($parentDir), ['.', '..']);
            if (empty($files)) {
                @rmdir($parentDir);
            }
        }
    }
}

/**
 * Sync physical PHP pages for all published blogs
 */
function syncAllBlogPhysicalPages() {
    $blogs = getAllBlogs(null, 0, 'published');
    foreach ($blogs as $b) {
        syncBlogPhysicalPages($b);
    }
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
 * Generate URL for a room page using room-details.html
 * Output: /room-details.html?room=room-slug
 */
function getRoomUrl($roomSlug, $catSlug = null) {
    $baseUrl = getBaseUrl();
    $roomSlug = ltrim($roomSlug, '/');
    return $baseUrl . '/room-details.html?room=' . urlencode($roomSlug);
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
        if ($pdo) {
            $stmt = $pdo->prepare("UPDATE blogs SET views = views + 1 WHERE id = ?");
            $stmt->execute([$blogId]);
        }
    } catch (Throwable $e) {
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
            ],
            [
                'title' => 'World Environment Day 2026: Dhauladhar Heights Resort Leads a Cleanliness Drive for a Greener Dharamshala',
                'slug' => 'world-environment-day-2026-dhauladhar-heights-resort-leads-a-cleanliness-drive-for-a-greener-dharamshala',
                'category' => 'Events',
                'excerpt' => 'Celebrating World Environment Day 2026 with an inspiring eco-drive across Dharamshala and tea garden trails surrounding Dhauladhar Heights Resort.',
                'content' => '<p>In celebration of World Environment Day 2026, Hotel Dhauladhar Heights Resort organized a major eco-cleanliness drive and tree plantation campaign across Kangra valley tea gardens and local trail routes in Dharamshala.</p><p>Guests, resort team members, and local volunteers gathered to spread awareness on eco-tourism, plastic waste reduction, and preserving the serene Himalayan ecosystem for future generations.</p>',
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

            // If table has 0 blogs, seed default blogs
            $countStmt = $pdo->query("SELECT COUNT(*) FROM blogs");
            if ($countStmt && (int)$countStmt->fetchColumn() === 0) {
                seedDefaultBlogs($pdo);
            }
        }
        
        // Sync physical PHP directories for all blogs
        syncAllBlogPhysicalPages();
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

/**
 * =========================================================================
 * ADVANCED BLOG FORMATTING & SECTION RENDERING ENGINE
 * =========================================================================
 */

/**
 * Automatically format blog text with generous spacing, styled paragraphs,
 * headings, bullet lists, bold/italic, and quotes.
 */
function autoFormatBlogContent($content) {
    if (empty($content)) return '';
    
    // If content already has complex block-level HTML tags, return cleanly after sanitizing
    $hasBlockTags = preg_match('/<(?:p|div|section|article|table|ul|ol|h[1-6]|blockquote)\b/i', $content);
    
    // Normalize line endings
    $text = str_replace(["\r\n", "\r"], "\n", trim($content));
    
    // If it already has HTML block tags, just ensure paragraphs and images have proper spacing classes
    if ($hasBlockTags) {
        // Add .blog-paragraph class to <p> tags if missing
        $text = preg_replace_callback('/<p(?:\s+class="([^"]*)")?([^>]*)>/i', function($m) {
            $class = isset($m[1]) ? trim($m[1]) : '';
            $rest = isset($m[2]) ? $m[2] : '';
            if (strpos($class, 'blog-paragraph') === false) {
                $class = trim($class . ' blog-paragraph');
            }
            return '<p class="' . htmlspecialchars($class) . '"' . $rest . '>';
        }, $text);
        return $text;
    }
    
    // Split into lines to parse paragraphs, headings, blockquotes, and lists
    $rawLines = preg_split('/\r\n|\r|\n/', $text);
    $htmlParts = [];
    $currentType = null; // 'p', 'ul', 'quote'
    $currentBuffer = [];

    $flush = function() use (&$currentType, &$currentBuffer, &$htmlParts) {
        if ($currentType === null || empty($currentBuffer)) {
            $currentType = null;
            $currentBuffer = [];
            return;
        }

        if ($currentType === 'p') {
            $pText = implode("\n", $currentBuffer);
            $escaped = htmlspecialchars($pText);
            $escaped = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $escaped);
            $escaped = preg_replace('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/s', '<em>$1</em>', $escaped);
            $escaped = preg_replace('/\[([^\]]+)\]\((https?:\/\/[^\s\)]+)\)/', '<a href="$2" target="_blank" rel="noopener" class="blog-inline-link">$1</a>', $escaped);
            $escaped = nl2br($escaped);
            $htmlParts[] = '<p class="blog-paragraph">' . $escaped . '</p>';
        } elseif ($currentType === 'ul') {
            $listHtml = '<ul class="blog-styled-list">';
            foreach ($currentBuffer as $item) {
                $formattedItem = htmlspecialchars(trim($item));
                $formattedItem = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $formattedItem);
                $formattedItem = preg_replace('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/s', '<em>$1</em>', $formattedItem);
                $formattedItem = preg_replace('/\[([^\]]+)\]\((https?:\/\/[^\s\)]+)\)/', '<a href="$2" target="_blank" rel="noopener" class="blog-inline-link">$1</a>', $formattedItem);
                $listHtml .= '<li><i class="fa-solid fa-circle-check"></i> <span>' . $formattedItem . '</span></li>';
            }
            $listHtml .= '</ul>';
            $htmlParts[] = $listHtml;
        } elseif ($currentType === 'quote') {
            $quoteText = htmlspecialchars(implode(' ', $currentBuffer));
            $quoteText = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $quoteText);
            $quoteText = preg_replace('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/s', '<em>$1</em>', $quoteText);
            $htmlParts[] = '<div class="blog-section-quote">
                <div class="quote-icon"><i class="fa-solid fa-quote-left"></i></div>
                <blockquote>' . $quoteText . '</blockquote>
            </div>';
        }

        $currentType = null;
        $currentBuffer = [];
    };

    foreach ($rawLines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '') {
            $flush();
            continue;
        }

        // Heading: ## Heading 2 or ### Heading 3
        if (preg_match('/^(#{2,3})\s+(.+)$/', $trimmed, $hMatch)) {
            $flush();
            $level = strlen($hMatch[1]) === 3 ? 'h3' : 'h2';
            $headingClass = $level === 'h3' ? 'blog-subheading' : 'blog-section-heading';
            $titleText = htmlspecialchars(trim($hMatch[2]));
            $htmlParts[] = "<{$level} class=\"{$headingClass}\">{$titleText}</{$level}>";
            continue;
        }

        // Blockquote: > Quote text
        if (preg_match('/^>\s*(.+)$/', $trimmed, $qMatch)) {
            if ($currentType !== 'quote') {
                $flush();
                $currentType = 'quote';
            }
            $currentBuffer[] = trim($qMatch[1]);
            continue;
        }

        // Bullet list item: *, -, or •
        if (preg_match('/^[\*\-•]\s+(.+)$/u', $trimmed, $lMatch)) {
            if ($currentType !== 'ul') {
                $flush();
                $currentType = 'ul';
            }
            $currentBuffer[] = trim($lMatch[1]);
            continue;
        }

        // Regular paragraph text
        if ($currentType !== 'p') {
            $flush();
            $currentType = 'p';
        }
        $currentBuffer[] = $trimmed;
    }

    $flush();

    return implode("\n\n", $htmlParts);
}

/**
 * Render multi-type advanced blog sections into semantic HTML.
 * Handles text, image, gallery_2col, gallery_grid, quote, features, callout, faq, video, cta.
 */
function renderAdvancedBlogSections($sections, $fallbackContent = '') {
    $baseUrl = getBaseUrl();
    
    // Normalize sections if string
    if (is_string($sections) && !empty($sections)) {
        $decoded = json_decode($sections, true);
        if (is_array($decoded)) {
            $sections = $decoded;
        }
    }
    
    // Fallback if no structured sections
    if (empty($sections) || !is_array($sections)) {
        return '<div class="blog-section blog-section-text">' . autoFormatBlogContent($fallbackContent) . '</div>';
    }
    
    $output = '';
    $sectionCount = count($sections);
    
    foreach ($sections as $index => $sec) {
        if (!is_array($sec)) continue;
        
        $type = $sec['type'] ?? 'text';
        $output .= '<div class="blog-section blog-section-' . htmlspecialchars($type) . '">';
        
        switch ($type) {
            case 'text':
                $heading = trim($sec['heading'] ?? ($sec['title'] ?? ''));
                $level = (!empty($sec['level']) && in_array($sec['level'], ['h2', 'h3'])) ? $sec['level'] : 'h2';
                $body = $sec['content'] ?? ($sec['body'] ?? '');
                
                if (!empty($heading)) {
                    $headingClass = $level === 'h3' ? 'blog-subheading' : 'section-title';
                    $output .= "<{$level} class=\"{$headingClass}\">" . htmlspecialchars($heading) . "</{$level}>";
                }
                
                $output .= '<div class="section-content-inner">' . autoFormatBlogContent($body) . '</div>';
                
                // If section has attached legacy images
                if (!empty($sec['images']) && is_array($sec['images'])) {
                    $output .= '<div class="section-gallery">';
                    foreach ($sec['images'] as $img) {
                        if (empty($img)) continue;
                        $imgUrl = getBlogImageUrl($img);
                        $output .= '<div class="gallery-item" onclick="openLightbox(\'' . htmlspecialchars($imgUrl) . '\')">';
                        $output .= '<img src="' . htmlspecialchars($imgUrl) . '" alt="' . htmlspecialchars($heading ?: 'Resort View') . '" loading="lazy" onerror="this.onerror=null; this.src=\'' . $baseUrl . '/images/default-blog.jpg\';">';
                        $output .= '<div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i></div>';
                        $output .= '</div>';
                    }
                    $output .= '</div>';
                }
                break;
                
            case 'image':
                $img = $sec['image_url'] ?? ($sec['image'] ?? '');
                $caption = trim($sec['caption'] ?? '');
                $layout = in_array($sec['layout'] ?? '', ['full', 'contained', 'centered']) ? $sec['layout'] : 'full';
                
                if (!empty($img)) {
                    $imgUrl = getBlogImageUrl($img);
                    $output .= '<div class="blog-section-image-card layout-' . htmlspecialchars($layout) . '">';
                    $output .= '<div class="img-zoom-wrap" onclick="openLightbox(\'' . htmlspecialchars($imgUrl) . '\')">';
                    $output .= '<img src="' . htmlspecialchars($imgUrl) . '" alt="' . htmlspecialchars($caption ?: 'Resort Dhauladhar') . '" loading="lazy" onerror="this.onerror=null; this.src=\'' . $baseUrl . '/images/default-blog.jpg\';">';
                    $output .= '<div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i></div>';
                    $output .= '</div>';
                    if (!empty($caption)) {
                        $output .= '<div class="blog-img-caption"><i class="fa-solid fa-camera"></i> ' . htmlspecialchars($caption) . '</div>';
                    }
                    $output .= '</div>';
                }
                break;
                
            case 'gallery_2col':
                $img1 = $sec['image_url'] ?? ($sec['image_1'] ?? '');
                $cap1 = trim($sec['caption'] ?? ($sec['caption_1'] ?? ''));
                $img2 = $sec['image_url_2'] ?? ($sec['image_2'] ?? '');
                $cap2 = trim($sec['caption_2'] ?? '');
                
                $output .= '<div class="blog-section-gallery-2col">';
                if (!empty($img1)) {
                    $imgUrl1 = getBlogImageUrl($img1);
                    $output .= '<div class="gallery-col-item">';
                    $output .= '<div class="img-zoom-wrap" onclick="openLightbox(\'' . htmlspecialchars($imgUrl1) . '\')">';
                    $output .= '<img src="' . htmlspecialchars($imgUrl1) . '" alt="' . htmlspecialchars($cap1 ?: 'Resort View') . '" loading="lazy" onerror="this.onerror=null; this.src=\'' . $baseUrl . '/images/default-blog.jpg\';">';
                    $output .= '<div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i></div>';
                    $output .= '</div>';
                    if (!empty($cap1)) {
                        $output .= '<div class="blog-img-caption">' . htmlspecialchars($cap1) . '</div>';
                    }
                    $output .= '</div>';
                }
                if (!empty($img2)) {
                    $imgUrl2 = getBlogImageUrl($img2);
                    $output .= '<div class="gallery-col-item">';
                    $output .= '<div class="img-zoom-wrap" onclick="openLightbox(\'' . htmlspecialchars($imgUrl2) . '\')">';
                    $output .= '<img src="' . htmlspecialchars($imgUrl2) . '" alt="' . htmlspecialchars($cap2 ?: 'Resort View') . '" loading="lazy" onerror="this.onerror=null; this.src=\'' . $baseUrl . '/images/default-blog.jpg\';">';
                    $output .= '<div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i></div>';
                    $output .= '</div>';
                    if (!empty($cap2)) {
                        $output .= '<div class="blog-img-caption">' . htmlspecialchars($cap2) . '</div>';
                    }
                    $output .= '</div>';
                }
                $output .= '</div>';
                break;
                
            case 'quote':
                $quote = trim($sec['quote'] ?? ($sec['content'] ?? ''));
                $author = trim($sec['author'] ?? 'Hotel Dhauladhar Heights Team');
                
                $output .= '<div class="blog-section-quote">';
                $output .= '<div class="quote-icon"><i class="fa-solid fa-quote-left"></i></div>';
                $output .= '<blockquote>' . htmlspecialchars($quote) . '</blockquote>';
                if (!empty($author)) {
                    $output .= '<div class="quote-author">— ' . htmlspecialchars($author) . '</div>';
                }
                $output .= '</div>';
                break;
                
            case 'features':
                $heading = trim($sec['heading'] ?? ($sec['title'] ?? 'Key Highlights & Amenities'));
                $items = $sec['items'] ?? [];
                if (is_string($items)) {
                    $items = array_filter(array_map('trim', explode("\n", $items)));
                }
                
                $output .= '<div class="blog-section-features">';
                if (!empty($heading)) {
                    $output .= '<h3><i class="fa-solid fa-star"></i> ' . htmlspecialchars($heading) . '</h3>';
                }
                if (!empty($items)) {
                    $output .= '<ul class="feature-checklist">';
                    foreach ($items as $item) {
                        if (trim($item) === '') continue;
                        $output .= '<li><i class="fa-solid fa-circle-check"></i> <span>' . htmlspecialchars(trim($item)) . '</span></li>';
                    }
                    $output .= '</ul>';
                }
                $output .= '</div>';
                break;
                
            case 'callout':
                $title = trim($sec['title'] ?? ($sec['heading'] ?? 'Pro Tip / Note'));
                $body = trim($sec['body'] ?? ($sec['content'] ?? ''));
                
                $output .= '<div class="blog-section-callout">';
                $output .= '<div class="callout-icon"><i class="fa-solid fa-lightbulb"></i></div>';
                $output .= '<div class="callout-content">';
                if (!empty($title)) {
                    $output .= '<h4 class="callout-title">' . htmlspecialchars($title) . '</h4>';
                }
                $output .= '<div class="callout-body">' . autoFormatBlogContent($body) . '</div>';
                $output .= '</div>';
                $output .= '</div>';
                break;
                
            case 'faq':
                $items = $sec['items'] ?? [];
                // Support single item or multiple
                if (empty($items) && !empty($sec['question'])) {
                    $items = [['question' => $sec['question'], 'answer' => $sec['answer'] ?? '']];
                }
                
                $faqHeading = trim($sec['heading'] ?? 'Frequently Asked Questions');
                $output .= '<div class="blog-section-faq">';
                if (!empty($faqHeading)) {
                    $output .= '<h3><i class="fa-solid fa-circle-question"></i> ' . htmlspecialchars($faqHeading) . '</h3>';
                }
                if (!empty($items) && is_array($items)) {
                    $output .= '<div class="faq-accordion-list">';
                    foreach ($items as $fIndex => $faq) {
                        $q = trim($faq['question'] ?? '');
                        $a = trim($faq['answer'] ?? '');
                        if (empty($q)) continue;
                        $output .= '<div class="faq-accordion-item' . ($fIndex === 0 ? ' active' : '') . '">';
                        $output .= '<button type="button" class="faq-accordion-header" onclick="toggleFaqAccordion(this)">';
                        $output .= '<span>' . htmlspecialchars($q) . '</span>';
                        $output .= '<i class="fa-solid fa-chevron-down"></i>';
                        $output .= '</button>';
                        $output .= '<div class="faq-accordion-body"' . ($fIndex === 0 ? ' style="display:block;"' : ' style="display:none;"') . '>';
                        $output .= '<div class="faq-inner">' . autoFormatBlogContent($a) . '</div>';
                        $output .= '</div>';
                        $output .= '</div>';
                    }
                    $output .= '</div>';
                }
                $output .= '</div>';
                break;
                
            case 'video':
                $vUrl = trim($sec['video_url'] ?? ($sec['url'] ?? ''));
                $vTitle = trim($sec['heading'] ?? ($sec['title'] ?? 'Resort Video Tour'));
                
                $output .= '<div class="blog-section-video">';
                if (!empty($vTitle)) {
                    $output .= '<h3><i class="fa-solid fa-video"></i> ' . htmlspecialchars($vTitle) . '</h3>';
                }
                if (preg_match('/(?:v=|\/embed\/|\/shorts\/|youtu\.be\/)([a-zA-Z0-9_-]{11})/i', $vUrl, $m)) {
                    $ytId = $m[1];
                    $output .= '<div class="video-embed-wrap"><iframe src="https://www.youtube.com/embed/' . htmlspecialchars($ytId) . '" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe></div>';
                } elseif (preg_match('/\.(mp4|webm)/i', $vUrl)) {
                    $output .= '<div class="video-embed-wrap"><video src="' . htmlspecialchars($vUrl) . '" controls playsinline preload="metadata"></video></div>';
                } elseif (!empty($vUrl)) {
                    $output .= '<div class="video-link-wrap"><a href="' . htmlspecialchars($vUrl) . '" target="_blank" rel="noopener" class="cta-btn-primary"><i class="fa-solid fa-play"></i> Watch Video / Reel</a></div>';
                }
                $output .= '</div>';
                break;
                
            case 'cta':
                $ctaTitle = trim($sec['heading'] ?? ($sec['title'] ?? 'Plan Your Event or Mountain Stay'));
                $ctaDesc = trim($sec['body'] ?? ($sec['content'] ?? 'Experience panoramic Himalayan luxury, grand banquet halls, and 5-star hospitality in Dharamshala.'));
                $ctaPhone = trim($sec['phone'] ?? '+917018841900');
                $ctaWhatsapp = trim($sec['whatsapp'] ?? '917018841900');
                
                $output .= '<div class="blog-section-cta">';
                $output .= '<div class="cta-content">';
                $output .= '<h3>' . htmlspecialchars($ctaTitle) . '</h3>';
                $output .= '<p>' . htmlspecialchars($ctaDesc) . '</p>';
                $output .= '</div>';
                $output .= '<div class="cta-btns">';
                $output .= '<a href="tel:' . htmlspecialchars($ctaPhone) . '" class="cta-btn-primary"><i class="fa-solid fa-phone"></i> Call Direct</a>';
                $output .= '<a href="https://wa.me/' . htmlspecialchars($ctaWhatsapp) . '?text=' . urlencode('Hello Dhauladhar Heights Resort, I would like to book a stay/event.') . '" target="_blank" class="cta-btn-whatsapp"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>';
                $output .= '</div>';
                $output .= '</div>';
                break;
                
            default:
                // Generic fallback for custom types
                $body = $sec['content'] ?? ($sec['body'] ?? '');
                $output .= autoFormatBlogContent($body);
                break;
        }
        
        $output .= '</div>';
        
        // Subtle divider between sections
        if ($index < $sectionCount - 1) {
            $output .= '<div class="section-divider"></div>';
        }
    }
    
    return $output;
}

/**
 * Compile structured sections array into clean, standalone semantic HTML.
 * Used when saving/updating blog posts so the 'content' column is never empty.
 */
function compileBlogSectionsToHtml($sections, $fallbackContent = '') {
    return renderAdvancedBlogSections($sections, $fallbackContent);
}

/**
 * Process raw POST submitted blog sections into sanitized structured array
 * Handles multi-type sections and image uploads
 */
function processSubmittedBlogSections($rawSections, $filesArray = null) {
    $processed = [];
    if (!is_array($rawSections)) return $processed;
    
    foreach ($rawSections as $index => $sec) {
        if (!is_array($sec)) continue;
        $type = $sec['type'] ?? 'text';
        $item = ['type' => $type, 'order' => $index];
        
        switch ($type) {
            case 'text':
                $item['heading'] = trim($sec['heading'] ?? ($sec['title'] ?? ''));
                $item['level'] = (!empty($sec['level']) && in_array($sec['level'], ['h2', 'h3'])) ? $sec['level'] : 'h2';
                $item['content'] = trim($sec['content'] ?? ($sec['body'] ?? ''));
                
                // Existing section images
                $secImages = [];
                if (!empty($sec['existing_images'])) {
                    $secImages = json_decode($sec['existing_images'], true) ?: [];
                }
                // Handle new uploaded images for this section
                if (isset($filesArray['name'][$index]['images']) && is_array($filesArray['name'][$index]['images'])) {
                    foreach ($filesArray['name'][$index]['images'] as $fIdx => $fName) {
                        if (!empty($fName) && isset($filesArray['error'][$index]['images'][$fIdx]) && $filesArray['error'][$index]['images'][$fIdx] === 0) {
                            $file = [
                                'name' => $filesArray['name'][$index]['images'][$fIdx],
                                'type' => $filesArray['type'][$index]['images'][$fIdx],
                                'tmp_name' => $filesArray['tmp_name'][$index]['images'][$fIdx],
                                'error' => $filesArray['error'][$index]['images'][$fIdx],
                                'size' => $filesArray['size'][$index]['images'][$fIdx]
                            ];
                            $up = uploadImage($file);
                            if ($up) $secImages[] = $up;
                        }
                    }
                }
                // Remove images if requested
                if (isset($_POST['remove_images_' . $index]) && is_array($_POST['remove_images_' . $index])) {
                    foreach ($_POST['remove_images_' . $index] as $rem) {
                        $k = array_search($rem, $secImages);
                        if ($k !== false) {
                            deleteImage($rem);
                            unset($secImages[$k]);
                        }
                    }
                    $secImages = array_values($secImages);
                }
                $item['images'] = $secImages;
                break;
                
            case 'image':
                $imgUrl = trim($sec['image_url'] ?? '');
                if (isset($_FILES['section_image_' . $index]) && $_FILES['section_image_' . $index]['error'] === 0) {
                    $up = uploadImage($_FILES['section_image_' . $index]);
                    if ($up) $imgUrl = $up;
                }
                $item['image_url'] = $imgUrl;
                $item['caption'] = trim($sec['caption'] ?? '');
                $item['layout'] = in_array($sec['layout'] ?? '', ['full', 'contained', 'centered']) ? $sec['layout'] : 'full';
                break;
                
            case 'gallery_2col':
                $img1 = trim($sec['image_url'] ?? '');
                if (isset($_FILES['section_file1_' . $index]) && $_FILES['section_file1_' . $index]['error'] === 0) {
                    $up = uploadImage($_FILES['section_file1_' . $index]);
                    if ($up) $img1 = $up;
                }
                $img2 = trim($sec['image_url_2'] ?? '');
                if (isset($_FILES['section_file2_' . $index]) && $_FILES['section_file2_' . $index]['error'] === 0) {
                    $up = uploadImage($_FILES['section_file2_' . $index]);
                    if ($up) $img2 = $up;
                }
                $item['image_url'] = $img1;
                $item['caption'] = trim($sec['caption'] ?? '');
                $item['image_url_2'] = $img2;
                $item['caption_2'] = trim($sec['caption_2'] ?? '');
                break;
                
            case 'quote':
                $item['quote'] = trim($sec['quote'] ?? '');
                $item['author'] = trim($sec['author'] ?? 'Hotel Dhauladhar Heights Team');
                break;
                
            case 'features':
                $item['heading'] = trim($sec['heading'] ?? 'Key Highlights & Amenities');
                $items = $sec['items'] ?? [];
                if (is_string($items)) {
                    $items = array_filter(array_map('trim', explode("\n", $items)));
                } elseif (is_array($items)) {
                    $items = array_values(array_filter(array_map('trim', $items)));
                }
                $item['items'] = $items;
                break;
                
            case 'callout':
                $item['title'] = trim($sec['title'] ?? 'Pro Tip / Note');
                $item['body'] = trim($sec['body'] ?? '');
                $item['style'] = trim($sec['style'] ?? 'tip');
                break;
                
            case 'faq':
                $item['heading'] = trim($sec['heading'] ?? 'Frequently Asked Questions');
                $faqList = [];
                if (isset($sec['items']) && is_array($sec['items'])) {
                    foreach ($sec['items'] as $fq) {
                        if (!empty($fq['question'])) {
                            $faqList[] = ['question' => trim($fq['question']), 'answer' => trim($fq['answer'] ?? '')];
                        }
                    }
                } elseif (!empty($sec['question'])) {
                    $faqList[] = ['question' => trim($sec['question']), 'answer' => trim($sec['answer'] ?? '')];
                }
                $item['items'] = $faqList;
                break;
                
            case 'video':
                $item['heading'] = trim($sec['heading'] ?? 'Resort Video Tour');
                $item['video_url'] = trim($sec['video_url'] ?? '');
                break;
                
            case 'cta':
                $item['heading'] = trim($sec['heading'] ?? 'Plan Your Event or Mountain Stay');
                $item['body'] = trim($sec['body'] ?? '');
                $item['phone'] = trim($sec['phone'] ?? '+917018841900');
                $item['whatsapp'] = trim($sec['whatsapp'] ?? '917018841900');
                break;
                
            default:
                $item['content'] = trim($sec['content'] ?? '');
                break;
        }
        
        $processed[] = $item;
    }
    return $processed;
}

?>
