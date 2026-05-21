<?php
require_once __DIR__ . '/includes/functions.php';

// Get latest 3 blogs for homepage
$latestBlogs = getRecentBlogs(3);
$popularBlogs = getPopularBlogs(3);
$categories = getCategories();

// Get base URL for assets
$baseUrl = getBaseUrl();
?><!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- stylesheet linked here -->
   
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.9/slick-theme.min.css"/>

  <link rel="stylesheet" href="./style.css">

  <!-- google fonts linked here -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Agu+Display:MORF@0..60&family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Edu+AU+VIC+WA+NT+Arrows:wght@400..700&family=Inconsolata:wght@200..900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Oswald:wght@200..700&family=Outfit:wght@100..900&family=Parkinsans:wght@300..800&family=Quicksand:wght@300..700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
    rel="stylesheet">

  <!-- cdn linked here -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- EmailJS -->
  <script src="https://cdn.jsdelivr.net/npm/emailjs-com@3/dist/email.min.js"></script>

  <!-- AOS CSS -->
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<title>Luxury Destination Wedding in Dharamshala | Dhauladhar Heights Resort</title>
</head>
<body>
  <!-- navbar -->
  <header class="site-header">

    <!-- TOP INFO -->
    <div class="top-info">
      <!-- <span><i class="fa-solid fa-location-dot"></i> Tea Garden Road, Kunal Pathri, Dharamshala</span> -->
            <span id="weather"><i class="fa-solid fa-cloud-sun"></i> Dharamshala weather...</span>

      <div class="header-contact">
        <span><i class="fa-solid fa-phone"></i> +91 80915-08200</span>
        <span><i class="fa-solid fa-envelope"></i> reservation@dhauladharheightsresort.com</span>
      </div>
    </div>

    <!-- NAVBAR -->
    <nav class="navbar">

      <!-- LEFT LINKS (3) -->
      <ul class="nav-left">
        <li><a href="index.php">THE RESORT</a></li>
        <li><a href="about.php">ABOUT</a></li>
        <li><a href="rooms.html">ROOMS</a></li>
      </ul>

      <!-- CENTER LOGO -->
      <div class="nav-logo">
        <a href="index.php">
          <img src="./images/dhr_logo_full_white_720.png" alt="Logo">
        </a>
      </div>

      <!-- RIGHT LINKS (3) -->
      <ul class="nav-right">
        <li class="nav-dropdown">
          <a href="#">EVENTS <i class="fa-solid fa-chevron-down"></i></a>
          <ul class="nav-dropdown-menu">
            <li>
              <a href="destination-wedding.php">
                <i class="fa-solid fa-heart"></i> Destination Wedding
              </a>
            </li>
            <li>
              <a href="events.html">
                <i class="fa-solid fa-champagne-glasses"></i> Event Venues
              </a>
            </li>
          </ul>
        </li>

        <li><a href="blog.php">BLOG</a></li>
        <li><a href="contact.html">CONTACT</a></li>
      </ul>

      <!-- MOBILE MENU -->
      <div class="menu-btn" id="menuOpen">
        <i class="fa-solid fa-bars"></i>
      </div>

    </nav>
  </header>

  <div class="overlay-menu" id="overlayMenu">
    <div class="overlay-right">
      <span class="close-btn" id="menuClose"><i class="fa-solid fa-xmark"></i></span>

      <ul class="overlay-links">
        <li><a href="index.php">THE RESORT</a></li>
        <li><a href="about.php">ABOUT</a></li>
        <li><a href="rooms.html"> ROOMS</a></li>
        <li><a href="destination-wedding.php">DESTINATION WEDDING</a></li>
        <li><a href="events.html"> EVENT VENUES</a></li>
        <li><a href="blog.php"> BLOG</a></li>
        <li><a href="contact.html"> CONTACT</a></li>
      </ul>

    </div>
  </div>



  <!----------------------- hero section ---------------------->
  <!-- <div class="common-hero weddinghero">
    <h1 data-aos="fade-down" data-aos-duration="1200">Vivah</h1>
    <h2>by</h2>
    <h3>DHAULADHAR HEIGHTS RESORT</h3>
  
  </div> -->

  <!-- HERO -->
<div class="hero">
  <img src="./images/vivah_by_dh.jpg" alt="wedding">

  <!-- WAVE -->
  <div class="wave-container">
    <svg viewBox="0 0 1440 320" preserveAspectRatio="none">
      <path d="M0,180
               C100,120 200,120 300,180
               C400,240 500,240 600,180
               C700,120 800,120 900,180
               C1000,240 1100,240 1200,180
               C1300,120 1400,120 1440,180
               L1440,340 L0,340 Z"
            fill="#ffffff"></path>
    </svg>
  </div>
</div>

<!-- LOGO -->
<div class="logo-wrapper">
  <div class="logo-floating">

    <div class="vivah">
      <img src="./images/Red.png" alt="logo">
    </div>

    <div class="by">by</div>
    <div class="resort">DHAULADHAR HEIGHTS RESORT</div>

  </div>
</div>

<!-- destination wedding slider -->
 <div class="main">
    <section class="title-section">

    <div class="title-wrapper">
      <span class="line" data-aos="fade-up" data-aos-duration="2000"></span>

      <div class="logo-circle" data-aos="fade-up" data-aos-duration="2000">
        <img src="./images/dhr_logo_icon.png" alt="logo">
      </div>

      <span class="line" data-aos="fade-up" data-aos-duration="2000"></span>
    </div>

    <h2 data-aos="fade-up" data-aos-duration="2000">Create Unforgettable Wedding Memories</h2>
      <p class="utilitesp" data-aos="fade-up" data-aos-duration="2000">
        Celebrate love in the lap of nature and create timeless memories with your loved ones at Dhauladhar Heights
        Resort – the perfect destination for luxury weddings in Himachal Pradesh.
      </p>
  </section>




<div class="destination-wrapper">

<button class="slider-btn slider-prev">
<i class="fa-solid fa-arrow-left"></i>
</button>

<button class="slider-btn slider-next">
<i class="fa-solid fa-arrow-right"></i>
</button>

<div class="destination-main-slider">

<div class="destination-slide">
<img src="./images/0P7A6637.jpg" alt="slide-2">
<!-- in this img bride and groom is standing  -->
<div class="destination-slide-text">
<h2>LUXURY DESTINATION WEDDINGS</h2>
<p>Create unforgettable memories with elegant destination wedding celebrations in breathtaking locations.</p>
</div>
</div>

<div class="destination-slide">
<img src="./images/DSC09631-scaled.jpg" alt="slide-3">
<!-- in this image groom is sitted in horse and enjoying -->
<div class="destination-slide-text">
<h2>ROYAL BARAAT EXPERIENCE</h2>
<p>Celebrate traditional wedding rituals with grand baraat entries, royal vibes, and cultural charm.</p>
</div>
</div>

<div class="destination-slide">
<img src="./images/0P7A5669.jpg" alt="slide-4">
<!-- random photo of bride -->
<div class="destination-slide-text">
<h2>ELEGANT BRIDAL MOMENTS</h2>
<p>Capture timeless bridal beauty with luxurious wedding photography and romantic destination settings.</p>
</div>
</div>

<div class="destination-slide">
<img src="./images/sangeet1.jpg" alt="slide-5">
<!-- cultural gaddi dance  -->
<div class="destination-slide-text">
<h2>TRADITIONAL CULTURAL CELEBRATIONS</h2>
<p>Enjoy vibrant wedding festivities with folk performances, music, dance, and unforgettable cultural experiences.</p>
</div>
</div>

</div>

<!-- MOBILE NAVIGATION -->
<div class="mobile-nav">
<button class="btn mobile-btn mobile-prev"><i class="fa-solid fa-arrow-left"></i></button>
<button class="btn mobile-btn mobile-next"><i class="fa-solid fa-arrow-right"></i></button>
</div>

</div>  </div>


  <!-- --------------------------------Weesing Festivals------------------------- -->
  <div class="weddingfestivalcontainer">
    <section class="title-section">
      <div class="title-wrapper">
        <span class="line" data-aos="fade-up" data-aos-duration="2000"></span>

        <div class="logo-circle" data-aos="fade-up" data-aos-duration="2000">
          <img src="./images/dhr_logo_icon.png" alt="logo">
        </div>
        <span class="line" data-aos="fade-up" data-aos-duration="2000"></span>
      </div>

      <h2 data-aos="fade-up" data-aos-duration="3000">Bespoke Wedding Experiences</h2>
      <p class="utilitesp" data-aos="fade-up" data-aos-duration="2000">
      Celebrate your dream destination wedding amidst breathtaking mountain views, elegant décor, and unforgettable moments crafted with timeless luxury.
      </p>
    </section>

    <!-- SLIDER -->
    <div class="container" data-aos="zoom-out-right">
      <div class="slide">
        <!-- <div class="item" style="background:linear-gradient(to bottom, rgba(8, 8, 8, 0.5), rgba(0,0,0,0.7)),
       url('./images/ _VIS3216 (1) (1).jpg'); background-size: cover; background-position: center top;">
          <div class="content">
            <div class="name">Couple Retreats</div>
            <div class="description">Celebrate togetherness with intimate experiences, scenic beauty, peaceful moments, and unforgettable memories of love.</div>
          </div>
        </div> -->

        <div class="item"
          style="background:linear-gradient(to bottom, rgba(0,0,0,0.5), rgba(0,0,0,0.7)), url('./images/weddingshoweimg.jpg'); background-size: cover; background-position:center;">
          <div class="content">
            <div class="name">Bridal Shower</div>
            <div class="description">Embrace joy with your loved ones at our thoughtfully designed venues for an
              unforgettable and personalised bridal shower.</div>
          </div>
        </div>

        <div class="item"
          style="background:linear-gradient(to bottom, rgba(0,0,0,0.5), rgba(0,0,0,0.7)), url('./images/mehndinewimg\ \(1\).jpg'); background-size: cover; background-position: center;">
          <div class="content">
            <div class="name">Pre Wedding Gathering</div>
            <div class="description">Enjoy joyful celebrations with your loved ones through laughter, beautiful décor, and cherished moments before the wedding.</div>
          </div>
        </div>

        <div class="item"
          style="background:linear-gradient(to bottom, rgba(0,0,0,0.5), rgba(0,0,0,0.7)), url('./images/TurmericRituals.jpg'); background-size: cover; background-position:center;">
          <div class="content">
            <div class="name">Haldi Ceremony</div>
            <div class="description">Experience the warmth of sacred traditions surrounded by fragrant flowers, golden hues, joyful blessings, and cheerful celebrations.</div>
          </div>
        </div>

        <div class="item"
          style="background:linear-gradient(to bottom, rgba(0,0,0,0.5), rgba(0,0,0,0.7)), url('./images/sangeetnew\ \(1\).jpg'); background-size: cover; background-position: center;">
          <div class="content">
            <div class="name">Musical Evenings</div>
            <div class="description">Dance beneath sparkling lights and celebrate togetherness with enchanting performances, rhythmic beats, and unforgettable festive energy.</div>
          </div>
        </div>
      

        <div class="item"
          style="background:linear-gradient(to bottom, rgba(0,0,0,0.5), rgba(0,0,0,0.7)), url('./images/DSC00899.JPG'); background-size: cover; background-position:center;">
          <div class="content">
            <div class="name">Forever Begins Here</div>
            <div class="description">Create a magical start to your journey together with a romantic and unforgettable celebration of love.</div>
          </div>
        </div>
      </div>

      <div class="button">
        <button class="prev" id-><i class="fa-solid fa-arrow-left"></i></button>
        <button class="next"><i class="fa-solid fa-arrow-right"></i></button>
      </div>
    </div>
  </div>

    <!--  BROCHURE SECTION -->
  <div class="roomscontainer">
    <section class="room-section rowreverse factsheetsection">
      <div class="room-content" data-aos="fade-up" data-aos-delay="200">
       <h2 class="room-heading">Discover Dhauladhar Heights Brochure</h2>

        <p class="room-description">
          Get an in-depth look at Dhauladhar Heights Resort, one of the best luxury hotels in Dharamshala, Himachal
          Pradesh. Surrounded by the breathtaking Dhauladhar mountain range, our resort offers a perfect blend of
          natural beauty, modern comfort, and premium hospitality. Our detailed brochure showcases everything you need
          to know about planning your perfect stay in the hills.
        </p>

        <p class="room-description">
          Inside the brochure, explore our spacious rooms and suites, luxury amenities, multi-cuisine dining,
          destination wedding venues, corporate event spaces, and personalized guest services. Whether you are searching
          for a top-rated resort in Dharamshala for a romantic getaway, family vacation, or grand wedding celebration,
          this brochure provides complete insights to help you make the right choice.
        </p>


        <a href="#" class="discover-button rect-btn">
          Download Brochure Sheet <i class="fas fa-arrow-right"></i>
        </a>
      </div>

      <div class="room-image">
        <div class="image-overlay-text">
          <img src="./images/DSC01246 (1).jpg" alt="Dhauladhar Heights Resort">
        </div>
      </div>
    </section>

  </div>
    <!------------- Contact form ----------------->

    <section class="title-section">
      <!-- <div class="bg-logo"></div> -->

      <div class="title-wrapper">
        <span class="line" data-aos="fade-up" data-aos-duration="2000"></span>

        <div class="logo-circle" data-aos="fade-up" data-aos-duration="2000">
          <img src="./images/dhr_logo_icon.png" alt="logo">
        </div>
        <span class="line" data-aos="fade-up" data-aos-duration="2000"></span>
      </div>

      <h2 data-aos="fade-up" data-aos-duration="3000">Craft Your Grand Destination Wedding Experience</h2>
      <p class="utilitesp" data-aos="fade-up" data-aos-duration="2000">
Begin your journey to an unforgettable celebration with bespoke wedding experiences, timeless elegance, and luxurious hospitality at Dhauladhar Heights Resort.
      </p>
    </section>

    <!--  -->
  <div class="weddingcontact-form" data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="200">
    <h2>Begin Your Celebration With Us</h2>
    <form id="">
      <div class="weddingcontactdetails">
        <input type="text" name="user_name" placeholder="Guest Name" required />
        <input type="email" name="user_email" placeholder="Enter your mail" required />
        <input type="phone" name="Contact_No" placeholder="Contact_No" required />
        <input type="number" name="pax" placeholder="NO_Of_Guests" required />
        <input type="Date" name="date" required />
        <button class="rect-btn">SUBMIT</button>
      </div>
    </form>
  </div>

  <!-- --------------------------Destination wedding places------------------------------ -->

  <div class="weddingplaces">
    <section class="title-section">
      <!-- <div class="bg-logo"></div> -->

      <div class="title-wrapper">
        <span class="line" data-aos="fade-up" data-aos-duration="2000"></span>

        <div class="logo-circle" data-aos="fade-up" data-aos-duration="2000">
          <img src="./images/dhr_logo_icon.png" alt="logo">
        </div>
        <span class="line" data-aos="fade-up" data-aos-duration="2000"></span>
      </div>

      <h2 data-aos="fade-up" data-aos-duration="3000"> Destination Weddings & Celebrations in Dharamshala</h2>
      <p class="utilitesp" data-aos="fade-up" data-aos-duration="2000">
        Turn your dream wedding into reality at Dhauladhar Heights Resort in Dharamshala. Nestled in the lap of the majestic Dhauladhar mountains, our resort offers a breathtaking backdrop for destination weddings, pre-wedding shoots, and intimate celebrations. From luxurious stays to scenic outdoor venues, discover everything you need to plan an unforgettable wedding experience in Himachal Pradesh.
      </p>
    </section>
        
        <div class="blog-grid-3">
            <?php if (!empty($latestBlogs)): ?>
                <?php foreach ($latestBlogs as $blog): ?>
                    <?php $imageUrl = getBlogImageUrl($blog['featured_image']); ?>
                    <article class="blog-card" data-aos="fade-up" data-aos-duration="1000">
                        <div class="blog-img">
                            <img src="<?php echo $imageUrl; ?>" 
                                 alt="<?php echo htmlspecialchars($blog['title']); ?>"
                                 onerror="this.onerror=null; this.src='./images/default-blog.jpg';">
                        </div>
                        <div class="blog-content">
                            <div class="meta">
                                <span><?php echo date('F d, Y', strtotime($blog['created_at'])); ?></span>
                                <span><?php echo htmlspecialchars($blog['category'] ?? 'General'); ?></span>
                            </div>
                            <h4><?php echo htmlspecialchars($blog['title']); ?></h4>
                            <div class="blog-excerpt">
                                <?php echo htmlspecialchars($blog['excerpt'] ?? substr(strip_tags($blog['content']), 0, 100) . '...'); ?>
                            </div>
                        </div>
                        <div class="blog-footer">
                            <a href="blog-detail.php?slug=<?php echo urlencode($blog['slug']); ?>">Read More</a>
                            <span class="tag">
                                <a href="blog-detail.php?slug=<?php echo urlencode($blog['slug']); ?>">
                                    <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                </a>
                            </span>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback static content if no blogs in database -->
                <article class="blog-card" data-aos="fade-right" data-aos-duration="1000">
                    <div class="blog-img">
                        <img src="./images/DSC09810.jpg" alt="blog-1">
                    </div>
                    <div class="blog-content">
                        <div class="meta">
                            <span>August 10, 2023</span>
                            <span>Interior</span>
                        </div>
                        <h3>Indian Wedding In Malta</h3>
                    </div>
                    <div class="blog-footer">
                        <a href="blog-detail.html?slug=indian-wedding-malta">Read More</a>
                        <span class="tag"><a href="blog-detail.html?slug=indian-wedding-malta"><i class="fa fa-arrow-right" aria-hidden="true"></i></a></span>
                    </div>
                </article>

                <article class="blog-card" data-aos="zoom-in" data-aos-duration="1000">
                    <div class="blog-img">
                        <img src="./images/DSC00496-HDR-Enhanced-NR-Edit.jpg" alt="blog-2">
                    </div>
                    <div class="blog-content">
                        <div class="meta">
                            <span>August 10, 2023</span>
                            <span>Interior</span>
                        </div>
                        <h3>Indian Wedding Venues In Barcelona</h3>
                    </div>
                    <div class="blog-footer active">
                        <a href="blog-detail.html?slug=indian-wedding-barcelona">Read More</a>
                        <span><a href="blog-detail.html?slug=indian-wedding-barcelona"><i class="fa fa-arrow-right" aria-hidden="true"></i></a></span>
                    </div>
                </article>

                <article class="blog-card" data-aos="fade-left" data-aos-duration="1000">
                    <div class="blog-img">
                        <img src="./images/hs.jpg" alt="blog-3">
                    </div>
                    <div class="blog-content">
                        <div class="meta">
                            <span>August 10, 2023</span>
                            <span>Interior</span>
                        </div>
                        <h3>Indian Wedding In Switzerland</h3>
                    </div>
                    <div class="blog-footer">
                        <a href="blog-detail.html?slug=indian-wedding-switzerland">Read More</a>
                        <span><a href="blog-detail.html?slug=indian-wedding-switzerland"><i class="fa fa-arrow-right" aria-hidden="true"></i></a></span>
                    </div>
                </article>
            <?php endif; ?>
        </div>
     
         
    </section>

  </div>


  <!-- faq -->
  <section class="faq-section">
    <h2>Destination Wedding – FAQs</h2>

    <div class="faq-item">
      <button class="faq-question">Is Dharamshala Heights & Resort suitable for destination
        weddings?<span>+</span></button>
      <div class="faq-answer">
        <p>Yes, it is a perfect destination wedding resort in Dharamshala with scenic views.</p>
      </div>
    </div>

    <div class="faq-item">
      <button class="faq-question">Do you provide wedding planning services?<span>+</span></button>
      <div class="faq-answer">
        <p>We assist with decoration, catering, accommodation, and event coordination.</p>
      </div>
    </div>

    <div class="faq-item">
      <button class="faq-question">What is the best season for a wedding in Dharamshala?<span>+</span></button>
      <div class="faq-answer">
        <p>March to June and September to November are ideal for destination weddings.</p>
      </div>
    </div>

  </section>

  <!----------------------------------footer---------------------- -->
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
          <a href="https://www.facebook.com/dhauladharheightsresort" target="_blank" rel="noopener noreferrer">
            <i class="fab fa-facebook-f"></i>
          </a>

          <!-- Instagram -->
          <a href="https://www.instagram.com/dhauladhar_heights_resort" target="_blank" rel="noopener noreferrer">
            <i class="fab fa-instagram"></i>
          </a>

          <!-- YouTube -->
          <a href="https://www.youtube.com/@dhauladharheightsresort" target="_blank" rel="noopener noreferrer">
            <i class="fab fa-youtube"></i>
          </a>

          <!-- LinkedIn -->
          <a href="https://www.linkedin.com/company/dhauladhar-heights-resort/" target="_blank"
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

  <!-- AOS JS  -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <!-- JQUERY -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

  <script src="script.js"></script>
</body>

</html>
