<?php
require_once __DIR__ . '/includes/functions.php';

$baseUrl = getBaseUrl();

// Get blog by slug or ID from query param or URI path
$slug = $_GET['slug'] ?? $_GET['id'] ?? $_GET['post'] ?? '';
if (empty($slug)) {
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segments = array_values(array_filter(explode('/', $requestUri)));
    $blogIndex = array_search('blog', $segments);
    if ($blogIndex !== false && isset($segments[$blogIndex + 1])) {
        // If URI is /blog/category/slug or /blog/slug
        $slug = end($segments);
    }
}

$blog = getBlogBySlug($slug);

// Fallback search if exact slug not matched
if (!$blog && !empty($slug)) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE (slug = ? OR id = ?) AND status = 'published'");
    $stmt->execute([$slug, $slug]);
    $blog = $stmt->fetch();
}

if (!$blog) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    exit();
}

// Increment view count
incrementBlogViews($blog['id']);

$popularBlogs = getPopularBlogs(5);

// Decode sections from JSON
$sections = [];
if (!empty($blog['sections'])) {
    $sections = json_decode($blog['sections'], true);
}

$featuredImageUrl = getBlogImageUrl($blog['featured_image']);
$blogCanonicalUrl = getBlogUrl($blog);
$metaDesc = !empty($blog['meta_description']) ? $blog['meta_description'] : (!empty($blog['excerpt']) ? $blog['excerpt'] : substr(strip_tags($blog['content']), 0, 160));
$metaKeys = !empty($blog['meta_keywords']) ? $blog['meta_keywords'] : 'Dhauladhar Heights Resort, Dharamshala, ' . ($blog['category'] ?? 'Blog');

$pageTitle = $blog['title'] . ' - Hotel Dhauladhar Heights Resort';
$pageMetaDescription = $metaDesc;
$pageMetaKeywords = $metaKeys;

ob_start();
?>
    <link rel="canonical" href="<?php echo $blogCanonicalUrl; ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?php echo $blogCanonicalUrl; ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($blog['title']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($metaDesc); ?>">
    <meta property="og:image" content="<?php echo $featuredImageUrl; ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo $blogCanonicalUrl; ?>">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($blog['title']); ?>">
    <meta property="twitter:description" content="<?php echo htmlspecialchars($metaDesc); ?>">
    <meta property="twitter:image" content="<?php echo $featuredImageUrl; ?>">

    <!-- Schema.org BlogPosting JSON-LD -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BlogPosting",
      "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "<?php echo $blogCanonicalUrl; ?>"
      },
      "headline": <?php echo json_encode($blog['title']); ?>,
      "description": <?php echo json_encode($metaDesc); ?>,
      "image": <?php echo json_encode($featuredImageUrl); ?>,
      "author": {
        "@type": "Person",
        "name": <?php echo json_encode($blog['author'] ?? 'Admin'); ?>
      },
      "publisher": {
        "@type": "Organization",
        "name": "Hotel Dhauladhar Heights Resort",
        "logo": {
          "@type": "ImageObject",
          "url": "<?php echo $baseUrl; ?>/images/dhr_logo_full_white_720.png"
        }
      },
      "datePublished": "<?php echo date('c', strtotime($blog['created_at'])); ?>",
      "dateModified": "<?php echo date('c', strtotime($blog['updated_at'] ?? $blog['created_at'])); ?>"
    }
    </script>
<?php
$extraHead = ob_get_clean();

include __DIR__ . '/includes/header.php';
?>

<!----------------------- hero section ---------------------->
<div class="common-hero blog" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('<?php echo $featuredImageUrl; ?>'); background-size: cover; background-position: center;">
    <h1 data-aos="fade-down" data-aos-duration="1200"><?php echo htmlspecialchars($blog['title']); ?></h1>
    <div class="hero-links" data-aos="fade-up" data-aos-duration="1200" data-aos-delay="300">
      <a href="<?php echo $baseUrl; ?>/">THE RESORT</a>
      <a href="<?php echo $baseUrl; ?>/blog">/ Blog</a>
      <a href="#">/ <?php echo htmlspecialchars($blog['category'] ?? 'General'); ?></a>
    </div>
  </div>

<section class="blog-detail">
    <div class="blog-detail-container">
        <div class="blog-main">
            <?php 
            $blogFormattedDate = !empty($blog['created_at']) ? date('F d, Y', strtotime($blog['created_at'])) : date('F d, Y');
            ?>
            <div class="blog-header">
                <h2><?php echo htmlspecialchars($blog['title']); ?></h2>
                <div class="blog-meta">
                    <span><i class="fas fa-calendar"></i> <?php echo $blogFormattedDate; ?></span>
                    <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($blog['author'] ?? 'Admin'); ?></span>
                    <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($blog['category'] ?? 'General'); ?></span>
                 </div>
            </div>
            
            <?php if (!empty($blog['featured_image'])): ?>
                <div class="featured-image-container">
                    <img src="<?php echo $featuredImageUrl; ?>" 
                         alt="<?php echo htmlspecialchars($blog['title']); ?>" 
                         class="blog-featured-image"
                         onerror="this.onerror=null; this.src='<?php echo $baseUrl; ?>/images/default-blog.jpg';">
                </div>
            <?php endif; ?>
            
            <div class="blog-content">
                <?php if (!empty($sections) && is_array($sections)): ?>
                    <?php foreach ($sections as $index => $section): ?>
                        <div class="blog-section" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                            <?php if (!empty($section['title'])): ?>
                                <h2 class="section-title"><?php echo htmlspecialchars($section['title']); ?></h2>
                            <?php endif; ?>
                            
                            <div class="section-content">
                                <?php echo $section['content']; ?>
                            </div>
                            
                            <?php if (!empty($section['images']) && is_array($section['images'])): ?>
                                <div class="section-gallery">
                                    <?php foreach ($section['images'] as $image): ?>
                                        <?php if (!empty($image)): ?>
                                            <?php $imageUrl = getBlogImageUrl($image); ?>
                                            <div class="gallery-item" onclick="openLightbox('<?php echo $imageUrl; ?>')">
                                                <img src="<?php echo $imageUrl; ?>" 
                                                     alt="<?php echo htmlspecialchars($section['title'] ?? 'Image'); ?>"
                                                     onerror="this.onerror=null; this.src='<?php echo $baseUrl; ?>/images/default-blog.jpg';">
                                                <div class="gallery-overlay">
                                                    <i class="fas fa-search-plus"></i>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($index < count($sections) - 1): ?>
                            <div class="section-divider"></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="blog-section">
                        <div class="section-content">
                            <?php echo nl2br($blog['content']); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($blog['meta_keywords'])): ?>
                <div class="blog-tags">
                    <strong>Tags:</strong>
                    <?php foreach (explode(',', $blog['meta_keywords']) as $tag): ?>
                        <span>#<?php echo trim(htmlspecialchars($tag)); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="blog-share">
                <h4>Share this post:</h4>
                <div class="share-buttons">
                    <a href="https://www.facebook.com/dhauladharheightsresort" target="_blank" class="share-facebook">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                  <a href="https://www.instagram.com/dhauladhar_heights_resort" target="_blank" class="share-twitter" rel="noopener noreferrer">
    <i class="fab fa-instagram"></i> Instagram
</a>
                    <a href="https://www.linkedin.com/company/dhauladhar-heights-resort" target="_blank" class="share-linkedin">
                        <i class="fab fa-linkedin-in"></i> LinkedIn
                    </a>
                </div>
            </div>
        </div>
        
        <aside class="sidebar">
            <div class="sidebar-card">
                <h3>Popular Posts</h3>
                <?php if (!empty($popularBlogs)): ?>
                    <?php foreach ($popularBlogs as $popular): ?>
                        <?php if ($popular['id'] != $blog['id']): ?>
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
                        <?php endif; ?>
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

<!-- Lightbox Modal -->
<div id="lightbox" class="lightbox" onclick="closeLightbox()">
    <span class="close-lightbox">&times;</span>
    <img class="lightbox-content" id="lightbox-img">
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>


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
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init();
    
    function subscribeNewsletter() {
        const email = document.getElementById('newsletterEmail').value;
        if (email) {
            alert('Thank you for subscribing!');
            document.getElementById('newsletterEmail').value = '';
        } else {
            alert('Please enter your email address');
        }
    }
    
    function openLightbox(imgSrc) {
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightbox-img');
        lightboxImg.src = imgSrc;
        lightbox.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    
    function closeLightbox() {
        const lightbox = document.getElementById('lightbox');
        lightbox.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLightbox();
        }
    });
    
    // Mobile Menu Toggle
    document.addEventListener('DOMContentLoaded', function() {
        const menuOpen = document.getElementById('menuOpen');
        const menuClose = document.getElementById('menuClose');
        const overlayMenu = document.getElementById('overlayMenu');
        if (menuOpen && menuClose && overlayMenu) {
            menuOpen.addEventListener('click', function() {
                overlayMenu.classList.add('active');
            });
            menuClose.addEventListener('click', function() {
                overlayMenu.classList.remove('active');
            });
        }
    });
</script>
<script src="<?php echo $baseUrl; ?>/script.js"></script>
</body>
</html>
 
