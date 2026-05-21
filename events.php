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
                    <img src="/images/dhr_logo_full_white_720.png" alt="Logo">
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
    <!-- <div class="overlay-left">
    <p><strong>Dhauladhar Heights Resort</strong></p>
    <p>Tea Garden Road, Kunal Pathri,<br>Dharamshala, Himachal Pradesh 176217</p>
    <p><i class="fa-solid fa-phone"></i> +91 80915-08200</p>
    <p><i class="fa-solid fa-envelope"></i> reservation@dhauladharheightsresort.com</p>
  </div> -->

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

      <!-- <a href="#" class="overlay-book">BOOK YOUR STAY</a> -->
    </div>
  </div>


 <!----------------------- hero section ---------------------->
  <div class="mainherobanner">

    <!-- ITEM 1 -->
    <div class="banneritem activebanner" style="background-image:url('./images/DSC02153\ \(1\).jpg')">

      <div class="banneroverlay"></div>

      <div class="bannercontent common-hero">
        <h1>Corporate Events & Conferences</h1>
        <div class="hero-links" data-aos="fade-up" data-aos-duration="1200" data-aos-delay="300">
          <a href="/index.html">THE RESORT</a>
          <a href="/events.html">/ Events</a>
        </div>
      </div>

    </div>


    <!-- ITEM 2 -->
    <div class="banneritem" style="background-image:url('./images/DSC01910.JPG')">

      <div class="banneroverlay"></div>

      <div class="bannercontent common-hero">
        <h1>Private Parties & Celebrations</h1>
        <div class="hero-links" data-aos="fade-up" data-aos-duration="1200" data-aos-delay="300">
          <a href="/index.html">THE RESORT</a>
          <a href="/events.html">/ Events</a>
        </div>
      </div>

    </div>


    <!-- ITEM 3 -->
    <div class="banneritem" style="background-image:url('./images/IMG_1013\ \(2\).JPG')">

      <div class="banneroverlay "></div>

      <div class="bannercontent common-hero">
        <h1>Meet, Work & Inspire</h1>
        <div class="hero-links" data-aos="fade-up" data-aos-duration="1200" data-aos-delay="300">
          <a href="/index.html">THE RESORT</a>
          <a href="/events.html">/ Events</a>
        </div>
      </div>

    </div>


    <!-- CONTROLS -->
    <div class="bannercontrols">

      <div class="bannercontrol activecontrol"></div>
      <div class="bannercontrol"></div>
      <div class="bannercontrol"></div>

    </div>

  </div>


  <!-- ------------------ -->
  <section class="title-section">
    <!-- <div class="bg-logo"></div> -->

    <div class="title-wrapper">
      <span class="line" data-aos="fade-up" data-aos-duration="2000"></span>

      <div class="logo-circle" data-aos="fade-up" data-aos-duration="2000">
               <img src="./images/dhr_logo_icon.png" alt="logo">
      </div>

      <span class="line" data-aos="fade-up" data-aos-duration="2000"></span>
    </div>

    <h2 data-aos="fade-up" data-aos-duration="2000">Conferences & Events Venues</h2>
    <p class="utilitesp" data-aos="fade-up" data-aos-duration="2000">
      Host exceptional gatherings with Dhauladhar Heights Resort, a premier meetings and events venue in Dharamshala.
      Nestled amidst the scenic beauty of the Dhauladhar mountains, our resort offers elegant event spaces, modern
      conference facilities, luxury accommodation, curated catering, and personalized service for seamless experiences.
    </p>
  </section>

  <div class="facility-card" data-aos="fade-right" data-aos-offset="300" data-aos-easing="ease-in-sine">
    <div class="facility-image">
      <img src="./Images/WhatsApp-Image-2026-02-19-at-11.34.58-AM-3.jpeg" alt="events">
    </div>
    <div class="facility-content">
      <h4 class="tag">Book Your Event With Us</h4>

      <p class="facility-desc">
        Whether you are planning a corporate meeting in Dharamshala, business conference, team retreat, destination
        celebration, birthday party, or private social event, we provide the perfect setting for every occasion.
      </p>

      <p class="facility-desc">Recognized as one of the finest event venues in Dharamshala, Himachal Pradesh, Dhauladhar
        Heights Resort combines natural surroundings with premium hospitality to make every meeting productive and every
        event unforgettable. From executive board meetings and corporate offsites to family celebrations and private
        events, we create flawless experiences in one of the most beautiful destinations in Himachal Pradesh. Choose
        Dhauladhar Heights Resort for premium meetings and events in Dharamshala. With elegant venues, conference
        facilities, luxury rooms, catering, and mountain views, we are the ideal choice for corporate events,
        conferences, retreats, and private celebrations in Himachal Pradesh.</p>
      <a href="./contact.html" class="eventbtn discover-button rect-btn">
        Book Now <i class="fas fa-arrow-right"></i>
      </a>
    </div>
  </div>

<!--Luxury and Excellence  -->
  <section class="title-section">
    <div class="title-wrapper">
      <span class="line" data-aos="fade-up" data-aos-duration="2000"></span>

      <div class="logo-circle" data-aos="fade-up" data-aos-duration="2000">
        <img src="./images/dhr_logo_icon.png" alt="logo">
      </div>

      <span class="line" data-aos="fade-up" data-aos-duration="2000"></span>
    </div>

    <h2 data-aos="fade-up" data-aos-duration="2000">Our Commitment</h2>
    <p class="utilitesp" data-aos="fade-up" data-aos-duration="2000">
      At Hotel Dh, our commitment to innovation and personalised elegance is unwavering. As our experts craft your
      meetings
    </p>
  </section>

  <!--------------------------------- events slider -------------------------------->
   <!--------------------------------- events slider -------------------------------->
  <section class="event-slider" data-aos="fade-up" data-aos-duration="2000">

    <!-- DESKTOP ARROWS -->
    <button class="event-arrow event-prev desktop-control"><i class="fa-solid fa-arrow-left"></i></button>

    <div class="event-slider-viewport">
      <div class="event-slider-track">

        <div class="event-slide">
          <img src="./images/planevents-scaled.jpg" alt="event-1">
         <h4>Customized Celebration Experiences</h4>
        <p>Celebrate birthdays, anniversaries, receptions, and private gatherings with personalized themes and luxury arrangements.</p>
        </div>

        <div class="event-slide">
          <img src="./images/IMG_1057 (2).JPG" alt="event-2">
         <h4>Customized Celebration Experiences</h4>
        <p>Celebrate birthdays, anniversaries, receptions, and private gatherings with personalized themes and luxury arrangements.</p>
        </div>

        <div class="event-slide">
          <img src="./images/DSC02153 (1).jpg" alt="event-3">
         <h4>Award Ceremony Celebrations</h4>
         <p>Host prestigious award nights and recognition ceremonies with sophisticated event styling and seamless management.</p>
        </div>

        <div class="event-slide">
          <img src="./images/WhatsApp-Image-2026-02-19-at-11.34.58-AM-2.jpeg" alt="event-4">
           <h4>Conference Seating Arrangements</h4>
          <p>Elegant and professionally planned seating layouts designed for corporate meetings, seminars, and business conferences.</p>
        </div>

        <div class="event-slide">
          <img src="./images/IMG_9768 (1).jpg" alt="event-5">
         <h4>Conference</h4>
<p>Host professional conferences and corporate gatherings with modern event spaces, seamless arrangements, advanced facilities, and a refined atmosphere.</p>
        </div>

        <div class="event-slide">
          <img src="./images/IMG_1013 (2).JPG" alt="event-5">
            <h4>Corporate Event Management</h4>
        <p>From product launches to executive gatherings, experience flawless corporate event planning and premium hospitality.</p>
        </div>

      </div>
    </div>

    <button class="event-arrow event-next desktop-control">
<i class="fa-solid fa-arrow-right"></i>
    </button>

    <!-- MOBILE CONTROLS -->
    <div class="event-mobile-controls">
      <button class="event-arrow event-prev"><i class="fa-solid fa-arrow-left"></i></button>
      <button class="event-arrow event-next"><i class="fa-solid fa-arrow-right"></i></button>
    </div>

  </section>




  <!-- --------Meeting and Confrences---------- -->
  <section class="title-section">
    <!-- <div class="bg-logo"></div> -->

    <div class="title-wrapper">
      <span class="line" data-aos="fade-up" data-aos-duration="2000"></span>

      <div class="logo-circle" data-aos="fade-up" data-aos-duration="2000">
        <img src="./images/dhr_logo_icon.png" alt="logo">
      </div>

      <span class="line" data-aos="fade-up" data-aos-duration="2000"></span>
    </div>

    <h2 data-aos="fade-up" data-aos-duration="2000">Meeting And Conference Venues</h2>
    <p class="utilitesp" data-aos="fade-up" data-aos-duration="2000">
      Step into a realm of remarkable possibilities and explore a curated selection of versatile meeting and conference
      venues. From client conclaves, strategic think tank meets and grand conferences, discover spaces that offer a
      harmon..
    </p>
  </section>

 
  <!-- update code by sahil -->
  <section class="venue-section">

    <!-- VENUE IMAGES DISPLAY -->
    <div class="venue-images" data-aos="fade-up" data-aos-delay="100">
      <div class="venue-image-card" data-event="wedding corporate" data-capacity="600">
        <img src="./images/WhatsApp-Image-2026-02-19-at-11.34.58-AM-3.jpeg" alt="Amaltas Hall">
        <div class="venue-info">
          <h2>AMALTAS HALL</h2>
          <p class="specs">Indoor | 20000 Sq. Ft | Capacity: 600 guests</p>
          <p class="desc">Grand indoor hall perfect for weddings and large corporate events</p>
          <button class="enquire-btn rect-btn"><a href="contact.html">Enquire Now</a></button>
        </div>
      </div>

      <div class="venue-image-card" data-event="corporate conference" data-capacity="200">
        <img src="./images/WhatsApp-Image-2026-02-19-at-11.34.59-AM-5.jpeg" alt="Gulmohar Hall">
        <div class="venue-info">
          <h2>GULMOHAR HALL</h2>
          <p class="specs">Indoor | 8000 Sq. Ft | Capacity: 200 guests</p>
          <p class="desc">Versatile hall for conferences and corporate events</p>
          <button class="enquire-btn rect-btn"><a href="contact.html">Enquire Now</a></button>
        </div>
      </div>

      <div class="venue-image-card" data-event="wedding corporate" data-capacity="150">
        <img src="./images/WhatsApp-Image-2026-02-19-at-11.35.00-AM-2.jpeg" alt="Nargis Hall">
        <div class="venue-info">
          <h2>NARGIS HALL</h2>
          <p class="specs">Indoor | 7000 Sq. Ft | Capacity: 150 guests</p>
          <p class="desc">Elegant hall for medium-sized weddings and corporate gatherings</p>
          <button class="enquire-btn rect-btn"><a href="contact.html">Enquire Now</a></button>
        </div>
      </div>

      <div class="venue-image-card" data-event="corporate conference" data-capacity="40">
        <img src="./images/WhatsApp-Image-2026-02-19-at-11.34.58-AM-2.jpeg" alt="Meeting Room">
        <div class="venue-info">
          <h2>MEETING ROOM</h2>
          <p class="specs">Indoor | 600 Sq. Ft | Capacity: 40 guests</p>
          <p class="desc">Professional space for meetings and small conferences</p>
          <button class="enquire-btn rect-btn"><a href="contact.html">Enquire Now</a></button>
        </div>
      </div>

      <div class="venue-image-card" data-event="corporate" data-capacity="10">
        <img src="./images/WhatsApp-Image-2026-02-19-at-11.35.00-AM-3.jpeg" alt="Board Room">
        <div class="venue-info">
          <h2>BOARD ROOM</h2>
          <p class="specs">Indoor | 200 Sq. Ft | Capacity: 10 guests</p>
          <p class="desc">Executive boardroom for high-level meetings</p>
          <button class="enquire-btn rect-btn"><a href="contact.html">Enquire Now</a></button>
        </div>
      </div>

      <!-- Outdoor Venues -->
      <div class="venue-image-card" data-event="wedding" data-capacity="200">
        <img src="./images/DSC00214-scaled.jpg" alt="Garden Green Lawn 1">
        <div class="venue-info">
          <h2>GARDEN GREEN LAWN 1</h2>
          <p class="specs">Outdoor | 6000 Sq. Ft | Capacity: 200 guests</p>
          <p class="desc">Beautiful outdoor lawn perfect for garden weddings</p>
          <button class="enquire-btn rect-btn"><a href="contact.html">Enquire Now</a></button>
        </div>
      </div>
    </div>
  </section>

  <!-- FACT SHEET SECTION -->
  <div class="roomscontainer">
    <section class="room-section rowreverse factsheetsection">
      <div class="room-content" data-aos="fade-up" data-aos-delay="200">
        <h2 class="room-heading"> Dhauladhar Heights Resort – Luxury Hotel in Dharamshala</h2>

        <p class="room-description">
          Discover the complete experience of Dhauladhar Heights Resort, a luxury hotel in Dharamshala offering
          breathtaking views of the Himalayan ranges, elegant accommodations, and world-class hospitality. Designed for
          travelers seeking comfort, serenity, and unforgettable moments, our resort blends nature with modern luxury.
        </p>

        <p class="room-description">
          Download our detailed fact sheet to explore everything about our resort — including room categories,
          amenities, wedding venues, dining options, and exclusive services. Whether you're planning a destination
          wedding, corporate retreat, or relaxing vacation in Dharamshala, this guide provides all the essential
          information you need to plan your stay with confidence.
        </p>

        <a href="#" class="discover-button rect-btn">
          Download Fact Sheet <i class="fas fa-arrow-right"></i>
        </a>
      </div>

      <div class="room-image">
        <div class="image-overlay-text">
          <img src="./images/DJI_0158-1-scaled3.jpg" alt="Dhauladhar Heights Resort">
        </div>
      </div>
    </section>

  </div>
<!-- FAQ SECTION -->
<section class="faq-section">
  <h2>Event Venue – FAQs</h2>

  <!-- FAQ 1 -->
  <div class="faq-item">
    <button class="faq-question">
      Does Dharamshala Heights Resort host destination weddings?
      <span>+</span>
    </button>

    <div class="faq-answer">
      <p>
        Yes, Dharamshala Heights Resort is an ideal venue for destination weddings,
        pre-wedding celebrations, receptions, engagements, and private gatherings
        with breathtaking mountain views and elegant event spaces.
      </p>
    </div>
  </div>

  <!-- FAQ 2 -->
  <div class="faq-item">
    <button class="faq-question">
      What types of events can be organized at the resort?
      <span>+</span>
    </button>

    <div class="faq-answer">
      <p>
        We host weddings, corporate events, birthday celebrations, anniversary parties,
        cocktail evenings, family functions, social gatherings, and luxury private events.
      </p>
    </div>
  </div>

  <!-- FAQ 3 -->
  <div class="faq-item">
    <button class="faq-question">
      What is the guest capacity for events?
      <span>+</span>
    </button>

    <div class="faq-answer">
      <p>
        Our event spaces can comfortably accommodate both intimate celebrations and
        large-scale gatherings with customized seating and décor arrangements.
      </p>
    </div>
  </div>

  <!-- FAQ 4 -->
  <div class="faq-item">
    <button class="faq-question">
      Does the resort provide catering services?
      <span>+</span>
    </button>

    <div class="faq-answer">
      <p>
        Yes, we offer premium catering services with customized vegetarian,
        non-vegetarian, local Himachali, Indian, and multi-cuisine menus tailored
        to your event requirements.
      </p>
    </div>
  </div>

  <!-- FAQ 5 -->
  <div class="faq-item">
    <button class="faq-question">
      Are decoration and event planning services available?
      <span>+</span>
    </button>

    <div class="faq-answer">
      <p>
        Yes, our team can assist with elegant décor setups, floral arrangements,
        stage design, lighting, entertainment, and complete event planning services.
      </p>
    </div>
  </div>

  <!-- FAQ 6 -->
  <div class="faq-item">
    <button class="faq-question">
      Is accommodation available for wedding guests and event attendees?
      <span>+</span>
    </button>

    <div class="faq-answer">
      <p>
        Absolutely. The resort offers luxury rooms and suites for guests,
        ensuring a comfortable and memorable stay during weddings and events.
      </p>
    </div>
  </div>

  <!-- FAQ 7 -->
  <div class="faq-item">
    <button class="faq-question">
      Does the resort offer outdoor event spaces?
      <span>+</span>
    </button>

    <div class="faq-answer">
      <p>
        Yes, guests can enjoy beautiful outdoor venues with panoramic views of
        the Dhauladhar mountains, perfect for weddings, receptions, and evening celebrations.
      </p>
    </div>
  </div>

  <!-- FAQ 8 -->
  <div class="faq-item">
    <button class="faq-question">
      How can I book the venue for an event?
      <span>+</span>
    </button>

    <div class="faq-answer">
      <p>
        You can contact our hospitality team directly through phone, email,
        or the website inquiry form to check availability and customize your event package.
      </p>
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

  <!-- AOS JS  -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script src="script.js"></script>
</body>

</html>