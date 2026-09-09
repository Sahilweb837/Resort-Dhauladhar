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
        $blogCategoryName = !empty($blog['category']) ? htmlspecialchars($blog['category']) : 'Resort & Travel';
        ?>
        <header class="blog-detail-post-header">
            <span class="blog-detail-category"><?php echo $blogCategoryName; ?></span>
            <h2><?php echo htmlspecialchars($blog['title']); ?></h2>
            <div class="blog-detail-meta">
                <span><i class="fa-regular fa-calendar"></i> <?php echo $blogFormattedDate; ?></span>
                <span><i class="fa-regular fa-clock"></i> <?php echo $readTime; ?></span>
                <span><i class="fa-solid fa-location-dot"></i> Dharamshala, Himachal Pradesh</span>
            </div>
        </header>
        
        <?php if (!empty($blog['featured_image'])): ?>
            <img src="<?php echo $featuredImageUrl; ?>" 
                 alt="<?php echo htmlspecialchars($blog['title']); ?>" 
                 class="blog-featured-image"
                 onerror="this.onerror=null; this.src='<?php echo $baseUrl; ?>/images/default-blog.jpg';">
        <?php endif; ?>

        <div class="blog-share-row">
            <a href="<?php echo $baseUrl; ?>/blog.php" class="blog-back-link"><i class="fa-solid fa-arrow-left"></i> Back to Blogs</a>
            <div class="blog-social-icons" aria-label="Share this blog">
                <!-- Facebook -->
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($blogCanonicalUrl); ?>" target="_blank" rel="noopener" aria-label="Share on Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <!-- Instagram -->
                <a href="https://www.instagram.com/dhauladhar_heights_resort" target="_blank" rel="noopener" aria-label="Visit Resort on Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <!-- WhatsApp -->
                <a href="https://wa.me/?text=<?php echo urlencode($blog['title'] . ' ' . $blogCanonicalUrl); ?>" target="_blank" rel="noopener" aria-label="Share on WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <!-- LinkedIn -->
                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($blogCanonicalUrl); ?>" target="_blank" rel="noopener" aria-label="Share on LinkedIn">
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
        </div>

        <div class="blog-detail-cta">
            <div>
                <span>Planning a celebration or mountain stay?</span>
                <h2>Let Dhauladhar Heights Resort host your stay, banquet, wedding and dining experience.</h2>
            </div>
            <a href="<?php echo $baseUrl; ?>/contact.html" class="cta-btn-primary"><i class="fa-solid fa-calendar-check"></i> Book Consultation</a>
        </div>
    </article>
    
    <!-- SIDEBAR (KD TENT HOUSE LAYOUT) -->
    <aside class="sidebar blog-detail-sidebar">
        <!-- SEARCH WIDGET -->
        <div class="widget search-box">
            <h2>Search</h2>
            <input type="text" id="blogSearchInput" placeholder="Search blog topics..." onkeyup="filterSidebarPosts(this.value)">
        </div>

        <!-- RECENT POSTS WIDGET -->
        <div class="widget">
            <h2>Recent Posts</h2>
            <ul class="detail-recent-posts" id="sidebarRecentPosts">
                <?php if (!empty($popularBlogs)): ?>
                    <?php foreach ($popularBlogs as $popular): ?>
                        <?php if ($popular['id'] != $blog['id']): ?>
                            <?php $popularUrl = getBlogUrl($popular); ?>
                            <li><a href="<?php echo $popularUrl; ?>"><?php echo htmlspecialchars($popular['title']); ?></a></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li><span style="color:#718096; font-size:13.5px;">No other posts available.</span></li>
                <?php endif; ?>
            </ul>
        </div>
        
        <!-- ACCOMMODATIONS & EXPERIENCES WIDGET -->
        <div class="widget">
            <h2>Accommodations & Experiences</h2>
            <ul>
                <li><a href="<?php echo getRoomUrl('executive-room'); ?>">Executive Room</a></li>
                <li><a href="<?php echo getRoomUrl('executive-suite'); ?>">Executive Suite</a></li>
                <li><a href="<?php echo getRoomUrl('presidential-suite'); ?>">Presidential Suite</a></li>
                <li><a href="<?php echo getRoomUrl('twin-bed'); ?>">Twin Bedded Room</a></li>
                <li><a href="<?php echo getRoomUrl('deluxe-room'); ?>">Deluxe Room</a></li>
                <li><a href="<?php echo $baseUrl; ?>/events.php">Destination Weddings & Banquets</a></li>
                <li><a href="<?php echo $baseUrl; ?>/about.php">Tea Garden Mountain View</a></li>
                <li><a href="<?php echo $baseUrl; ?>/contact.html">Private Dining & Events</a></li>
            </ul>
        </div>

        <!-- QUICK CONTACT WIDGET -->
        <div class="widget blog-contact-widget">
            <h2>Quick Contact</h2>
            <p><i class="fa-solid fa-phone"></i> +91 70188-41900</p>
            <p><i class="fa-solid fa-envelope"></i> reservation@dhauladharheightsresort.com</p>
            <a href="<?php echo $baseUrl; ?>/contact.html" class="send-enquiry-btn">Send Enquiry</a>
        </div>
    </aside>
</main>

<!-- RELATED IDEAS / STORIES SECTION (KD TENT HOUSE STYLE) -->
<section class="related-blog-section">
    <div class="common-header">
        <h2>Related <span>Stories</span></h2>
        <p>More destination guides, travel tips and luxury retreat ideas in Dharamshala.</p>
    </div>
    <div class="related-blog-grid">
        <?php if (!empty($relatedBlogs)): ?>
            <?php foreach ($relatedBlogs as $rel): ?>
                <?php 
                $relImageUrl = getBlogImageUrl($rel['featured_image']);
                $relUrl = getBlogUrl($rel);
                $relDate = !empty($rel['created_at']) ? date('F d, Y', strtotime($rel['created_at'])) : date('F d, Y');
                $relCat = !empty($rel['category']) ? htmlspecialchars($rel['category']) : 'Resort & Travel';
                ?>
                <article class="blog-card" data-aos="fade-up">
                    <div class="img-container">
                        <a href="<?php echo $relUrl; ?>" aria-label="<?php echo htmlspecialchars($rel['title']); ?>">
                            <img src="<?php echo $relImageUrl; ?>" 
                                 alt="<?php echo htmlspecialchars($rel['title']); ?>"
                                 onerror="this.onerror=null; this.src='<?php echo $baseUrl; ?>/images/default-blog.jpg';">
                        </a>
                    </div>
                    <div class="card-content">
                        <span class="category"><?php echo $relCat; ?></span>
                        <h3 class="post-title">
                            <a href="<?php echo $relUrl; ?>"><?php echo htmlspecialchars($rel['title']); ?></a>
                        </h3>
                        <div class="post-meta">
                            <span class="date"><i class="fa-regular fa-calendar"></i> <?php echo $relDate; ?></span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <article class="blog-card" data-aos="fade-up">
                <div class="img-container">
                    <a href="<?php echo $baseUrl; ?>/blog.php">
                        <img src="<?php echo $baseUrl; ?>/images/DSC09810.jpg" alt="Dharamshala Travel Guide">
                    </a>
                </div>
                <div class="card-content">
                    <span class="category">TRAVEL GUIDE</span>
                    <h3 class="post-title"><a href="<?php echo $baseUrl; ?>/blog.php">Top 10 Hidden Gems to Visit in Dharamshala in 2026</a></h3>
                    <div class="post-meta">
                        <span class="date"><i class="fa-regular fa-calendar"></i> April 21, 2026</span>
                    </div>
                </div>
            </article>
            <article class="blog-card" data-aos="fade-up" data-aos-delay="100">
                <div class="img-container">
                    <a href="<?php echo $baseUrl; ?>/blog.php">
                        <img src="<?php echo $baseUrl; ?>/images/DSC00496-HDR-Enhanced-NR-Edit.jpg" alt="Tea Garden Luxury Stay">
                    </a>
                </div>
                <div class="card-content">
                    <span class="category">EXPERIENCES</span>
                    <h3 class="post-title"><a href="<?php echo $baseUrl; ?>/blog.php">Why Staying Amidst Tea Gardens is the Ultimate Mountain Retreat</a></h3>
                    <div class="post-meta">
                        <span class="date"><i class="fa-regular fa-calendar"></i> May 12, 2026</span>
                    </div>
                </div>
            </article>
            <article class="blog-card" data-aos="fade-up" data-aos-delay="200">
                <div class="img-container">
                    <a href="<?php echo $baseUrl; ?>/blog.php">
                        <img src="<?php echo $baseUrl; ?>/images/hs.jpg" alt="Destination Wedding in Kangra">
                    </a>
                </div>
                <div class="card-content">
                    <span class="category">WEDDINGS</span>
                    <h3 class="post-title"><a href="<?php echo $baseUrl; ?>/blog.php">Planning a Dream Himalayan Destination Wedding at Dhauladhar</a></h3>
                    <div class="post-meta">
                        <span class="date"><i class="fa-regular fa-calendar"></i> June 08, 2026</span>
                    </div>
                </div>
            </article>
        <?php endif; ?>
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
    
    function filterSidebarPosts(query) {
        var q = query.toLowerCase();
        var list = document.getElementById('sidebarRecentPosts');
        if (!list) return;
        var items = list.getElementsByTagName('li');
        for (var i = 0; i < items.length; i++) {
            var txt = items[i].textContent || items[i].innerText;
            items[i].style.display = txt.toLowerCase().indexOf(q) > -1 ? '' : 'none';
        }
    }
    
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
 
