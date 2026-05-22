<?php
require_once __DIR__ . '/includes/functions.php';

// Get latest 3 blogs for homepage
$latestBlogs = getRecentBlogs(3);
$popularBlogs = getPopularBlogs(3);
$categories = getCategories();

// Get base URL for assets
$baseUrl = getBaseUrl();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- stylesheet linked here -->
    <link rel="stylesheet" href="./style.css">

    <!-- google fonts linked here -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Agu+Display:MORF@0..60&family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Edu+AU+VIC+WA+NT+Arrows:wght@400..700&family=Inconsolata:wght@200..900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Oswald:wght@200..700&family=Outfit:wght@100..900&family=Parkinsans:wght@300..800&family=Quicksand:wght@300..700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">

    <!-- cdn linked here -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Dhauladhar Heights Resort | Best Luxury Resort in Dharamshala Himachal Pradesh</title>


    <!-- AOS CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    
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

    <!-- hero slider -->
    <section class="luxury-slider" data-aos="fade-up" data-aos-duration="1000" data-aos-easing="ease-in-out">


        <div class="slider-track">
            <div class="slider-item is-active" style="background-image:url('./images/heromainslide.jpg')">
                <div class="slider-overlay"></div>
                <div class="slider-content">
                    <p>Luxury mountain retreat surrounded by the beauty of Dharamshala</p>
                    <h1>Dhauladhar Heights Resort – Premium Stay in Dharamshala</h1>
                </div>
            </div>
            <div class="slider-item" style="background-image:url('./images/heromainslide2.jpg')">
                <div class="slider-overlay"></div>
                <div class="slider-content">
                    <p>Wake up to breathtaking Himalayan views and peaceful nature</p>
                    <h1>Experience Comfort & Scenic Beauty at Dhauladhar Heights Resort</h1>

                </div>
            </div>
            <div class="slider-item" style="background-image:url('./images/DJI_0158-1-scaled3.jpg')">
                <div class="slider-overlay"></div>
                <div class="slider-content">
                    <p>Elegant rooms, modern amenities, and unforgettable mountain hospitality</p>
                    <h1>Dhauladhar Heights Resort – Luxury Resort in Dharamshala</h1>

                </div>
            </div>
        </div>

        <!-- <div class="slider-controls">
            <button class="slider-btn hero-prev-btn">&#10094;</button> <button
                class="slider-btn hero-next-btn">&#10095;</button>
        </div> -->
    </section>


    <!-- ------------------Avaliablity section------------------- -->
    <section class="availability">
        <div class="availability-box" data-aos="zoom-in">
            <!-- Check In -->
            <div class="field">
                <label>Check In</label>
                <input type="date" id="checkIn">
            </div>

            <!-- Check Out -->
            <div class="field">
                <label>Check Out</label>
                <input type="date" id="checkOut">
            </div>



            <!-- <button type="button" class="hero-btn cta-btn" id="bookBtn">Book Now</button> -->
            <button type="button" class="hero-btn cta-btn rect-btn disabled-btn" id="bookBtn">
                Book Now
            </button>



        </div>
    </section>

    <!---------------------rooms section------------------ -->
    <div class="common-container">
        <section class="rooms-section">
            <section class="title-section">
                <!-- <div class="bg-logo"></div> -->

                <div class="title-wrapper">
                    <span class="line" data-aos="fade-up" data-aos-duration="2000"></span>

                    <div class="logo-circle" data-aos="fade-up" data-aos-duration="2000">
                        <img src="./images/dhr_logo_icon.png" alt="logo">
                    </div>

                    <span class="line" data-aos="fade-up" data-aos-duration="2000"></span>
                </div>

                <h2 data-aos="fade-up" data-aos-duration="3000">Our Luxurious Rooms</h2>
                <p class="utilitesp" data-aos="fade-up" data-aos-duration="2000">
                    Discover luxury at Dhauladhar Heights Resort, a premium mountain retreat in Dharamshala offering elegant stays, panoramic Himalayan views, world-class hospitality, and unforgettable experiences for couples, families, and luxury travelers in Himachal Pradesh.
                </p>
            </section>

            <!-- ----------------rooms card------------------- -->
            <div class="roomCardGrid">

             <!--Presidential Suite CARD 1 -->
                <div class="roomCard" data-aos="fade-left" data-aos-delay="100">
                    <div class="roomCardImage">
                        <img src="./images/presidentialmain.jpg" alt="Presidential Suite">

                        <a href="room-details.html?room=presidential-suite" class="roomViewBtn">
                            View Details <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </a>

                    </div>
                    <div class="roomCardBody">
                        <h2 class="roomTitle">Presidential Suite</h2>
                        <p class="roomSize">Experience the finest luxury in Dharamshala with our Presidential Suite...</p>
                    </div>
        
                </div>

                     <!-- Executive Suite CARD 2 -->
                <div class="roomCard" data-aos="zoom-in" data-aos-delay="100">
                    <div class="roomCardImage">
                        <img src="./images/executivesuite.jpg">
                        <a href="room-details.html?room=executive-suite" class="roomViewBtn">
                            View Details <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </a>

                    </div>
                    <div class="roomCardBody">
                        <h2 class="roomTitle">Executive Suite</h2>
                        <p class="roomSize">Designed for those who prefer extra space and luxury, the Executive Suite...</p>
                    </div>
                   
                </div>

                <!-- CARD 3 -->
                <div class="roomCard" data-aos="fade-right" data-aos-delay="100">
                    <div class="roomCardImage">
                        <img src="./images/DSC00963.jpg" alt="Executive Room">
                        <a href="room-details.html?room=executive-room" class="roomViewBtn">
                            View Details <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="roomCardBody">
                        <h2 class="roomTitle">Executive Room</h2>
                        <p class="roomSize">A perfect blend of comfort and functionality, our Executive Rooms ....</p>
                    </div>
               
                </div>

           
               

            </div>

        </section>
    </div>

    <!-- next ------------------------------------ -->
    <div class="common-container">
        <section class="split-section" data-aos="fade-up">
            <div class="split-wrapper">

                <!-- LEFT IMAGE -->
                <div class="split-image">
                    <span class="gold-strip"></span>
                    <img src="./images/DSC00213-scaled.jpg" alt="Luxury Room">
                </div>

                <!-- RIGHT CONTENT -->
                <div class="split-content">
                    <span class="tag">Experience Luxury & Serenity at Dhauladhar Heights Resort</span>

                    <h2 class="title">
                        Best Luxury Resort in Dharamshala<br>

                    </h2>

                    <p class="description">
                      Discover timeless luxury at Dhauladhar Heights Resort, a premier mountain retreat surrounded by the breathtaking Dhauladhar Hills. Renowned for its elegant accommodations, panoramic valley views, exceptional hospitality, and world-class comfort, the resort offers an unforgettable stay experience for couples, families, and luxury travelers seeking the finest resort in Dharamshala and Himachal Pradesh.
                    </p>    

                    <div class="stats">
                        <div class="stat-item">
                            <h3 id="counter">74+</h3>
                            <p>Premium Rooms & Suites</p>
                        </div>

                    </div>

                    <button class="hero-btn"> <a href="about.php">MORE ABOUT</a></button>
                </div>
            </div>
        </section>
    </div>

    <!----------------------------Hotel facility-------------------------  -->
    <div class="hotel-Utilities" data-aos="fade-up">
        <section class="title-section">

            <div class="title-wrapper">
                <span class="line" data-aos="fade-up" data-aos-duration="2000"></span>

                <div class="logo-circle" data-aos="fade-up" data-aos-duration="2000">
                    <img src="./images/dhr_logo_icon.png" alt="logo">
                </div>

                <span class="line" data-aos="fade-up" data-aos-duration="2000"></span>
            </div>

            <h2 data-aos="fade-up" data-aos-duration="2000">Premium Amenities at Dhauladhar Heights Resort</h2>
            <p class="utilitesp" data-aos="fade-up" data-aos-duration="2000">
                Enjoy world-class amenities at Dhauladhar Heights Resort including luxury rooms, high-speed Wi-Fi, swimming pool, landscaped gardens, fine dining, event spaces, secure parking, and personalized hospitality — everything you need for a memorable luxury stay in Dharamshala.
            </p>
        </section>

        <section class="amenities-section">

            <div class="amenities-grid">

                <div class="amenity-box">
                    <i class="fa-solid fa-wifi"></i>
                    <h3>Free Wi-Fi</h3>
                    <p>Seamless high-speed internet throughout rooms and public spaces.</p>
                </div>

                <div class="amenity-box">
                    <i class="fa-solid fa-tree"></i>
                    <h3>Garden & Lawn</h3>
                    <p>Peaceful green spaces perfect for relaxation and evening walks.</p>
                </div>

                <div class="amenity-box">
                    <i class="fa-solid fa-person-swimming"></i>
                    <h3>Swimming Pool</h3>
                    <p>Refresh yourself in our scenic pool with mountain views.</p>
                </div>

                <div class="amenity-box">
                    <i class="fa-solid fa-square-parking"></i>
                    <h3>Free Parking</h3>
                    <p>Secure and spacious parking area for all our valued guests.</p>
                </div>

            </div>
        </section>
    </div>


    <!----------------------  luxury hotel and room ------------------- -->
    <section class="split-section two" data-aos="fade-up" data-aos-duration="3000">
        <div class="split-wrapper">

            <!-- LEFT IMAGE -->
            <div class="split-image">
                <span class="gold-strip"></span>
                <img src="./images/DSC00173-Edit-scaled.jpg" alt="Luxury Room">
            </div>

            <!-- RIGHT CONTENT -->
            <div class="split-content">
                <span class="tag">Luxury Stay in Dharamshala</span>

                <h2 class="title">
                    Dhauladhar Heights Resort – Himalayan Luxury Redefined
                </h2>

                <p class="description">
                   Perfect for family vacations, romantic escapes, destination weddings, and corporate retreats, Dhauladhar Heights Resort offers spacious luxury rooms, fine dining experiences, scenic event venues, and exceptional service in the heart of Himachal Pradesh.
                </p>
                <p class="description">Located close to Dharamshala’s top attractions, Dhauladhar Heights Resort
                    combines scenic beauty with convenience, making it an ideal choice for couples, families, and
                    leisure travelers seeking a refined stay in the Himalayas.</p>

                <div class="stats">
                    <div class="stat-item">
                    </div>

                    <div class="stat-item">
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Faculty Card -->
    <div class="facilitytitle" data-aos="zoom-out-right">
        <div>
            <span class="tag">facilities</span>
            <h2>Enjoy Complete & Best Quality facilities</h2>
        </div>

    </div>
    <div class="facility-card" data-aos="fade-right" data-aos-offset="300" data-aos-easing="ease-in-sine">
        <div class="facility-image">
            <img src="./images/DSC00681-HDR-Edit-scaled.jpg" alt="Gym Training Grounds">
        </div>
        <div class="facility-content">
            <span class="facility-number">01</span>
            <h4 class="tag">Dining</h4>
            <h2 class="facility-title">Luxury Multi-Cuisine Dining Experience</h2>
            <p class="facility-desc">
                Enjoy a delightful dining experience at our in-house restaurant, serving carefully curated local and
                multi-cuisine dishes. Fresh ingredients, hygienic preparation, and warm hospitality define every meal.
            </p>

        </div>
    </div>

    <!-- card -2 -->
    <div class="facility-card rowreverse" data-aos="fade-left" data-aos-offset="300" data-aos-easing="ease-in-sine">
        <div class="facility-image">
            <img src="./images/DJI_0175-scaled.jpg" alt="Gym Training Grounds">
        </div>
        <div class="facility-content">
            <span class="facility-number">02</span>
            <h4 class="tag">Wellness</h4>
            <h2 class="facility-title"> Infinity Style Swimming Pool with Mountain Views</h2>
            <p class="facility-desc">
                Relax and refresh in our beautifully designed swimming pool, offering a peaceful atmosphere with scenic
                surroundings. Perfect for leisure swims, family time, and unwinding after a long day of travel.
            </p>

        </div>
    </div>
    <!-- card -3 -->
    <div class="facility-card" data-aos="fade-left" data-aos-offset="300" data-aos-easing="ease-in-sine">
        <div class="facility-image">
            <img src="./images/deluxemain1.jpg" alt="Gym Training Grounds">
        </div>
        <div class="facility-content">
            <span class="facility-number">03</span>
            <h4 class="tag">Experience
            </h4>
            <h2 class="facility-title">Luxury Hospitality & Personalized Guest Services</h2>
            <p class="facility-desc">
                Experience thoughtful hospitality with premium services focused on relaxation and convenience. From
                serene surroundings to personalized care, every detail is designed to enhance your stay.
            </p>

        </div>
    </div>

       <!-- card -4 -->
    <!-- card -4 -->
<div class="facility-card rowreverse" data-aos="fade-left" data-aos-offset="300" data-aos-easing="ease-in-sine">
    <div class="facility-image">
        <img src="./images/Kidszone.png" alt="Kids Zone at Dhauladhar Heights Resort Dharamshala">
    </div>

    <div class="facility-content">
        <span class="facility-number">04</span>

        <h4 class="tag">Family Entertainment</h4>

        <h2 class="facility-title">Fun-Filled Kids Zone</h2>

        <p class="facility-desc">
           Enjoy a family-friendly vacation at Dhauladhar Heights Resort with our dedicated Kids Zone designed for fun, creativity, and entertainment. A safe and engaging play area where children can enjoy memorable moments while parents relax amidst the peaceful beauty of Dharamshala.
        </p>
    </div>
</div>
    <!-- DYNAMIC BLOG SECTION -->
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
                            <span>August 10, 2026</span>
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
        
        <div class="view-all-btn">
            <!-- <a href="blog.php" class="hero-btn">View All Blogs <i class="fas fa-arrow-right"></i></a> -->
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <h2>Frequently Asked Questions</h2>
        <div class="faq-item">
            <button class="faq-question">Where is Dharamshala Heights & Resort located?<span>+</span></button>
            <div class="faq-answer">
                <p>Dharamshala Heights & Resort is located in Dharamshala, Himachal Pradesh, surrounded by scenic mountain views.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question">Is the resort near McLeod Ganj?<span>+</span></button>
            <div class="faq-answer">
                <p>Yes, the resort is conveniently located near McLeod Ganj and major tourist attractions.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question">Is Dharamshala Heights & Resort family friendly?<span>+</span></button>
            <div class="faq-answer">
                <p>Yes, the resort is suitable for families, couples, and group stays.</p>
            </div>
        </div>
    </section>

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
 
     <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="script.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
        
    </script>
</body>
</html>
