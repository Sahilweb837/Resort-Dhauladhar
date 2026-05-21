<?php
require_once __DIR__ . '/includes/functions.php';

// Get blog by slug
$slug = $_GET['slug'] ?? '';
$blog = getBlogBySlug($slug);

if (!$blog) {
    header('HTTP/1.0 404 Not Found');
    echo "<div style='text-align: center; padding: 100px;'><h1>Blog post not found</h1><p>The blog post you're looking for doesn't exist.</p><a href='blog.php' style='color: #000e3a;'>Back to Blog</a></div>";
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
include __DIR__ . '/includes/header.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($blog['title']); ?> - Hotel Dhauladhar</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
</head>
<body>

<!-- Include your header -->
  <!----------------------- hero section ---------------------->
<div class="common-hero blog" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('<?php echo $featuredImageUrl; ?>'); background-size: cover; background-position: center;">
    <h1 data-aos="fade-down" data-aos-duration="1200">Blog Details</h1>
    <div class="hero-links" data-aos="fade-up" data-aos-duration="1200" data-aos-delay="300">
      <a href="index.php">THE RESORT</a>
      <a href="blog.php">/Blog</a>
    </div>
  </div>

<section class="blog-detail">
    <div class="blog-detail-container">
        <div class="blog-main">
            <div class="blog-header">
                <h2><?php echo htmlspecialchars($blog['title']); ?></h2>
                <div class="blog-meta">
                    <span><i class="fas fa-calendar"></i> <?php echo date('F d, Y', strtotime($blog['created_at'])); ?></span>
                    <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($blog['author'] ?? 'Admin'); ?></span>
                    <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($blog['category'] ?? 'General'); ?></span>
                 </div>
            </div>
            
            <?php if (!empty($blog['featured_image'])): ?>
                <div class="featured-image-container">
                    <img src="<?php echo $featuredImageUrl; ?>" 
                         alt="<?php echo htmlspecialchars($blog['title']); ?>" 
                         class="blog-featured-image"
                         onerror="this.onerror=null; this.src='./images/default-blog.jpg';">
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
                                                     onerror="this.onerror=null; this.src='./images/default-blog.jpg';">
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
                            <?php echo nl2br(htmlspecialchars($blog['content'])); ?>
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
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="share-facebook">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                  <a href="https://www.instagram.com/dhauladhar_heights_resort" target="_blank" class="share-twitter" rel="noopener noreferrer">
    <i class="fab fa-instagram"></i> Instagram
</a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&title=<?php echo urlencode($blog['title']); ?>" target="_blank" class="share-linkedin">
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
                            <?php $popularImageUrl = getBlogImageUrl($popular['featured_image']); ?>
                            <a href="blog-detail.php?slug=<?php echo $popular['slug']; ?>" class="post" style="text-decoration: none; display: flex; gap: 15px;">
                                <img src="<?php echo $popularImageUrl; ?>" 
                                     alt="<?php echo htmlspecialchars($popular['title']); ?>"
                                     onerror="this.onerror=null; this.src='./images/default-blog.jpg';">
                                <div>
                                    <p><?php echo htmlspecialchars($popular['title']); ?></p>
                                    <span><?php echo date('M d, Y', strtotime($popular['created_at'])); ?></span>
                                </div>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No popular posts yet.</p>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</section>

<!-- Lightbox Modal -->
<div id="lightbox" class="lightbox" onclick="closeLightbox()">
    <span class="close-lightbox">&times;</span>
    <img class="lightbox-content" id="lightbox-img">
</div>

<!-- Include your footer -->
    <!-- Footer -->
   <footer class="hf-footer">

        <!-- MAIN FOOTER -->
        <div class="hf-main">

            <!-- CONTACT -->
            <div class="hf-col hf-contact hf-overlay" data-aos="fade-up" data-aos-delay="0">

                <h3>CONTACT INFO</h3>

                <ul>
                    <li><i class="fas fa-phone"></i> +91 80915-08200 <br>+91 70188-41900</li>
                    <li><i class="fas fa-envelope"></i> reservation@dhauladhar<br>heightsresort.com</li>
                    <li><i class="fas fa-map-marker-alt"></i> Dhauladhar Heights Resort

                        Tea Garden Road, Kunal Pathri,
                        Dharamshala, Himachal Pradesh 176217</li>
                </ul>

                <div class="hf-social">

                    <!-- Facebook -->
                    <a href="https://www.facebook.com/dhauladharheightsresort" target="_blank"
                        rel="noopener noreferrer">
                        <i class="fab fa-facebook-f"></i>
                    </a>

                    <!-- Instagram -->
                    <a href="https://www.instagram.com/dhauladhar_heights_resort" target="_blank"
                        rel="noopener noreferrer">
                        <i class="fab fa-instagram"></i>
                    </a>

                    <!-- YouTube -->
                    <a href="https://www.youtube.com/@dhauladharheightsresort" target="_blank"
                        rel="noopener noreferrer">
                        <i class="fab fa-youtube"></i>
                    </a>

                    <!-- LinkedIn -->
                    <a href="https://www.linkedin.com/company/dhauladhar-heights-resort" target="_blank"
                        rel="noopener noreferrer">
                        <i class="fab fa-linkedin-in"></i>
                    </a>


                </div>

            </div>

            <!-- LINKS -->
            <div class="hf-col hf-links" data-aos="fade-up" data-aos-delay="100">
                <h3>USEFUL LINKS</h3>
                <ul>
                    <li><a class="is-active" href="./about.php">About </a></li>
                    <li><a href="./rooms.html">Rooms & Suites</a></li>
                    <li><a href="./contact.html">Reservations</a></li>
                    <li><a href="./blog.php">News & Blogs</a></li>
                    <li><a href="./contact.html">Contact Us</a></li>
                    <li><a href="./privacypolicy.html">Privacy Policy</a></li>

                </ul>
            </div>

            <!-- GALLERY -->
            <div class="hf-col hf-gallery" data-aos="zoom-in" data-aos-delay="200">
                <h3>GALLERY</h3>
                <a href="gallery.html">
                    <div class="hf-gallery-grid">

                        <img src="./images/DSC09149-scaled.jpg" alt="gallery">
                        <img src="./images/sangeet1.jpg" alt="gallery">
                        <img src="./images/PHOTO-2024-01-20-20-04-52.jpg" alt="gallery">
                        <img src="./images/DSC09631-scaled.jpg" alt="gallery">
                        <img src="./images/aboutbanner - Copy.jpg" alt="gallery">
                        <img src="./images/PHOTO-2024-01-20-20-04-47-2.jpg" alt="gallery">
                    </div>
                </a>
            </div>

            <!-- NEWSLETTER -->
            <div class="hf-col hf-newsletter" data-aos="fade-left" data-aos-delay="300">
                <h3>NEWSLETTER</h3>
                <p>Subscribe our Newsletter</p>
                <input type="email" placeholder="Enter Email">
                <button>SUBSCRIBE NOW</button>
            </div>

        </div>

        <!-- COPYRIGHT -->
        <div class="hf-bottom">
                      © 2026, Hotel Dhauladhar Heights Resort. All Rights Reserved. <br><small>A Unit of NASV Warehouses & Constructions Pvt. Ltd.</small> 

        </div>

    </footer>


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
</body>
<script src="script.js"></script>
</html>
 
