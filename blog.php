<?php
require_once __DIR__ . '/includes/functions.php';

// Get all published blogs
$blogs = getAllBlogs(null, 0, 'published');
$popularBlogs = getPopularBlogs(5);
$categories = getCategories();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 6;
$offset = ($page - 1) * $perPage;
$blogsPaginated = getAllBlogs($perPage, $offset, 'published');
$totalBlogs = getBlogCount('published');
$totalPages = ceil($totalBlogs / $perPage);

$baseUrl = getBaseUrl();

// Include header
include __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<div class="common-hero blog">
    <h1 data-aos="fade-down">Our Blog</h1>
    <div class="hero-links" data-aos="fade-up">
        <a href="index.php">Home</a>
        <a href="blog.php">/ Blog</a>
    </div>
</div>

<section class="blog-cards">
    <div class="blog-layout">
        <div class="blog-left">
            <?php if (empty($blogsPaginated)): ?>
                <div class="no-posts" style="text-align: center; padding: 60px; background: white; border-radius: 12px;">
                    <i class="fas fa-blog" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
                    <h3>No blog posts yet</h3>
                    <p>Check back soon for updates!</p>
                </div>
            <?php else: ?>
                <div class="blog-grid">
                    <?php foreach ($blogsPaginated as $blog): ?>
                        <?php 
                        $imageUrl = getBlogImageUrl($blog['featured_image']);
                        ?>
                        <article class="blog-card" data-aos="fade-up">
                            <div class="blog-img">
                                <img src="<?php echo $imageUrl; ?>" 
                                     alt="<?php echo htmlspecialchars($blog['title']); ?>"
                                     onerror="this.onerror=null; this.src='<?php echo $baseUrl; ?>/images/default-blog.jpg';">
                            </div>
                            
                            <div class="blog-content">
                                <div class="meta">
                                    <span><?php echo date('F d, Y', strtotime($blog['created_at'])); ?></span>
                                    <span><?php echo htmlspecialchars($blog['category'] ?? 'General'); ?></span>
                                </div>
                                
                                <h4><?php echo htmlspecialchars($blog['title']); ?></h4>
                                
                                <div class="blog-excerpt">
                                    <?php echo htmlspecialchars($blog['excerpt'] ?? substr(strip_tags($blog['content']), 0, 150) . '...'); ?>
                                </div>
                            </div>
                            
                            <div class="blog-footer">
                                <a href="blog-detail.php?slug=<?php echo urlencode($blog['slug']); ?>">Read More</a>
                                <span class="tag">
                                    <a href="blog-detail.php?slug=<?php echo urlencode($blog['slug']); ?>">
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
                            <a href="?page=<?php echo $page - 1; ?>"><i class="fas fa-chevron-left"></i> Previous</a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>">Next <i class="fas fa-chevron-right"></i></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <aside class="sidebar">
            <!-- SEARCH -->
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search blog posts...">
                <i class="fa fa-search"></i>
            </div>
            
            <!-- POPULAR POSTS -->
            <div class="sidebar-card">
                <h3>Popular Posts</h3>
                <?php if (!empty($popularBlogs)): ?>
                    <?php foreach ($popularBlogs as $popular): ?>
                        <?php 
                        $popularImageUrl = getBlogImageUrl($popular['featured_image']);
                        ?>
                        <div class="post">
                            <img src="<?php echo $popularImageUrl; ?>" 
                                 alt="<?php echo htmlspecialchars($popular['title']); ?>"
                                 onerror="this.onerror=null; this.src='<?php echo $baseUrl; ?>/images/default-blog.jpg';">
                            <div>
                                <p><?php echo htmlspecialchars($popular['title']); ?></p>
                                <span><?php echo date('M d, Y', strtotime($popular['created_at'])); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No popular posts yet.</p>
                <?php endif; ?>
            </div>
            
            <!-- BLOG CATEGORIES -->
            <!-- <div class="sidebar-card">
                <h3>Categories</h3>
                <ul class="category">
                    <?php foreach ($categories as $cat): ?>
                        <li>
                            <?php echo htmlspecialchars($cat['name']); ?>
                            <span>(<?php echo rand(5, 20); ?>)</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div> -->
            
            <!-- ROOM CATEGORIES - Static as requested -->
            <div class="sidebar-card">
                    <h3>Rooms Category</h3>
                    <ul class="category">
                        <a href="room-details.html?room=executive-room">
                            <li>Executive Room<span>24</span></li>
                        </a>
                        <a href="room-details.html?room=executive-suite">
                            <li>Executive Suite<span>40</span></li>
                        </a>
                        <a href="room-details.html?room=presidential-suite">
                            <li>Presidential Suite<span>02</span></li>
                        </a>
                        <a href="room-details.html?room=twin-bed">
                            <li>Twin Bed<span>04</span></li>
                        </a>
                        <a href="room-details.html?room=deluxe-room">
                            <li>Deluxe Room<span>03</span></li>
                        </a>
                    </ul>
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

<script src="script.js"></script>
