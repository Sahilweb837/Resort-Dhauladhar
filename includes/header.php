<?php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Agu+Display:MORF@0..60&family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Edu+AU+VIC+WA+NT+Arrows:wght@400..700&family=Inconsolata:wght@200..900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Oswald:wght@200..700&family=Outfit:wght@100..900&family=Parkinsans:wght@300..800&family=Quicksand:wght@300..700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/emailjs-com@3/dist/email.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <title>Hotel Dhauladhar</title>
</head>
<body>

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
            <li><a href="rooms.html">ROOMS</a></li>
            <li><a href="destination-wedding.php">DESTINATION WEDDING</a></li>
            <li><a href="events.html">EVENT VENUES</a></li>
            <li><a href="blog.php">BLOG</a></li>
            <li><a href="contact.html">CONTACT</a></li>
        </ul>
    </div>
</div>

<script>
    // Mobile menu functionality
    const menuBtn = document.getElementById('menuOpen');
    const overlayMenu = document.getElementById('overlayMenu');
    const menuClose = document.getElementById('menuClose');

    if (menuBtn) {
        menuBtn.addEventListener('click', () => {
            overlayMenu.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }

    if (menuClose) {
        menuClose.addEventListener('click', () => {
            overlayMenu.classList.remove('active');
            document.body.style.overflow = '';
        });
    }

    // Close menu when clicking outside
    overlayMenu?.addEventListener('click', (e) => {
        if (e.target === overlayMenu) {
            overlayMenu.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    // AOS initialization
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
    }
</script>
