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
        <a href="<?php echo $baseUrl; ?>/blog">/ Blog</a>
        <?php if ($categoryFilter): ?>
            <a href="#">/ Category</a>
        <?php elseif ($authorFilter): ?>
            <a href="#">/ Author</a>
        <?php elseif ($searchFilter): ?>
            <a href="#">/ Search</a>
        <?php endif; ?>
    </div>
</div>

<section class="blog-cards">
    <h1 class="mobile-blog-page-title"><?php echo $heroTitle; ?></h1>
    <div class="blog-layout">
        <div class="blog-left">
            <?php if (empty($blogsPaginated)): ?>
                <div class="no-posts" style="text-align: center; padding: 60px; background: white; border-radius: 12px;">
                    <i class="fas fa-blog" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
                    <h3>No blog posts found</h3>
                    <p>Check back soon or try another category or search query!</p>
                    <a href="<?php echo $baseUrl; ?>/blog" style="display: inline-block; margin-top: 15px; padding: 10px 20px; background: #000e3a; color: white; border-radius: 6px; text-decoration: none;">View All Posts</a>
                </div>
            <?php else: ?>
                <div class="blog-grid">
                    <?php foreach ($blogsPaginated as $blog): ?>
                        <?php 
                        $imageUrl = getBlogImageUrl($blog['featured_image']);
                        $blogUrl = getBlogUrl($blog);
                        $catName = $blog['category'] ?? 'General';
                        $catSlug = createSlug($catName);
                        $catUrl = $baseUrl . '/blog/category/' . $catSlug;
                        $formattedDate = !empty($blog['created_at']) ? date('F d, Y', strtotime($blog['created_at'])) : date('F d, Y');
                        ?>
                        <article class="blog-card" data-aos="fade-up">
                            <div class="blog-img">
                                <a href="<?php echo $blogUrl; ?>">
                                    <img src="<?php echo $imageUrl; ?>" 
                                         alt="<?php echo htmlspecialchars($blog['title']); ?>"
                                         onerror="this.onerror=null; this.src='<?php echo $baseUrl; ?>/images/default-blog.jpg';">
                                </a>
                            </div>
                            
                            <div class="blog-content">
                                <div class="meta">
                                    <span><i class="fas fa-calendar"></i> <?php echo $formattedDate; ?></span>
                                    <span><a href="<?php echo $catUrl; ?>" style="color: inherit; text-decoration: none;"><i class="fas fa-folder"></i> <?php echo htmlspecialchars($catName); ?></a></span>
                                </div>
                                
                                <h4><a href="<?php echo $blogUrl; ?>" style="color: inherit; text-decoration: none;"><?php echo htmlspecialchars($blog['title']); ?></a></h4>
                                
                                <div class="blog-excerpt">
                                    <?php echo htmlspecialchars($blog['excerpt'] ?? substr(strip_tags($blog['content']), 0, 150) . '...'); ?>
                                </div>
                            </div>
                            
                            <div class="blog-footer">
                                <a href="<?php echo $blogUrl; ?>">Read More</a>
                                <span class="tag">
                                    <a href="<?php echo $blogUrl; ?>">
                                        <i class="fa fa-arrow-right"></i>
                                    </a>
                                </span>
                            </div>
                        </article>
                    <?php endforeach; ?>
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
        
        <aside class="sidebar">
            <!-- SEARCH -->
            <div class="search-box">
                <form action="<?php echo $baseUrl; ?>/blog" method="GET" style="display: flex; width: 100%; align-items: center;">
                    <input type="text" name="search" id="searchInput" placeholder="Search blog posts..." value="<?php echo htmlspecialchars($searchFilter ?? ''); ?>" style="border: none; outline: none; width: 100%; padding-right: 30px;">
                    <button type="submit" style="background: none; border: none; cursor: pointer; color: #666;"><i class="fa fa-search"></i></button>
                </form>
            </div>

            <!-- POPULAR POSTS -->
            <div class="sidebar-card">
                <h3>Popular Posts</h3>
                <?php if (!empty($popularBlogs)): ?>
                    <?php foreach ($popularBlogs as $popular): ?>
                        <?php 
                        $popularImageUrl = getBlogImageUrl($popular['featured_image']);
                        $popularUrl = getBlogUrl($popular);
                        $popDate = !empty($popular['created_at']) ? date('M d, Y', strtotime($popular['created_at'])) : date('M d, Y');
                        ?>
                        <a href="<?php echo $popularUrl; ?>" class="post" style="text-decoration: none; display: flex; gap: 15px;">
                            <img src="<?php echo $popularImageUrl; ?>" 
                                 alt="<?php echo htmlspecialchars($popular['title']); ?>"
                                 onerror="this.onerror=null; this.src='<?php echo $baseUrl; ?>/images/default-blog.jpg';">
                            <div>
                                <p><?php echo htmlspecialchars($popular['title']); ?></p>
                                <span><?php echo $popDate; ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No popular posts yet.</p>
                <?php endif; ?>
            </div>
            
            <!-- ROOM CATEGORIES - Extended Room Details -->
            <div class="sidebar-card rooms-sidebar-card">
                <h3><i class="fa-solid fa-bed"></i> Room Categories</h3>
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

   <span class="weather-text" id="weatherText">
      Loading...
   </span>
</a>

  <div id="weatherPopup" class="weather-popup" aria-hidden="true">
    <div class="weather-popup-header">
      <i class="fa-solid fa-cloud-sun"></i>
      Current weather
    </div>
    <div id="weatherPopupContent" class="weather-popup-content">Loading...</div>
  </div>

</div>

<script>
    // Search functionality
    document.getElementById('searchInput')?.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const blogCards = document.querySelectorAll('.blog-card');
        
        blogCards.forEach(card => {
            const title = card.querySelector('h4').textContent.toLowerCase();
            const excerpt = card.querySelector('.blog-excerpt').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || excerpt.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
    
    // Debug info
    console.log('Blog page loaded');
    console.log('Base URL: <?php echo $baseUrl; ?>');
</script>

<script src="<?php echo $baseUrl; ?>/script.js"></script>
