<?php
require_once __DIR__ . '/includes/functions.php';

// Get latest 3 blogs for about page
$latestBlogs = getRecentBlogs(3);
$popularBlogs = getPopularBlogs(3);
$categories = getCategories();

// Get base URL for assets
$baseUrl = getBaseUrl();
$googleReviewsUrl = 'https://www.google.com/travel/hotels/entity/CgsI7_2S5eW34JLyARAB/reviews?q=hotel%20dhauladhar%20dharamshala%20tea%20garden&g2lb=4965990%2C72471280%2C72560029%2C72573224%2C72647020%2C72686036%2C72803964%2C72882230%2C73059275%2C73064764%2C121608705&hl=en-IN&gl=in&cs=1&ssta=1&ts=CAESCgoCCAMKAggDEAAaIAoCGgASGhIUCgcI6g8QBRgTEgcI6g8QBRgUGAEyAhAAKgkKBToDSU5SGgA&rp=OAFAAEgCePPAgqeB_cqLowF427jXrprkkIfHAXiE983FlLfM9p0BwAEDygJtqgFqCggvbS8wN2NseBABKg4iCnRlYSBnYXJkZW4oADIfEAEiG4Oo_nPMDLigun9r2AzW96JjgUj-QoWYoNaf4TIrEAIiJ2hvdGVsIGRoYXVsYWRoYXIgZGhhcmFtc2hhbGEgdGVhIGdhcmRlbg&ap=ugEHcmV2aWV3cw&ictx=111&utm_campaign=sharing&utm_medium=link&utm_source=htls&ved=0CAAQ5JsGahcKEwiY3-_LiMeUAxUAAAAAHQAAAAAQBA';
?><!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Elevating Luxury Hospitality in the Heart of Dharamshala</title>
  <!-- stylesheet linked here -->
  <link rel="stylesheet" href="style.css">

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
  <title>Hotel Dhauladhar</title>
</head>
<body>

  <!-- navbar -->
    <header class="site-header">

        <!-- TOP INFO -->
        <div class="top-info">
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
  <div class="common-hero about-hero">
    <h1 data-aos="fade-down" data-aos-duration="1200">About Us</h1>
    <div class="hero-links" data-aos="fade-up" data-aos-duration="1200" data-aos-delay="300">
      <a href="index.php">THE RESORT</a>
      <a href="about.php">/ About Us</a>
    </div>
  </div>

 <!-- next ------------------------------------ -->
  <section class="split-section">
    <div class="split-wrapper">

      <!-- LEFT IMAGE -->
      <div class="split-image" data-aos="zoom-in">
        <span class="gold-strip"></span>
        <img src="./images/Copy-of-DSC09939-scaled.jpg" alt="Luxury Room">
      </div>

      <!-- RIGHT CONTENT -->
      <div class="split-content" data-aos="zoom-in-left">
        <span class="tag">About Dhauladhar Heights Resort</span>

        <h2 class="title">
          Elevating Hospitality in the Heart of the Himalayas
        </h2>

        <p class="description">
          Nestled in Dharamshala’s serene tea gardens, Dhauladhar Heights Resort blends nature, luxury, and warm
          hospitality. Spread across 4 acres, it offers breathtaking views of the Dhauladhar range. With 74 elegantly
          designed rooms and suites, the resort provides comfort, sophistication, and tranquility for both leisure and
          business travellers in a peaceful setting.
        </p>


        <p class="description">
          Dhauladhar Heights Resort features the region’s largest banqueting and events facility, ideal for weddings,
          corporate gatherings, and celebrations. Guests enjoy scenic mornings and relaxing evenings with modern
          amenities and spacious venues. Whether for leisure or business, the resort ensures memorable experiences with
          seamless service, refined comfort, and unmatched natural surroundings in Dharamshala.
        </p>


      </div>
    </div>
  </section>


   <!-- --------------------team members------------------- -->
  <div class="teamcontainer">
    <section class="title-section">
      <!-- <div class="bg-logo"></div> -->

      <div class="title-wrapper">
        <span class="line" data-aos="fade-up" data-aos-duration="2000"></span>

        <div class="logo-circle" data-aos="fade-up" data-aos-duration="2000">
          <img src="./images/dhr_logo_icon.png" alt="logo">
        </div>

        <span class="line" data-aos="fade-up" data-aos-duration="2000"></span>
      </div>

      <h2 data-aos="fade-up" data-aos-duration="2000">Message from the General Manager</h2>
      <p class="utilitesp" data-aos="fade-up" data-aos-duration="2000">
     At Dhauladhar Heights Resort, we are committed to offering the finest luxury hospitality in Dharamshala. Every guest experience is thoughtfully crafted with warm service, modern comforts, and personalized care, ensuring a memorable and truly welcoming stay in the heart of Himachal Pradesh.
      </p>
    </section>

      <div class="roomscontainer">
      <section class="room-section rowreverse">
        <div class="room-content" data-aos="fade-up" data-aos-delay="200">
          <h2>Message from the General Manager
          </h2>



          <p class="room-description message">
            "At Dhauladhar Heights Resort, we believe true hospitality lies in creating memorable experiences with
            warmth and elegance.
          </p>
          <p class="room-description message">
            Set amidst the serene tea gardens of Dharamshala and overlooking the majestic Dhauladhar range, our
            resort reflects our philosophy of “Elevating Hospitality in the Heart of the Himalayas.” Every detail—from
            our thoughtfully designed rooms and suites to our grand banqueting spaces—is crafted to offer comfort,
            luxury, and seamless service.
          </p>
          <p class="room-description message">
            Whether you are here for leisure, business, or celebrations, it is our privilege to make your stay truly
            exceptional".
          </p>

          <div class="designation">
            <span class="ownername">Warm Regards,</span>
            <h3 class="post">Lucky Nehria</h3>
            <h4>General Manager</h4>
            <h4>Dhauladhar Heights Resort, Dharamshala (H.P)</h4>
          </div>

        </div>

        <div class="room-image gm-image">
          <div class="image-overlay-text">
            <img src="./images/HotelManager.jpeg" alt="HotelManager" class="HotelManager">
          </div>
        </div>
      </section>

    </div>
  </div>



  <style>
    .read-more-btn {
      color: #5DC5E3;
      font-weight: 600;
      text-decoration: none;
      margin-left: 5px;
      font-size: 13px;
      transition: color 0.3s;
      cursor: pointer;
    }
    .card-box:hover .read-more-btn {
      color: #fff !important;
    }
    .feedback-slider-container {
      max-width: 1090px;
      margin: 60px auto 0;
      overflow: hidden;
      width: 100%;
    }
    .feedback-slider-track {
      display: flex;
      gap: 20px;
      margin: 0;
      max-width: none;
      transition: transform 0.5s ease;
      width: max-content;
    }
    .feedback-slider-track .feedback-card {
      flex: 0 0 350px;
      max-width: 350px;
      min-width: 350px;
    }
    @media (max-width: 1130px) {
      .feedback-slider-container {
        max-width: 720px;
      }
    }
    @media (max-width: 760px) {
      .feedback-slider-container {
        max-width: 350px;
      }
    }
    @media (max-width: 420px) {
      .feedback-slider-track .feedback-card {
        flex-basis: calc(100vw - 40px);
        max-width: calc(100vw - 40px);
        min-width: calc(100vw - 40px);
      }
    }
  </style>

  <!-----------------------------Feedback Section--------------->
  <section class="feedback" style="overflow: hidden;">
    <div class="feedback-header" data-aos="zoom-out-up">
      <div>
        <span class="tag">Guest Experiences </span>
        <h2>What Our Guests Say<br> About Their Stay? </h2>
      </div>
      <div style="display:flex; flex-direction:column; align-items:flex-end; gap: 15px;">
        <div class="nav-btns" style="display: flex; gap: 10px;">
            <button class="feedback-prev rect-btn" style="cursor:pointer; width:40px; height:40px; border:1px solid #ccc; background:#fff; border-radius:4px;"><i class="fa-solid fa-arrow-left"></i></button>
            <button class="feedback-next rect-btn" style="cursor:pointer; width:40px; height:40px; border:1px solid #ccc; background:#fff; border-radius:4px;"><i class="fa-solid fa-arrow-right"></i></button>
        </div>
        <a href="<?php echo htmlspecialchars($googleReviewsUrl); ?>" target="_blank" style="text-decoration:none; color:#5DC5E3; font-weight:600; font-size:15px; border-bottom:1px solid #5DC5E3; padding-bottom:2px;">See all reviews <i class="fa-solid fa-arrow-right" style="font-size:12px;"></i></a>
      </div>
    </div>

    <div class="feedback-slider-container" data-aos="zoom-in">
      <div class="feedback-cards feedback-slider-track">
        <?php 
        $reviews = getTopReviews(6);
        if(empty($reviews)): 
        ?>
            <!-- Fallback Static Cards -->
            <div class="feedback-card">
                <div class="card-box">
                <div class="stars"> 
                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                </div>
                <p>Exceptional Experience at Dhauladhar Heights Resort... highly recommend this property for destination weddings.</p>
                <span class="arrow"></span>
                </div>
                <div class="user">
                <img src="./images/vishal.png">
                <div><h4>Vishal Jamnwal</h4><span>Business ❘ Family</span></div>
                </div>
            </div>
            <div class="feedback-card">
                <div class="card-box">
                <div class="stars"> 
                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                </div>
                <p>The stay was very comfortable, the staff was courteous and responsive. Beautiful location.</p>
                <span class="arrow"></span>
                </div>
                <div class="user">
                <img src="./images/rita.png" alt="rita">
                <div><h4>Rita Dadwal</h4><span>Friends</span></div>
                </div>
            </div>
            <div class="feedback-card">
                <div class="card-box">
                <div class="stars"> 
                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                </div>
                <p>We had a really lovely time here with our group of family friends. The property is well maintained.</p>
                <span class="arrow"></span>
                </div>
                <div class="user">
                <img src="./images/abhishek.png">
                <div><h4>Abhishek</h4><span>Holiday ❘ Friends</span></div>
                </div>
            </div>
        <?php else: ?>
            <!-- Dynamic Reviews from DB -->
            <?php foreach($reviews as $rev): ?>
            <div class="feedback-card">
                <div class="card-box">
                <div class="stars"> 
                    <?php 
                    $rating = intval($rev['rating'] ?? 5);
                    for($i = 1; $i <= 5; $i++) {
                        if($i <= $rating) echo '<i class="fa-solid fa-star" style="color:#cf9a2c;"></i>';
                        else echo '<i class="fa-regular fa-star" style="color:#cf9a2c;"></i>';
                    }
                    ?>
                </div>
                <p class="review-text-container">
                    <?php 
                    $fullText = $rev['review_text'];
                    if (strlen($fullText) > 120): 
                        $truncated = mb_strimwidth($fullText, 0, 115, '...');
                    ?>
                        <span class="review-short"><?php echo nl2br(htmlspecialchars($truncated)); ?></span>
                        <a href="<?php echo htmlspecialchars($googleReviewsUrl); ?>" class="read-more-btn" target="_blank">Read More</a>
                    <?php else: ?>
                        <?php echo nl2br(htmlspecialchars($fullText)); ?>
                    <?php endif; ?>
                </p>
                <span class="arrow"></span>
                </div>
                <div class="user">
                <?php if(!empty($rev['reviewer_image'])): ?>
                    <img src="<?php echo getReviewerImageUrl($rev['reviewer_image']); ?>" alt="Reviewer" style="width:50px;height:50px;border-radius:50%;object-fit:cover;">
                <?php else: ?>
                    <div style="width:50px;height:50px;border-radius:50%;background:#eee;display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-user" style="color:#ccc;"></i></div>
                <?php endif; ?>
                <div>
                    <h4><?php echo htmlspecialchars($rev['reviewer_name']); ?></h4>
                    <span><?php echo htmlspecialchars($rev['guest_type']); ?></span>
                </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </section>
 

  <!--------------------------------- blog section---------------------------- -->
     <section class="blog-section">
        <section class="title-section">
            <div class="title-wrapper">
                <span class="line" data-aos="fade-up" data-aos-duration="3000"></span>
                <div class="logo-circle" data-aos="fade-up" data-aos-duration="3000">
                    <img src="./images/dhr_logo_icon.png" alt="logo">
                </div>
                <span class="line" data-aos="fade-up" data-aos-duration="3000"></span>
            </div>
              <h2 data-aos="fade-up" data-aos-duration="3000">Latest Travel Stories & Resort Experiences in Dharamshala</h2>
            <p class="utilitesp" data-aos="fade-up" data-aos-duration="3000">
               Discover the best of Dharamshala with insights from Dhauladhar Heights Resort. From travel guides and local attractions to luxury stay experiences and hidden gems near the Dhauladhar mountain range, explore everything you need to plan a perfect Himachal getaway. Stay updated with expert tips, seasonal travel ideas, and unforgettable experiences.
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
                    <a href="https://www.linkedin.com/company/dhauladhar-heights-resort/" target="_blank" rel="noopener noreferrer">
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
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
  <script src="script.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const track = document.querySelector('.feedback-slider-track');
      const prevBtn = document.querySelector('.feedback-prev');
      const nextBtn = document.querySelector('.feedback-next');
      
      if(track && prevBtn && nextBtn) {
        let index = 0;
        const cards = track.querySelectorAll('.feedback-card');
        if(cards.length === 0) return;
          
        let autoPlayInterval;

        function startAutoPlay() {
          stopAutoPlay();
          autoPlayInterval = setInterval(() => {
            nextBtn.click();
          }, 5000); // Autoplay every 5 seconds
        }

        function stopAutoPlay() {
          clearInterval(autoPlayInterval);
        }

        function getGap() {
          return parseFloat(window.getComputedStyle(track).gap) || 0;
        }

        function getCardWidthWithGap() {
          return cards[0].getBoundingClientRect().width + getGap();
        }

        function getVisibleCards() {
          const cardWidthWithGap = getCardWidthWithGap();
          const containerWidth = track.parentElement.offsetWidth + getGap();
          return Math.max(1, Math.min(3, Math.floor(containerWidth / cardWidthWithGap)));
        }

        function getMaxIndex() {
          return Math.max(0, cards.length - getVisibleCards());
        }

        function updateSlider() {
          const cardWidthWithGap = getCardWidthWithGap();
          index = Math.min(index, getMaxIndex());
          track.style.transform = `translateX(-${index * cardWidthWithGap}px)`;
        }
        
        nextBtn.addEventListener('click', () => {
          stopAutoPlay(); // Pause autoplay on manual click
          const visibleCards = getVisibleCards();
          const maxIndex = getMaxIndex();
          
          if(index < maxIndex) {
            index = Math.min(index + visibleCards, maxIndex);
            updateSlider();
          } else {
            index = 0;
            updateSlider();
          }
          startAutoPlay(); // Resume autoplay
        });
        
        prevBtn.addEventListener('click', () => {
          stopAutoPlay();
          const visibleCards = getVisibleCards();
          const maxIndex = getMaxIndex();

          if(index > 0) {
            index = Math.max(index - visibleCards, 0);
            updateSlider();
          } else {
            index = maxIndex;
            updateSlider();
          }
          startAutoPlay();
        });

        // Pause autoplay when hovering over the slider container
        track.parentElement.addEventListener('mouseenter', stopAutoPlay);
        track.parentElement.addEventListener('mouseleave', startAutoPlay);
        window.addEventListener('resize', updateSlider);

        // Start autoplay initially
        updateSlider();
        startAutoPlay();
      }
    });

    function toggleReviewText(btn) {
      const parent = btn.parentElement;
      const shortSpan = parent.querySelector('.review-short');
      const fullSpan = parent.querySelector('.review-full');
      
      if (fullSpan.style.display === 'none') {
        fullSpan.style.display = 'inline';
        shortSpan.style.display = 'none';
        btn.textContent = 'Read Less';
      } else {
        fullSpan.style.display = 'none';
        shortSpan.style.display = 'inline';
        btn.textContent = 'Read More';
      }
    }
  </script>
</body>

</html>
