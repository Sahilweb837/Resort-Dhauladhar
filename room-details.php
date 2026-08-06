<?php
require_once __DIR__ . '/includes/functions.php';
$baseUrl = getBaseUrl();

// Detect requested room slug from query string or URI path
$roomSlug = $_GET['room'] ?? '';
if (empty($roomSlug)) {
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segments = array_values(array_filter(explode('/', $requestUri)));
    $roomsIndex = array_search('rooms', $segments);
    if ($roomsIndex !== false && isset($segments[$roomsIndex + 1])) {
        $roomSlug = end($segments);
    } else {
        $roomIndex = array_search('room', $segments);
        if ($roomIndex !== false && isset($segments[$roomIndex + 1])) {
            $roomSlug = end($segments);
        }
    }
}

// Redirect to room-details.html as requested
$targetSlug = !empty($roomSlug) ? $roomSlug : 'executive-room';
header('Location: ' . $baseUrl . '/room-details.html?room=' . urlencode($targetSlug));
exit();

// Room dictionary for title & metadata
$roomDataMap = [
    'executive-room' => [
        'name' => 'Executive Room',
        'title' => 'Executive Room with Modern Comforts - Hotel Dhauladhar Heights',
        'desc' => 'A perfect blend of comfort and functionality, our Executive Rooms are ideal for business and leisure travelers. Enjoy modern interiors, premium amenities, and a relaxing atmosphere after a day exploring Dharamshala.'
    ],
    'executive-suite' => [
        'name' => 'Executive Suite',
        'title' => 'Executive Suite with Spacious Living Area - Hotel Dhauladhar Heights',
        'desc' => 'Designed for those who prefer extra space and luxury, the Executive Suite offers a separate living area, elegant décor, and scenic views, making your stay both comfortable and memorable.'
    ],
    'presidential-suite' => [
        'name' => 'Presidential Suite',
        'title' => 'Presidential Suite Offering Ultimate Luxury - Hotel Dhauladhar Heights',
        'desc' => 'Experience the finest luxury in Dharamshala with our Presidential Suite. Featuring spacious living areas, premium furnishings, and unmatched comfort, it’s perfect for guests seeking an exclusive and indulgent stay.'
    ],
    'twin-bed' => [
        'name' => 'Twin Bedded Room',
        'title' => 'Twin Bedded Room for Shared Stay - Hotel Dhauladhar Heights',
        'desc' => 'Our Twin Bedded Rooms are ideal for friends or colleagues traveling together. With two comfortable beds, modern amenities, and a peaceful ambiance, these rooms ensure a restful stay.'
    ],
    'deluxe-room' => [
        'name' => 'Deluxe Room',
        'title' => 'Deluxe Room with Elegant Interior - Hotel Dhauladhar Heights',
        'desc' => 'Relax in style in our Deluxe Rooms, thoughtfully designed with warm interiors and modern comforts. Perfect for couples and families, these rooms offer a cozy retreat with beautiful surroundings.'
    ]
];

$activeRoom = $roomDataMap[$roomSlug] ?? $roomDataMap['executive-room'];
$activeKey = isset($roomDataMap[$roomSlug]) ? $roomSlug : 'executive-room';

$pageTitle = $activeRoom['title'];
$pageMetaDescription = $activeRoom['desc'];
include __DIR__ . '/includes/header.php';
?>

  <!----------------------- hero section ---------------------->
  <div class="common-hero" id="roomHero">
    <h1 data-aos="fade-down" data-aos-duration="1200"><?php echo htmlspecialchars($activeRoom['name']); ?></h1>
    <div class="hero-links" data-aos="fade-up" data-aos-duration="1200" data-aos-delay="300">
      <a href="<?php echo $baseUrl; ?>/">THE RESORT</a>
      <a href="<?php echo $baseUrl; ?>/rooms">/ Rooms</a>
      <a href="#">/ <?php echo htmlspecialchars($activeRoom['name']); ?></a>
    </div>
  </div>

  <div class="romm-detail">
    <section class="title-section">
      <div class="title-wrapper">
        <span class="line" data-aos="fade-up" data-aos-duration="3000"></span>
        <div class="logo-circle" data-aos="fade-up" data-aos-duration="3000">
          <img src="<?php echo $baseUrl; ?>/images/dhr_logo_icon.png" alt="logo">
        </div>
        <span class="line" data-aos="fade-up" data-aos-duration="3000"></span>
      </div>

      <h2 data-aos="fade-up" data-aos-duration="3000">Room Details</h2>
      <p class="utilitesp" data-aos="fade-up" data-aos-duration="3000">
        Stay in comfort and style at Dhauladhar Heights Resort, with rooms offering stunning Dhauladhar mountain views, modern amenities, and cozy interiors. Perfect for couples, families, or solo travelers.
      </p>
    </section>
  </div>

  <!-- ROOMS CLEAN URL NAVIGATION BUTTONS -->
  <div class="room-buttons">
    <div class="room-button rect-btn <?php echo $activeKey === 'executive-room' ? 'active' : ''; ?>" data-room="executive-room"> 
      <a href="<?php echo getRoomUrl('executive-room'); ?>">Executive Room</a>
    </div>
    <div class="room-button rect-btn <?php echo $activeKey === 'executive-suite' ? 'active' : ''; ?>" data-room="executive-suite"> 
      <a href="<?php echo getRoomUrl('executive-suite'); ?>">Executive Suite</a>
    </div>
    <div class="room-button rect-btn <?php echo $activeKey === 'presidential-suite' ? 'active' : ''; ?>" data-room="presidential-suite"> 
      <a href="<?php echo getRoomUrl('presidential-suite'); ?>">Presidential Suite</a>
    </div>
    <div class="room-button rect-btn <?php echo $activeKey === 'twin-bed' ? 'active' : ''; ?>" data-room="twin-bed"> 
      <a href="<?php echo getRoomUrl('twin-bed'); ?>">Twin Bed</a>
    </div>
    <div class="room-button rect-btn <?php echo $activeKey === 'deluxe-room' ? 'active' : ''; ?>" data-room="deluxe-room">
      <a href="<?php echo getRoomUrl('deluxe-room'); ?>">Deluxe Room</a>
    </div>
  </div>

  <!-- selected room details content (rendered dynamically via PHP & JS) -->
  <div class="roomscontainer">
    <section class="room-section" id="roomDetails">
      <div class="room-content" data-aos="fade-left">
        <h2 class="room-heading" id="roomTitle"><?php echo htmlspecialchars($activeRoom['name']); ?></h2>
        <p class="room-description" id="roomDescription">
          <?php echo htmlspecialchars($activeRoom['desc']); ?>
        </p>
      </div>
      <div class="room-image">
        <div class="image-overlay-text">
          <img id="slideImage" src="<?php echo $baseUrl; ?>/images/<?php 
            if ($activeKey === 'executive-suite') echo 'executivesuite.jpg';
            elseif ($activeKey === 'presidential-suite') echo 'presidentialmain.jpg';
            elseif ($activeKey === 'twin-bed') echo 'DSC00821-HDR-2-1-scaled.jpg';
            elseif ($activeKey === 'deluxe-room') echo 'deluxemain.jpg';
            else echo 'Executive Room.jpg';
          ?>" alt="<?php echo htmlspecialchars($activeRoom['name']); ?>">
        </div>
      </div>
    </section>
  </div>

  <div class="roomscontainer">
    <section class="room-section">
      <div class="room-content" data-aos="fade-left">
        <div class="roomaminities-content">
          <span class="roomaminities-subtitle">AMENITIES</span>
          <h2 class="roomaminities-title">
            What's Included
          </h2>

          <ul class="roomaminities-list">
            <li>Free Wi-Fi</li>
            <li>LED / Smart TV with cable channels</li>
            <li>Wardrobe</li>
            <li>Mountain / Garden View</li>
            <li>Cubical Shower</li>
          </ul>
        </div>
      </div>

      <div class="room-image">
          <div class="selectedroomslider">
            <div class="slides"></div>
            <div class="slider-dots"></div>
          </div>
      </div>
    </section>
  </div>

  <!-- ROOM IMAGE SLIDER -->
  <div class="rooms-slider">
    <div class="rooms-track">
      <!-- SLIDE 1 -->
      <div class="room-slide active">
        <div class="roomscontainer">
          <section class="room-section rowreverse">
            <div class="room-content" data-aos="fade-left">
              <h2 class="room-heading">Executive Room with Modern Comforts</h2>
              <ul class="room-features"><li>40 ROOMS</li></ul>
              <p class="room-description">
                A perfect blend of comfort and functionality, our Executive Rooms are ideal for business and leisure travelers.
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
      </div>

      <!-- SLIDE 2 -->
      <div class="room-slide">
        <div class="roomscontainer">
          <section class="room-section rowreverse">
            <div class="room-content" data-aos="fade-left">
              <h2 class="room-heading">Executive Suite with Spacious Living Area</h2>
              <ul class="room-features"><li>24 ROOMS</li></ul>
              <p class="room-description">
                Designed for those who prefer extra space and luxury, the Executive Suite offers a separate living area.
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
      </div>

      <!-- SLIDE 3 -->
      <div class="room-slide">
        <div class="roomscontainer">
          <section class="room-section rowreverse">
            <div class="room-content" data-aos="fade-left">
              <h2 class="room-heading">Presidential Suite Offering Ultimate Luxury Stay</h2>
              <ul class="room-features"><li>2 ROOMS</li></ul>
              <p class="room-description">
                Experience the finest luxury in Dharamshala with our Presidential Suite.
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
      </div>

      <!-- SLIDE 4 -->
      <div class="room-slide">
        <div class="roomscontainer">
          <section class="room-section rowreverse">
            <div class="room-content" data-aos="fade-left">
              <h2 class="room-heading">Twin Bedded Room for Comfortable Shared Stay</h2>
              <ul class="room-features"><li>4 ROOMS </li></ul>
              <p class="room-description">
                Our Twin Bedded Rooms are ideal for friends or colleagues traveling together.
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
      </div>
    </div>

    <!-- Dots -->
    <div class="slider-dots"></div>
  </div>

<!-- FAQ SECTION -->
<section class="faq-section">
  <h2>Luxury Stay FAQs</h2>

  <div class="faq-item">
    <button class="faq-question">
      What makes the rooms at Dhauladhar Heights Resort special?
      <span>+</span>
    </button>
    <div class="faq-answer">
      <p>
        Thoughtfully designed with elegant interiors, premium comforts, and breathtaking Dhauladhar mountain views.
      </p>
    </div>
  </div>

  <div class="faq-item">
    <button class="faq-question">
      Do the rooms feature scenic mountain views?
      <span>+</span>
    </button>
    <div class="faq-answer">
      <p>
        Yes, most rooms and suites open to stunning views of the majestic Dhauladhar ranges and tea gardens.
      </p>
    </div>
  </div>

  <div class="faq-item">
    <button class="faq-question">
      What in-room amenities can guests expect?
      <span>+</span>
    </button>
    <div class="faq-answer">
      <p>
        Guests can enjoy luxurious bedding, high-speed Wi-Fi, Smart LED television, premium bath amenities, tea/coffee maker, and room service.
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
