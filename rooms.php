<?php
require_once __DIR__ . '/includes/functions.php';
$baseUrl = getBaseUrl();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Luxury Rooms & Suites in Dharamshala | Dhauladhar Heights Resort</title>
  
  <!-- stylesheet linked here -->
  <link rel="stylesheet" href="<?php echo $baseUrl; ?>/style.css">

  <!-- google fonts linked here -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Agu+Display:MORF@0..60&family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Edu+AU+VIC+WA+NT+Arrows:wght@400..700&family=Inconsolata:wght@200..900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Oswald:wght@200..700&family=Outfit:wght@100..900&family=Parkinsans:wght@300..800&family=Quicksand:wght@300..700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
    rel="stylesheet">

  <!-- cdn linked here -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- AOS CSS -->
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
</head>

<body>

<?php include __DIR__ . '/includes/header.php'; ?>

  <!----------------------- hero section ---------------------->
  <div class="common-hero roomshero">
    <h1 data-aos="fade-down" data-aos-duration="1200">Rooms & Suites</h1>
    <div class="hero-links" data-aos="fade-up" data-aos-duration="1200" data-aos-delay="300">
      <a href="<?php echo $baseUrl; ?>/">THE RESORT</a>
      <a href="<?php echo $baseUrl; ?>/rooms">/ Rooms</a>
    </div>
  </div>

  <!-- ------------------Availability section------------------- -->
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

            <a href="https://asiatech.in/booking_engine/index3?token=NTc3MQ==" 
               class="hero-btn cta-btn rect-btn disabled-btn" 
               id="bookBtn" target="_blank">
               Book Now
            </a>
        </div>
    </section>

  <section class="title-section">
            <div class="title-wrapper">
                <span class="line" data-aos="fade-up" data-aos-duration="3000"></span>
                <div class="logo-circle" data-aos="fade-up" data-aos-duration="3000">
               <img src="<?php echo $baseUrl; ?>/images/dhr_logo_icon.png" alt="logo">
                </div>
                <span class="line" data-aos="fade-up" data-aos-duration="3000"></span>
            </div>

            <h2 data-aos="fade-up" data-aos-duration="3000">Luxury Rooms with Breathtaking <br> Mountain Views</h2>
            <p class="utilitesp" data-aos="fade-up" data-aos-duration="3000">
               Experience comfort and elegance in beautifully appointed rooms and suites at Dhauladhar Heights Resort, offering panoramic views of the majestic Dhauladhar mountain range, modern amenities including high‑speed Wi‑Fi, smart TV, minibar and private balconies, plus thoughtful touches for a relaxing hill‑station stay in Dharamshala.
            </p>
  </section>

<!-- room card 1 -->
 <div class="roomscontainer">
        <section class="room-section">
            <div class="room-content" data-aos="fade-up" data-aos-delay="200">
                <h2 class="room-heading">Executive Room with Modern Comforts</h2>
                
                <ul class="room-features">
                    <li>40 ROOMS</li>
                </ul>
                
                <p class="room-description">
                  A perfect blend of comfort and functionality, our Executive Rooms are ideal for business and leisure travelers. Enjoy modern interiors, premium amenities, and a relaxing atmosphere after a day exploring Dharamshala.
                </p>
                
                <a href="<?php echo getRoomUrl('executive-room'); ?>" class="discover-button rect-btn">
                    Discover More <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="room-image">
                <div class="image-overlay-text">
                    <img src="<?php echo $baseUrl; ?>/images/Executive room .jpg" alt="Executive Room">
                </div>
            </div>
        </section>
    </div>

<!-- room card 2 -->
 <div class="roomscontainer">
        <section class="room-section rowreverse">
            <div class="room-content" data-aos="fade-up" data-aos-delay="200">
                <h2 class="room-heading">Executive Suite with Spacious Living Area</h2>
                
                <ul class="room-features">
                    <li>24 ROOMS</li>
                </ul>
                
                <p class="room-description">
                  Designed for those who prefer extra space and luxury, the Executive Suite offers a separate living area, elegant décor, and scenic views, making your stay both comfortable and memorable.
                </p>
                
                <a href="<?php echo getRoomUrl('executive-suite'); ?>" class="discover-button rect-btn">
                    Discover More <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="room-image">
                <div class="image-overlay-text">
                    <img src="<?php echo $baseUrl; ?>/images/executivesuite.jpg" alt="Executive Suite">
                </div>
            </div>
        </section>
    </div>

    <!-- room card 3 -->
 <div class="roomscontainer">
        <section class="room-section">
            <div class="room-content" data-aos="fade-up" data-aos-delay="200">
                <h2 class="room-heading">Presidential Suite Offering Ultimate Luxury Stay</h2>
                
                <ul class="room-features">
                    <li>2 ROOMS </li>
                </ul>
                
                <p class="room-description">
                  Experience the finest luxury in Dharamshala with our Presidential Suite. Featuring spacious living areas, premium furnishings, and unmatched comfort, it’s perfect for guests seeking an exclusive and indulgent stay.
                </p>
                <a href="<?php echo getRoomUrl('presidential-suite'); ?>" class="discover-button rect-btn">
                    Discover More <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="room-image">
                <div class="image-overlay-text">
                    <img src="<?php echo $baseUrl; ?>/images/presidentialmain.jpg" alt="Presidential Suite">
                </div>
            </div>
        </section>        
    </div>

    <!-- room card 4 -->
 <div class="roomscontainer">
        <section class="room-section rowreverse">
            <div class="room-content" data-aos="fade-up" data-aos-delay="200">
                <h2 class="room-heading">Twin Bedded Room for Comfortable Shared Stay</h2>
                
                <ul class="room-features">
                    <li>4 ROOMS</li>
                </ul>
                
                <p class="room-description">
                  Our Twin Bedded Rooms are ideal for friends or colleagues traveling together. With two comfortable beds, modern amenities, and a peaceful ambiance, these rooms ensure a restful stay.
                </p>
                
                <a href="<?php echo getRoomUrl('twin-bed'); ?>" class="discover-button rect-btn">
                    Discover More <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="room-image">
                <div class="image-overlay-text">
                    <img src="<?php echo $baseUrl; ?>/images/DSC00821-HDR-2-1-scaled.jpg" alt="twin beded rooms">
                </div>
            </div>
        </section>
    </div>

    <!-- room card 5 -->
 <div class="roomscontainer">
        <section class="room-section">
            <div class="room-content" data-aos="fade-up" data-aos-delay="200">
                <h2 class="room-heading">Deluxe Room with Elegant Interior Design</h2>
                
                <ul class="room-features">
                    <li>3 ROOMS </li>
                </ul>
                
                <p class="room-description">
                   Relax in style in our Deluxe Rooms, thoughtfully designed with warm interiors and modern comforts. Perfect for couples and families, these rooms offer a cozy retreat with beautiful surroundings.
                </p>
                
                <a href="<?php echo getRoomUrl('deluxe-room'); ?>" class="discover-button rect-btn">
                    Discover More <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="room-image">
                <div class="image-overlay-text">
                    <img src="<?php echo $baseUrl; ?>/images/deluxemain.jpg" alt="Deluxe Room">
                </div>
            </div>
        </section>
    </div>

 <!-- FAQ SECTION -->
<section class="faq-section">
  <h2>Rooms & Suites – Frequently Asked Questions</h2>

  <div class="faq-item">
    <button class="faq-question">
      What types of rooms and suites are available at Dhauladhar Heights Resort?
      <span>+</span>
    </button>
    <div class="faq-answer">
      <p>
        Dhauladhar Heights Resort offers a wide selection of luxury accommodations including Executive Rooms, Deluxe Rooms, Twin Bedded Rooms, Executive Suites, and Presidential Suites, all designed with modern amenities and elegant interiors for a comfortable stay in Dharamshala.
      </p>
    </div>
  </div>

  <div class="faq-item">
    <button class="faq-question">
      Do the rooms offer views of the Dhauladhar mountains?
      <span>+</span>
    </button>
    <div class="faq-answer">
      <p>
        Yes, many rooms and suites at our luxury resort in Dharamshala feature breathtaking views of the majestic Dhauladhar mountain range, lush tea gardens, and peaceful Himalayan surroundings.
      </p>
    </div>
  </div>

  <div class="faq-item">
    <button class="faq-question">
      What amenities are included in the resort rooms?
      <span>+</span>
    </button>
    <div class="faq-answer">
      <p>
        Our rooms include modern amenities such as high-speed Wi-Fi, smart TVs, comfortable bedding, premium toiletries, room service, spacious bathrooms, and elegant furnishings to ensure a relaxing and luxurious stay experience.
      </p>
    </div>
  </div>

  <div class="faq-item">
    <button class="faq-question">
      Is breakfast included with room bookings?
      <span>+</span>
    </button>
    <div class="faq-answer">
      <p>
        Breakfast inclusion depends on the room package selected during booking. Guests can choose from room-only, breakfast-inclusive, or customized stay packages based on their travel preferences.
      </p>
    </div>
  </div>

  <div class="faq-item">
    <button class="faq-question">
      Are the rooms suitable for families and group stays?
      <span>+</span>
    </button>
    <div class="faq-answer">
      <p>
        Absolutely. Our spacious rooms and suites are ideal for couples, families, corporate travelers, and group stays, offering comfort, privacy, and easy access to all resort facilities in Dharamshala.
      </p>
    </div>
  </div>

  <div class="faq-item">
    <button class="faq-question">
      How can I book rooms at Dhauladhar Heights Resort Dharamshala?
      <span>+</span>
    </button>
    <div class="faq-answer">
      <p>
        You can book your stay directly through our official website or contact our reservations team for the best room rates, exclusive offers, and personalized assistance for your Dharamshala vacation.
      </p>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

  <!-- AOS JS  -->
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
  <script src="<?php echo $baseUrl; ?>/script.js"></script>
</body>

</html>
