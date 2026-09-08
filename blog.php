<?php
require_once __DIR__ . '/includes/functions.php';

// Ensure table columns and default seed exist
ensureBlogTableColumns();

$categoryFilter = isset($_GET['category']) ? trim($_GET['category']) : null;
$authorFilter = isset($_GET['author']) ? trim($_GET['author']) : null;
$searchFilter = isset($_GET['search']) ? trim($_GET['search']) : null;

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 6;
$offset = ($page - 1) * $perPage;

$blogsPaginated = getAllBlogs($perPage, $offset, 'published', $categoryFilter, $authorFilter, $searchFilter);
$totalBlogs = getBlogCount('published', $categoryFilter, $authorFilter, $searchFilter);
$totalPages = ceil($totalBlogs / $perPage);

$popularBlogs = getPopularBlogs(5);
$categories = getCategories();

$baseUrl = getBaseUrl();

// Page Title logic
$heroTitle = 'Our Blog';
if ($categoryFilter) {
    $heroTitle = 'Category: ' . htmlspecialchars(ucwords(str_replace('-', ' ', $categoryFilter)));
} elseif ($authorFilter) {
    $heroTitle = 'Author: ' . htmlspecialchars(ucwords(str_replace('-', ' ', $authorFilter)));
} elseif ($searchFilter) {
    $heroTitle = 'Search: ' . htmlspecialchars($searchFilter);
}

// Build query parameter string for pagination links
$queryParams = [];
if ($categoryFilter) $queryParams['category'] = $categoryFilter;
if ($authorFilter) $queryParams['author'] = $authorFilter;
if ($searchFilter) $queryParams['search'] = $searchFilter;

function getPaginationUrl($p, $params) {
    $baseUrl = getBaseUrl();
    $params['page'] = $p;
    return $baseUrl . '/blog?' . http_build_query($params);
}

// Include header
include __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<div class="common-hero blog">
    <h1 data-aos="fade-down"><?php echo $heroTitle; ?></h1>
    <div class="hero-links" data-aos="fade-up">
        <a href="<?php echo $baseUrl; ?>/">Home</a>
        <a href="<?php echo $baseUrl; ?>/blog.php">/ Blog</a>
        <?php if ($categoryFilter): ?>
            <a href="#">/ Category</a>
        <?php elseif ($authorFilter): ?>
            <a href="#">/ Author</a>
        <?php elseif ($searchFilter): ?>
            <a href="#">/ Search</a>
        <?php endif; ?>
    </div>
</div>

<!-- BLOG HEADER (KD TENT HOUSE STYLE) -->
<div class="blog-header" data-aos="fade-up">
    <span class="sub-title">HOTEL DHAULADHAR HEIGHTS RESORT BLOG</span>
    <h2 class="main-title"><?php echo $categoryFilter ? 'Category: ' . htmlspecialchars(ucwords(str_replace('-', ' ', $categoryFilter))) : ($searchFilter ? 'Search: ' . htmlspecialchars($searchFilter) : 'Stories, Travel Guides & Himalayan Experiences'); ?></h2>
</div>

<section class="blog-cards">
    <div class="blog-wrapper">
        <!-- ===== MAIN BLOG GRID ===== -->
        <div>
            <?php if (empty($blogsPaginated)): ?>
                <div class="no-posts" style="text-align: center; padding: 60px 20px; background: white; border-radius: 14px; box-shadow: 0 10px 30px rgba(29,40,92,0.06);">
                    <i class="fas fa-blog" style="font-size: 54px; color: var(--secondary-color, #5DC5E3); margin-bottom: 20px; display:block;"></i>
                    <h3 style="font-family:'Cormorant Garamond',serif; font-size:26px; color:var(--primary-color, #1D285C); margin-bottom:10px;">No blog posts found</h3>
                    <p style="color:#718096; margin-bottom:20px;">Check back soon or try another category or search keyword!</p>
                    <a href="<?php echo $baseUrl; ?>/blog.php" style="display: inline-block; padding: 10px 24px; background: var(--primary-color, #1D285C); color: white; border-radius: 6px; text-decoration: none; font-weight:600; font-size:14px;">View All Posts</a>
                </div>
            <?php else: ?>
                <div class="blog-grid" id="mainBlogGrid">
                    <?php foreach ($blogsPaginated as $blog): ?>
                        <?php 
                        $imageUrl = getBlogImageUrl($blog['featured_image']);
                        $blogUrl = getBlogUrl($blog);
                        $catName = $blog['category'] ?? 'General';
                        $catSlug = createSlug($catName);
                        $catUrl = $baseUrl . '/blog/category/' . $catSlug;
                        $formattedDate = !empty($blog['created_at']) ? date('F d, Y', strtotime($blog['created_at'])) : date('F d, Y');
                        ?>
                        <article class="blog-card" data-aos="fade-up" data-title="<?php echo strtolower(htmlspecialchars($blog['title'])); ?>" data-category="<?php echo strtolower(htmlspecialchars($catName)); ?>">
                            <div class="img-container">
                                <a href="<?php echo $blogUrl; ?>" aria-label="Read <?php echo htmlspecialchars($blog['title']); ?>">
                                    <img src="<?php echo $imageUrl; ?>" 
                                         alt="<?php echo htmlspecialchars($blog['title']); ?>"
                                         onerror="this.onerror=null; this.src='<?php echo $baseUrl; ?>/images/default-blog.jpg';">
                                </a>
                            </div>
                            
                            <div class="card-content">
                                <span class="category"><a href="<?php echo $catUrl; ?>"><?php echo htmlspecialchars($catName); ?></a></span>
                                
                                <h3 class="post-title">
                                    <a href="<?php echo $blogUrl; ?>">
                                        <?php echo htmlspecialchars($blog['title']); ?>
                                    </a>
                                </h3>
                                
                                <div class="blog-excerpt">
                                    <?php echo htmlspecialchars($blog['excerpt'] ?? substr(strip_tags($blog['content']), 0, 130) . '...'); ?>
                                </div>
                                
                                <div class="post-meta">
                                    <span class="date"><i class="fa-regular fa-calendar"></i> <?php echo $formattedDate; ?></span>
                                    <a href="<?php echo $blogUrl; ?>" class="read-more-btn">Read Story <i class="fa-solid fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <div id="noMatchMessage" style="display:none; text-align:center; padding:50px 20px; background:white; border-radius:14px; box-shadow:0 10px 30px rgba(29,40,92,0.06); color:#777;">
                    <i class="fas fa-search" style="font-size:32px; margin-bottom:12px; color:var(--secondary-color, #5DC5E3); display:block;"></i>
                    <h4 style="font-family:'Cormorant Garamond',serif; font-size:24px; color:var(--primary-color, #1D285C); margin-bottom:8px;">No matching blog articles found</h4>
                    <p style="font-size:14px; color:#718096;">Try searching for another keyword or browse our room and wedding guides.</p>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="<?php echo getPaginationUrl($page - 1, $queryParams); ?>"><i class="fas fa-chevron-left"></i> Previous</a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="<?php echo getPaginationUrl($i, $queryParams); ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="<?php echo getPaginationUrl($page + 1, $queryParams); ?>">Next <i class="fas fa-chevron-right"></i></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <!-- ===== SIDEBAR (KD TENT HOUSE INSPIRED) ===== -->
        <aside class="sidebar">
            <!-- SEARCH WIDGET -->
            <div class="widget search-box">
                <h2>Search</h2>
                <form action="<?php echo $baseUrl; ?>/blog" method="GET" style="position: relative; width: 100%;">
                    <input type="text" name="search" id="blogSearchInput" placeholder="Search blog topics..." value="<?php echo htmlspecialchars($searchFilter ?? ''); ?>" onkeyup="filterBlogCards(this.value)">
                    <button type="submit" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--secondary-color, #5DC5E3); font-size: 15px;"><i class="fa fa-search"></i></button>
                </form>
            </div>

            <!-- CATEGORIES WIDGET -->
            <div class="widget">
                <h2>Categories</h2>
                <ul class="category-list">
                    <?php foreach ($categories as $cat): 
                        $cName = $cat['name'] ?? 'General';
                        $cSlug = createSlug($cat['slug'] ?? $cName);
                    ?>
                        <li>
                            <a href="<?php echo $baseUrl; ?>/blog/category/<?php echo $cSlug; ?>">
                                <i class="fa-solid fa-chevron-right" style="font-size:11px; margin-right:8px; color:var(--secondary-color, #5DC5E3);"></i>
                                <?php echo htmlspecialchars($cName); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- RECENT / POPULAR POSTS WIDGET -->
            <div class="widget">
                <h2>Recent Posts</h2>
                <?php if (!empty($popularBlogs)): ?>
                    <ul class="recent-posts">
                        <?php foreach ($popularBlogs as $popular): ?>
                            <?php 
                            $popularImageUrl = getBlogImageUrl($popular['featured_image']);
                            $popularUrl = getBlogUrl($popular);
                            $popDate = !empty($popular['created_at']) ? date('M d, Y', strtotime($popular['created_at'])) : date('M d, Y');
                            ?>
                            <li class="recent-post-item">
                                <a href="<?php echo $popularUrl; ?>" class="recent-post-link">
                                    <img src="<?php echo $popularImageUrl; ?>" 
                                         alt="<?php echo htmlspecialchars($popular['title']); ?>"
                                         onerror="this.onerror=null; this.src='<?php echo $baseUrl; ?>/images/default-blog.jpg';">
                                    <div class="recent-post-info">
                                        <span class="recent-post-title"><?php echo htmlspecialchars($popular['title']); ?></span>
                                        <span class="recent-post-date"><i class="fa-regular fa-calendar"></i> <?php echo $popDate; ?></span>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p style="color:#718096; font-size:14px;">No posts available yet.</p>
                <?php endif; ?>
            </div>
            
            <!-- ROOM CATEGORIES WIDGET -->
            <div class="widget">
                <h2><i class="fa-solid fa-bed" style="color:var(--secondary-color, #5DC5E3); margin-right:6px;"></i> Room Categories</h2>
                <div class="room-category-list">
                    <a href="<?php echo getRoomUrl('executive-room'); ?>" class="room-category-item">
                        <div class="room-cat-info">
                            <span class="room-name">Executive Room</span>
                            <span class="room-sub">Modern Comforts & Mountain View</span>
                        </div>
                        <span class="room-count">24 Rooms</span>
                    </a>
                    <a href="<?php echo getRoomUrl('executive-suite'); ?>" class="room-category-item">
                        <div class="room-cat-info">
                            <span class="room-name">Executive Suite</span>
                            <span class="room-sub">Spacious Living & Private Balcony</span>
                        </div>
                        <span class="room-count">40 Suites</span>
                    </a>
                    <a href="<?php echo getRoomUrl('presidential-suite'); ?>" class="room-category-item">
                        <div class="room-cat-info">
                            <span class="room-name">Presidential Suite</span>
                            <span class="room-sub">Penthouse Luxury & Panoramic Vista</span>
                        </div>
                        <span class="room-count">02 Suites</span>
                    </a>
                    <a href="<?php echo getRoomUrl('twin-bed'); ?>" class="room-category-item">
                        <div class="room-cat-info">
                            <span class="room-name">Twin Bedded Room</span>
                            <span class="room-sub">Shared Stay & Premium Amenities</span>
                        </div>
                        <span class="room-count">04 Rooms</span>
                    </a>
                    <a href="<?php echo getRoomUrl('deluxe-room'); ?>" class="room-category-item">
                        <div class="room-cat-info">
                            <span class="room-name">Deluxe Room</span>
                            <span class="room-sub">Cozy Retreat & Elegant Interior</span>
                        </div>
                        <span class="room-count">03 Rooms</span>
                    </a>
                </div>
            </div>

            <!-- EVENT / STAY CONSULTATION WIDGET (KD TENT HOUSE CONTACT STYLE ADAPTED FOR RESORT) -->
            <div class="widget blog-contact-widget">
                <h2>Plan Your Event / Stay</h2>
                <p>Planning a holiday, corporate retreat, or dream destination wedding in Dharamshala?</p>
                <div class="contact-widget-buttons">
                    <a href="tel:+917018841900" class="widget-btn call-btn"><i class="fa-solid fa-phone"></i> Call +91 70188-41900</a>
                    <a href="https://wa.me/917018841900?text=Hello%20Dhauladhar%20Heights%20Resort,%20I%20would%20like%20to%20know%20more%20about%20room%20and%20wedding%20booking." target="_blank" class="widget-btn whatsapp-btn"><i class="fa-brands fa-whatsapp"></i> WhatsApp Enquiry</a>
                </div>
            </div>
        </aside>
    </div>
</section>

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>

 <!-- FLOATING BUTTONS -->
<div class="floating-btns">
    <!-- WHATSAPP (DIRECT CHAT) -->
    <a href="https://wa.me/917018841900?text=Hello%20I%20want%20to%20book%20a%20room"
       target="_blank"
       class="float-btn whatsapp-btn">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <!-- CALL (DIRECT CALL) -->
    <a href="tel:+917018841900" class="float-btn call-btn">
        <i class="fa-solid fa-phone"></i>
    </a>
    
    <!-- weather Btn -->
    <a id="weather4phone" class="float-btn weather-btn">
       <i class="fa-solid fa-cloud-sun"></i>
       <span class="weather-text" id="weatherText">Loading...</span>
    </a>

    <div id="weatherPopup" class="weather-popup" aria-hidden="true">
        <div class="weather-popup-header">
            <i class="fa-solid fa-cloud-sun"></i> Current weather
        </div>
        <div id="weatherPopupContent" class="weather-popup-content">Loading...</div>
    </div>
</div>

<script>
    // Real-time client filter (KD Tent House inspired)
    function filterBlogCards(query) {
        var q = query.toLowerCase().trim();
        var cards = document.querySelectorAll('#mainBlogGrid .blog-card');
        var visibleCount = 0;
        cards.forEach(function(card) {
            var title = card.getAttribute('data-title') || '';
            var cat = card.getAttribute('data-category') || '';
            if (title.indexOf(q) > -1 || cat.indexOf(q) > -1) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        var msg = document.getElementById('noMatchMessage');
        if (msg) {
            msg.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }
</script>

<script src="<?php echo $baseUrl; ?>/script.js"></script>
