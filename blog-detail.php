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

// Calculate read time
$wordCount = str_word_count(strip_tags($blog['content'] ?? ''));
$readTime = max(1, ceil($wordCount / 200)) . ' min read';

// Fetch related blogs (same category first, excluding current blog)
$relatedBlogs = [];
if (!empty($blog['category'])) {
    $catBlogs = getAllBlogs(4, 0, 'published', $blog['category']);
    foreach ($catBlogs as $cb) {
        if ($cb['id'] != $blog['id']) {
            $relatedBlogs[] = $cb;
        }
        if (count($relatedBlogs) >= 3) break;
    }
}
if (count($relatedBlogs) < 3) {
    $recents = getRecentBlogs(6);
    foreach ($recents as $rb) {
        if ($rb['id'] != $blog['id']) {
            $already = false;
            foreach ($relatedBlogs as $existing) {
                if ($existing['id'] == $rb['id']) { $already = true; break; }
            }
            if (!$already) {
                $relatedBlogs[] = $rb;
            }
        }
        if (count($relatedBlogs) >= 3) break;
    }
}

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

 

<main class="blog-detail-wrapper">
    <article class="blog-detail-article" data-aos="fade-up">
        <?php 
        $blogFormattedDate = !empty($blog['created_at']) ? date('F d, Y', strtotime($blog['created_at'])) : date('F d, Y');
        ?>
        <header class="blog-detail-post-header">
            <span class="blog-detail-category"><?php echo htmlspecialchars($blog['category'] ?? 'General'); ?></span>
            <h2><?php echo htmlspecialchars($blog['title']); ?></h2>
            <div class="blog-detail-meta">
                <span><i class="fa-regular fa-calendar"></i> <?php echo $blogFormattedDate; ?></span>
                <span><i class="fa-regular fa-user"></i> <?php echo htmlspecialchars($blog['author'] ?? 'Admin'); ?></span>
                <span><i class="fa-solid fa-location-dot"></i> Dharamshala, Himachal Pradesh</span>
            </div>
        </header>
        
        <?php if (!empty($blog['featured_image'])): ?>
            <div class="blog-featured-image-wrap">
                <img src="<?php echo $featuredImageUrl; ?>" 
                     alt="<?php echo htmlspecialchars($blog['title']); ?>" 
                     class="blog-featured-image"
                     onerror="this.onerror=null; this.src='<?php echo $baseUrl; ?>/images/default-blog.jpg';">
            </div>
        <?php endif; ?>

        <div class="blog-share-row">
            <a href="<?php echo $baseUrl; ?>/blog.php" class="blog-back-link"><i class="fa-solid fa-arrow-left"></i> Back to Blogs</a>
            <div class="blog-social-icons" aria-label="Share this blog">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($blogCanonicalUrl); ?>" target="_blank" rel="noopener" aria-label="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://www.instagram.com/dhauladhar_heights_resort" target="_blank" rel="noopener" aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://wa.me/?text=<?php echo urlencode($blog['title'] . ' ' . $blogCanonicalUrl); ?>" target="_blank" rel="noopener" aria-label="WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($blogCanonicalUrl); ?>" target="_blank" rel="noopener" aria-label="LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>
        </div>
        
        <div class="blog-detail-content">
            <?php echo renderAdvancedBlogSections($sections, $blog['content'] ?? ''); ?>

            <?php if (!empty($blog['meta_keywords'])): ?>
                <div class="blog-tags" style="margin-top:30px;">
                    <strong>Tags:</strong>
                    <?php foreach (explode(',', $blog['meta_keywords']) as $tag): ?>
                        <span>#<?php echo trim(htmlspecialchars($tag)); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="blog-detail-cta">
                <div>
                    <h3>Plan Your Event or Mountain Stay</h3>
                    <p>Experience panoramic Himalayan luxury, banquet halls, and five-star hospitality in Dharamshala.</p>
                </div>
                <div class="cta-btns">
                    <a href="<?php echo $baseUrl; ?>/contact.html" class="cta-btn-primary"><i class="fa-solid fa-calendar-check"></i> Book Consultation</a>
                    <a href="https://wa.me/917018841900?text=Hello%20Dhauladhar%20Heights%20Resort,%20I%20am%20interested%20in%20room/event%20booking." target="_blank" class="cta-btn-whatsapp"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
                </div>
            </div>
        </div>
    </article>
    
    <aside class="sidebar">
        <!-- POPULAR POSTS WIDGET -->
        <div class="widget">
            <h2>Recent Posts</h2>
            <?php if (!empty($popularBlogs)): ?>
                <ul class="recent-posts">
                    <?php foreach ($popularBlogs as $popular): ?>
                        <?php if ($popular['id'] != $blog['id']): ?>
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
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p style="color:#718096; font-size:14px;">No other posts available.</p>
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

        <!-- CONTACT / BOOKING WIDGET -->
        <div class="widget blog-contact-widget">
            <h2>Plan Your Event / Stay</h2>
            <p>Planning a holiday, corporate retreat, or dream destination wedding in Dharamshala?</p>
            <div class="contact-widget-buttons">
                <a href="tel:+917018841900" class="widget-btn call-btn"><i class="fa-solid fa-phone"></i> Call +91 70188-41900</a>
                <a href="https://wa.me/917018841900?text=Hello%20Dhauladhar%20Heights%20Resort,%20I%20would%20like%20to%20know%20more%20about%20room%20and%20wedding%20booking." target="_blank" class="widget-btn whatsapp-btn"><i class="fa-brands fa-whatsapp"></i> WhatsApp Enquiry</a>
            </div>
        </div>
    </aside>
</main>

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
    
    function toggleFaqAccordion(btn) {
        const item = btn.closest('.faq-accordion-item');
        if (!item) return;
        const body = item.querySelector('.faq-accordion-body');
        const isActive = item.classList.contains('active');
        
        // Toggle current item
        if (isActive) {
            item.classList.remove('active');
            if (body) body.style.display = 'none';
        } else {
            // Close other sibling items in same list
            const parentList = item.closest('.faq-accordion-list');
            if (parentList) {
                parentList.querySelectorAll('.faq-accordion-item.active').forEach(openItem => {
                    openItem.classList.remove('active');
                    const ob = openItem.querySelector('.faq-accordion-body');
                    if (ob) ob.style.display = 'none';
                });
            }
            item.classList.add('active');
            if (body) body.style.display = 'block';
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
 
