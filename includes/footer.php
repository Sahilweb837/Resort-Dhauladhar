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

<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    // Initialize AOS
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
    }

    // Newsletter subscription
    document.getElementById('newsletterForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const email = this.querySelector('input[name="email"]').value;
        
        // You can add AJAX call here to save email to database
        alert('Thank you for subscribing!');
        this.reset();
    });
</script>
</body>
</html>