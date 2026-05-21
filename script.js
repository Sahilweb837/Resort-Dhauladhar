
document.addEventListener("DOMContentLoaded", () => {
  if (window.AOS) {
    AOS.init({
      once: true,
      duration: 800,
      easing: 'ease-out-cubic',
      disable: false
    });
  }

  // ------------------------------- NAV LOGIC -------------------------------
  const overlay = document.getElementById("overlayMenu");
  const openBtn = document.getElementById("menuOpen");
  const closeBtn = document.getElementById("menuClose");
  const header = document.querySelector(".site-header");

  // Open menu
  if (openBtn && overlay) openBtn.onclick = () => {
    overlay.classList.add("active");
    document.body.style.overflow = "hidden";
  };

  // Close menu
  if (closeBtn) closeBtn.onclick = () => {
    closeOverlay();
  };

  // Reusable close function
  function closeOverlay() {
    if (!overlay) return;
    overlay.classList.remove("active");
    document.body.style.overflow = "auto";
  }

  // Header scroll effect
  window.addEventListener("scroll", () => {
    header?.classList.toggle("is-scrolled", window.scrollY > 50);
  });

  //  AUTO CLOSE
  window.addEventListener("resize", () => {
    if (window.innerWidth >= 768) {  
      closeOverlay();
    }
  });

  // ------------------------------- HEADER WEATHER -------------------------------
  const weatherEls = document.querySelectorAll("#weather");
  const weatherPopupContent = document.getElementById("weatherPopupContent");
  const weatherBtnText = document.getElementById("weatherText");

  const weatherIcons = {
    // OpenWeatherMap conditions
    "clear sky": "fa-sun",
    "few clouds": "fa-cloud-sun",
    "scattered clouds": "fa-cloud",
    "broken clouds": "fa-cloud",
    "shower rain": "fa-cloud-showers-heavy",
    "rain": "fa-cloud-rain",
    "thunderstorm": "fa-bolt",
    "snow": "fa-snowflake",
    "mist": "fa-smog",
    // Fallback/Open-Meteo codes
    0: "fa-sun", // Clear
    1: "fa-cloud-sun", // Mainly clear
    2: "fa-cloud-sun", // Partly cloudy
    3: "fa-cloud", // Overcast
    45: "fa-smog", // Fog
    48: "fa-smog", // Fog
    51: "fa-cloud-rain", // Drizzle
    61: "fa-cloud-showers-heavy", // Rain
    71: "fa-snowflake", // Snow
    80: "fa-cloud-showers-water", // Showers
    95: "fa-bolt-lightning" // Thunderstorm
  };

  function getWeatherIcon(condition) {
    if (typeof condition === 'number') {
      return weatherIcons[condition] || "fa-cloud-sun";
    }
    const lower = condition.toLowerCase();
    for (const key in weatherIcons) {
      if (lower.includes(key)) return weatherIcons[key];
    }
    return "fa-cloud-sun";
  }

  function setWeatherText(html, detailsHtml = "") {
    weatherEls.forEach(el => el.innerHTML = html);
    if (weatherBtnText) weatherBtnText.innerHTML = html;
    if (weatherPopupContent) {
      weatherPopupContent.innerHTML = detailsHtml || html;
    }
  }

  async function getWeather() {
    if (!weatherEls.length && !weatherBtnText) return;

    const apiKey = "fbe763d7747185ae17485ce247af3678";
    const lat = 32.219;
    const lon = 76.3234;
    const openWeatherUrl = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&units=metric&appid=${apiKey}`;
    const openMeteoUrl = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m`;

    setWeatherText('<i class="fa-solid fa-spinner fa-spin"></i> Dharamshala weather...');

    try {
      const response = await fetch(openWeatherUrl);
      const data = await response.json();

      if (response.ok && data.main?.temp !== undefined) {
        const temp = Math.round(data.main.temp);
        const desc = data.weather?.[0]?.description || "weather";
        const icon = getWeatherIcon(desc);
        const humidity = data.main.humidity;
        const wind = data.wind?.speed;

        const summary = `<i class="fa-solid ${icon}"></i> Dharamshala: ${temp}&deg;C | ${desc}`;
        const details = `
          <div class="weather-details">
            <div class="temp-main">${temp}&deg;C</div>
            <div class="desc-main">${desc}</div>
            <div class="extra-info">
              <span><i class="fa-solid fa-droplet"></i> Humidity: ${humidity}%</span>
              <span><i class="fa-solid fa-wind"></i> Wind: ${wind} m/s</span>
            </div>
          </div>
        `;
        setWeatherText(summary, details);
        return;
      }
    } catch (error) {
      console.warn("OpenWeatherMap failed, trying fallback...");
    }

    // Fallback to Open-Meteo
    try {
      const response = await fetch(openMeteoUrl);
      const data = await response.json();

      if (data.current) {
        const temp = Math.round(data.current.temperature_2m);
        const code = data.current.weather_code;
        const icon = getWeatherIcon(code);
        const humidity = data.current.relative_humidity_2m;
        const wind = data.current.wind_speed_10m;

        const summary = `<i class="fa-solid ${icon}"></i> Dharamshala: ${temp}&deg;C`;
        const details = `
          <div class="weather-details">
            <div class="temp-main">${temp}&deg;C</div>
            <div class="extra-info">
              <span><i class="fa-solid fa-droplet"></i> Humidity: ${humidity}%</span>
              <span><i class="fa-solid fa-wind"></i> Wind: ${wind} km/h</span>
            </div>
          </div>
        `;
        setWeatherText(summary, details);
      }
    } catch (error) {
      setWeatherText('<i class="fa-solid fa-triangle-exclamation"></i> Dharamshala: Weather error');
    }
  }

  getWeather();

  const weatherButton = document.getElementById("weather4phone");
  const weatherPopup = document.getElementById("weatherPopup");
  let weatherPopupOpen = false;

  function toggleWeatherPopup() {
    if (!weatherPopup) return;
    weatherPopupOpen = !weatherPopupOpen;
    weatherPopup.classList.toggle("visible", weatherPopupOpen);
    if (weatherPopupOpen) {
      weatherPopup.setAttribute("aria-hidden", "false");
    } else {
      weatherPopup.setAttribute("aria-hidden", "true");
    }
  }

  if (weatherButton) {
    weatherButton.addEventListener("click", (event) => {
      event.preventDefault();
      event.stopPropagation();
      toggleWeatherPopup();
    });
  }

  window.addEventListener("click", (event) => {
    if (!weatherPopupOpen || !weatherPopup) return;
    if (weatherPopup.contains(event.target) || weatherButton?.contains(event.target)) return;
    weatherPopupOpen = false;
    weatherPopup.classList.remove("visible");
    weatherPopup.setAttribute("aria-hidden", "true");
  });

  window.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && weatherPopupOpen && weatherPopup) {
      weatherPopupOpen = false;
      weatherPopup.classList.remove("visible");
      weatherPopup.setAttribute("aria-hidden", "true");
    }
  });

  // ------------------------------- CONTACT FORM -------------------------------
  if (window.emailjs) {
    emailjs.init("YOUR_PUBLIC_KEY"); 
    const form = document.getElementById("contactForm");
    if (form) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();
        emailjs.sendForm("YOUR_SERVICE_ID", "YOUR_TEMPLATE_ID", this)
          .then(() => { alert("Message sent successfully!"); form.reset(); },
            err => alert("FAILED... " + err.text));
      });
    }
  }

  // ------------------------------- DESTINATION WEDDING SLIDER -------------------------------
  // const weddingSlidesData = [
  //   { img: "./images/PHOTO-2024-01-20-20-04-52.jpg", title: "VOWS ON THE BEACH", desc: "Let the pristine sands, azure waters, breath-taking sunsets and our immaculate hospitality be the perfect companion for your dream wedding.", left: "ICONIC CITY<br>WEDDINGS", right: "MOUNTAIN<br>WEDDING VOWS" },
  //   { img: "./images/DSC09631-scaled.jpg", title: "ROYAL PALACE WEDDINGS", desc: "Experience regal celebrations amidst heritage palaces, timeless architecture and royal grandeur.", left: "BEACH<br>WEDDINGS", right: "ICONIC CITY<br>WEDDINGS" },
  //   { img: "./images/IMG_3835-scaled.jpg", title: "MOUNTAIN WEDDING ", desc: "Exchange vows amidst serene mountains, misty valleys and breathtaking natural beauty.", left: "ROYAL PALACE<br>WEDDINGS", right: "BEACH<br>WEDDINGS" }
  // ];

  // let index = 0;
  // const current = document.getElementById("current");
  // const nextImg = document.getElementById("next");
  // const title = document.getElementById("title");
  // const desc = document.getElementById("desc");
  // const leftText = document.getElementById("leftText");
  // const rightText = document.getElementById("rightText");
  // const leftSide = document.querySelector(".left");
  // const rightSide = document.querySelector(".right");
  // const leftImg = leftSide.querySelector(".side-img");
  // const rightImg = rightSide.querySelector(".side-img");

  // function loadContent(i) {
  //   if (current) {
  //     current.src = weddingSlidesData[i].img;
  //     title.innerHTML = weddingSlidesData[i].title;
  //     desc.innerHTML = weddingSlidesData[i].desc;
  //     leftText.innerHTML = weddingSlidesData[i].left;
  //     rightText.innerHTML = weddingSlidesData[i].right;
  //   }
  // }
  // loadContent(index);

  // window.next = function () {
  //   const newIndex = (index + 1) % weddingSlidesData.length;
  //   rightImg.style.backgroundImage = `url(${weddingSlidesData[index].img})`;
  //   rightSide.classList.add("show");
  //   nextImg.src = weddingSlidesData[newIndex].img;
  //   current.style.transform = "translateX(-100%)";
  //   nextImg.style.transform = "translateX(100%)";
  //   setTimeout(() => nextImg.style.transform = "translateX(0)", 20);
  //   setTimeout(() => {
  //     index = newIndex;
  //     loadContent(index);
  //     current.style.transform = "translateX(0)";
  //     rightSide.classList.remove("show");
  //   }, 700);
  // }

  // window.prev = function () {
  //   const newIndex = (index - 1 + weddingSlidesData.length) % weddingSlidesData.length;
  //   leftImg.style.backgroundImage = `url(${weddingSlidesData[index].img})`;
  //   leftSide.classList.add("show");
  //   nextImg.src = weddingSlidesData[newIndex].img;
  //   current.style.transform = "translateX(100%)";
  //   nextImg.style.transform = "translateX(-100%)";
  //   setTimeout(() => nextImg.style.transform = "translateX(0)", 20);
  //   setTimeout(() => {
  //     index = newIndex;
  //     loadContent(index);
  //     current.style.transform = "translateX(0)";
  //     leftSide.classList.remove("show");
  //   }, 700);
  // }

  // ------------------------------- DESTINATION WEDDING EVENT PAGE SLIDER -------------------------------
  const eventContainer = document.querySelector(".container");

  if (eventContainer) {
    const eventSlide = eventContainer.querySelector(".slide");
    const eventNext = eventContainer.querySelector(".next");
    const eventPrev = eventContainer.querySelector(".prev");

    if (eventSlide && eventNext && eventPrev) {
      eventNext.addEventListener("click", () => {
        const items = eventSlide.querySelectorAll(".item");
        eventSlide.appendChild(items[0]);
      });

      eventPrev.addEventListener("click", () => {
        const items = eventSlide.querySelectorAll(".item");
        eventSlide.prepend(items[items.length - 1]);
      });
    }
  }

}); 

// event main slider
document.addEventListener("DOMContentLoaded", function(){

const banners = document.querySelectorAll(".banneritem");
const controls = document.querySelectorAll(".bannercontrol");

if (!banners.length || !controls.length) return;

let bannerIndex = 0;
let autoSlider;

/* SHOW BANNER */

function showBanner(i){
if (!banners[i] || !controls[i]) return;

banners.forEach(item => item.classList.remove("activebanner"));
controls.forEach(ctrl => ctrl.classList.remove("activecontrol"));

banners[i].classList.add("activebanner");
controls[i].classList.add("activecontrol");

}

/* AUTO SLIDE FUNCTION */

function startAutoSlide(){

autoSlider = setInterval(function(){

bannerIndex++;

if(bannerIndex >= banners.length){
bannerIndex = 0;
}

showBanner(bannerIndex);

},5000);

}

/* CLICK CHANGE */

controls.forEach((control,i)=>{

control.addEventListener("click",function(){

bannerIndex = i;

showBanner(bannerIndex);

/* reset autoplay */

clearInterval(autoSlider);
startAutoSlide();

});

});

/* START AUTOPLAY */

startAutoSlide();

});

/* ================= HERO SLIDER ================= */
document.addEventListener("DOMContentLoaded", () => {

  const heroSlides = document.querySelectorAll(".slider-item");
  const heroNextBtn = document.querySelector(".hero-next-btn");
  const heroPrevBtn = document.querySelector(".hero-prev-btn");

  if (heroSlides.length) {
    let currentHeroSlide = 0;
    let slideInterval;
    const slideTime = 5000;

    function goToSlide(index) {
      heroSlides[currentHeroSlide].classList.remove("is-active");
      currentHeroSlide = (index + heroSlides.length) % heroSlides.length;
      heroSlides[currentHeroSlide].classList.add("is-active");
    }

    function nextSlide() { goToSlide(currentHeroSlide + 1); }
    function prevSlide() { goToSlide(currentHeroSlide - 1); }

    function startSlider() {
      slideInterval = setInterval(nextSlide, slideTime);
    }

    function resetSlider() {
      clearInterval(slideInterval);
      startSlider();
    }

    heroNextBtn?.addEventListener("click", () => {
      nextSlide();
      resetSlider();
    });

    heroPrevBtn?.addEventListener("click", () => {
      prevSlide();
      resetSlider();
    });

    startSlider(); // 
  }

  if (window.AOS) {
    AOS.init({
      once: true,
      duration: 800,
      easing: "ease-out-cubic",
      disable: false
    });

    window.addEventListener("load", () => {
      AOS.refreshHard(); //
    });
  }

});

// venuue
document.addEventListener("DOMContentLoaded", () => {
  const eventType = document.getElementById('eventType');
  const capacity = document.getElementById('capacity');
  const venueCards = document.querySelectorAll('.venue-image-card');
  const enquireBtns = document.querySelectorAll('.enquire-btn');

  if (eventType && capacity && venueCards.length) {

    function filterVenues() {
      const eventValue = eventType.value;
      const capacityValue = capacity.value ? Number(capacity.value) : null;

      venueCards.forEach(card => {
        const cardEvents = card.dataset.event.split(' ');
        const cardCapacity = Number(card.dataset.capacity);

        let eventMatch = true;
        let capacityMatch = true;

        // Event filter (can have multiple events)
        if (eventValue && !cardEvents.includes(eventValue)) {
          eventMatch = false;
        }

        // Capacity filter (less than or equal to selected capacity)
        if (capacityValue && cardCapacity > capacityValue) {
          capacityMatch = false;
        }

        // Show/hide card based on both filters
        if (eventMatch && capacityMatch) {
          card.style.display = "block";
          setTimeout(() => {
            card.style.opacity = "1";
            card.style.transform = "translateY(0)";
          }, 10);
        } else {
          card.style.display = "none";
        }
      });
    }

    // Event listeners for filters
    eventType.addEventListener('change', filterVenues);
    capacity.addEventListener('change', filterVenues);
    filterVenues();
  }
});

document.addEventListener("DOMContentLoaded", () => {
  const eventType = document.getElementById("eventType");
  const seatingStyle = document.getElementById("seatingStyle");
  const capacity = document.getElementById("capacity");
  const cards = document.querySelectorAll(".venue-card");

  function filterVenues() {
    const eventVal = eventType.value;
    const seatingVal = seatingStyle.value;
    const capacityVal = capacity.value;

    cards.forEach(card => {
      const cardEvent = card.dataset.event;
      const cardSeating = card.dataset.seating;
      const cardCapacity = Number(card.dataset.capacity);

      let match = true;

      if (eventVal && cardEvent !== eventVal) match = false;
      if (seatingVal && cardSeating !== seatingVal) match = false;

      if (capacityVal) {
        if (capacityVal === "50" && cardCapacity > 50) match = false;
        if (capacityVal === "100" && (cardCapacity < 50 || cardCapacity > 100)) match = false;
        if (capacityVal === "300" && (cardCapacity < 100 || cardCapacity > 300)) match = false;
        if (capacityVal === "500" && cardCapacity < 300) match = false;
      }

      card.classList.toggle("hide", !match);
    });
  }

  if (eventType) eventType.addEventListener("change", filterVenues);
if (seatingStyle) seatingStyle.addEventListener("change", filterVenues);
if (capacity) capacity.addEventListener("change", filterVenues);
});



// ================= EVENT SLIDER =================
document.addEventListener("DOMContentLoaded", () => {

  const slider = document.querySelector(".event-slider");
  if (!slider) return;

  const track = slider.querySelector(".event-slider-track");
  if (!track || !track.children.length) return;

  const prevBtns = slider.querySelectorAll(".event-prev");
  const nextBtns = slider.querySelectorAll(".event-next");

  let slides = Array.from(track.children);
  let index = 0;
  let slidesToShow = 3;

  function getSlidesToShow() {
    return window.innerWidth < 992 ? 1 : 3;
  }

  function setupSlider() {
    slidesToShow = getSlidesToShow();
    track.innerHTML = "";

    slides.forEach(slide => track.appendChild(slide));

    slides.slice(0, slidesToShow).forEach(slide =>
      track.appendChild(slide.cloneNode(true))
    );

    slides.slice(-slidesToShow).forEach(slide =>
      track.insertBefore(slide.cloneNode(true), track.firstChild)
    );

    index = slidesToShow;
    move(false);
  }

  function move(animate = true) {
    const slideWidth = track.children[0].offsetWidth;
    track.style.transition = animate ? "0.5s ease" : "none";
    track.style.transform = `translateX(-${index * slideWidth}px)`;
  }

  nextBtns.forEach(btn => {
    btn.addEventListener("click", () => {
      index++;
      move();

      if (index >= track.children.length - slidesToShow) {
        setTimeout(() => {
          index = slidesToShow;
          move(false);
        }, 500);
      }
    });
  });

  prevBtns.forEach(btn => {
    btn.addEventListener("click", () => {
      index--;
      move();

      if (index <= 0) {
        setTimeout(() => {
          index = track.children.length - slidesToShow * 2;
          move(false);
        }, 500);
      }
    });
  });

  window.addEventListener("resize", setupSlider);
  setupSlider();
});

/* ================= BLOG DATA ================= */

const blogs = {
  "indian-wedding-malta": {
    title: "Indian Wedding In Malta",
    date: "March 12, 2026",
    image: "./images/IMG_8932.jpg",
    content: `
      <p>Malta is one of the most beautiful destinations for Indian weddings.</p>
      <p>Luxury venues, sea views, and heritage locations make it unforgettable.</p>
    `
  },

  "indian-wedding-barcelona": {
    title: "Indian Wedding Venues In Barcelona",
    date: "March 18, 2026",
    image: "/images/IMG_8331.jpg",
    content: `
      <p>Barcelona offers a blend of modern architecture and rich traditions.</p>
      <p>Indian weddings here are vibrant and colorful.</p>
    `
  },

  "indian-wedding-switzerland": {
    title: "Indian Wedding In Switzerland",
    date: "March 25, 2026",
    image: "./images/PHOTO-2024-01-20-20-04-47-2.jpg",
    content: `
      <p>Switzerland offers snow-clad mountains and fairy-tale venues.</p>
      <p>Perfect for luxury Indian destination weddings.</p>
    `
  },

  "indian-wedding-spain": {
    title: "Indian Wedding In Spain",
    date: "April 2, 2026",
    image: "https://images.unsplash.com/photo-1520854221256-17451cc331bf",
    content: `
      <p>Spain is famous for royal palaces and seaside venues.</p>
      <p>Indian weddings here feel elegant and grand.</p>
    `
  },

  "indian-wedding-bahrain": {
    title: "Indian Destination Wedding In Bahrain",
    date: "April 8, 2026",
    image: "https://images.unsplash.com/photo-1517841905240-472988babdf9",
    content: `
      <p>Bahrain offers luxury hotels and desert charm.</p>
      <p>A premium destination for intimate Indian weddings.</p>
    `
  },

  "indian-wedding-france": {
    title: "Indian Wedding In France",
    date: "April 15, 2026",
    image: "https://images.unsplash.com/photo-1511285560929-80b456fea0bc",
    content: `
      <p>France brings romance, vineyards, and castles.</p>
      <p>Ideal for high-end Indian destination weddings.</p>
    `
  },

  "luxury-indian-wedding": {
    title: "Luxury Indian Wedding",
    date: "April 20, 2026",
    image: "https://images.unsplash.com/photo-1517841905240-472988babdf9",
    content: `
      <p>Luxury Indian weddings redefine elegance.</p>
      <p>From decor to hospitality, everything is world-class.</p>
    `
  },

  "royal-indian-wedding": {
    title: "Royal Indian Wedding",
    date: "April 28, 2026",
    image: "https://images.unsplash.com/photo-1500530855697-b586d89ba3ee",
    content: `
      <p>Royal weddings are about grandeur and heritage.</p>
      <p>Perfect for couples seeking a majestic celebration.</p>
    `
  }
};

/* ================= BLOG DETAIL LOGIC ================= */

document.addEventListener("DOMContentLoaded", function () {

  const blogTitle = document.getElementById("blogTitle");
  if (!blogTitle) return;

  const params = new URLSearchParams(window.location.search);
  const slug = params.get("slug");

  if (!slug || !blogs[slug]) {
    blogTitle.innerText = "Blog not found";
    return;
  }

  const blog = blogs[slug];

  document.getElementById("blogTitle").innerText = blog.title;
  document.getElementById("blogDate").innerText = blog.date;
  document.getElementById("blogImage").src = blog.image;
  document.getElementById("blogContent").innerHTML = blog.content;

});



// ================= ROOM DETAILS =================
document.addEventListener("DOMContentLoaded", () => {

  const roomTitle = document.getElementById("roomTitle");
  const slideImage = document.getElementById("slideImage");

  // if room detail is not present then exit
  if (!roomTitle || !slideImage) return;

  const roomsData = {
    "double-suite-room": {
      title: "Double Suite Room",
      images: [
        "./images/DSC00821-HDR-2-1-scaled.jpg",
        "./images/DSC00176-Edit.jpg",
        "./images/DSC03083 copy.jpg"
      ],
      description: "Luxury double suite room with king bed, balcony and premium amenities.",
      price: "$560 / Night"
    },

    "delux-family-room": {
      title: "Delux Family Room",
      images: [
        "./images/deluxemain.jpg",
        "./images/DSC03289.jpg",
        "./images/DSC03289.jpg"
      ],
      description: "Spacious family room with 2 king beds, perfect for families.",
      price: "$560 / Night"
    },

    "superior-bed-room": {
      title: "Superior Bed Room",
      images: [
        "./images/DSC03083 copy.jpg",
        "./images/DSC03289.jpg",
        "./images/DSC03289.jpg"
      ],
      description: "Elegant superior room with modern interiors and comfort.",
      price: "$560 / Night"
    }
  };

  const params = new URLSearchParams(window.location.search);
  const roomSlug = params.get("room");

 
  if (!roomSlug || !roomsData[roomSlug]) {
    roomTitle.innerText = "Room not found";
    return;
  }

  const room = roomsData[roomSlug];

  // TEXT
  roomTitle.innerText = room.title;
  document.getElementById("roomDescription").innerText = room.description;
  document.getElementById("roomPrice").innerText = room.price;

  // SLIDER
  let currentIndex = 0;
  slideImage.src = room.images[currentIndex];

  const nextBtn = document.querySelector(".next");
  const prevBtn = document.querySelector(".prev");

  if (nextBtn && prevBtn) {
    nextBtn.addEventListener("click", () => {
      currentIndex = (currentIndex + 1) % room.images.length;
      slideImage.src = room.images[currentIndex];
    });

    prevBtn.addEventListener("click", () => {
      currentIndex = (currentIndex - 1 + room.images.length) % room.images.length;
      slideImage.src = room.images[currentIndex];
    });
  }
});

// ================= GALLERY PAGE =================
document.addEventListener("DOMContentLoaded", () => {

  const buttons = document.querySelectorAll(".editorial-filters button");
  const items = document.querySelectorAll(".editorial-item");
  const lightbox = document.querySelector(".lightbox");
  const lightboxImg = document.querySelector(".lightbox-img");
  const closeBtn = document.querySelector(".close");

  // FILTERS
  buttons.forEach(btn => {
    btn.addEventListener("click", () => {
      buttons.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");

      const filter = btn.dataset.filter;

      items.forEach(item => {
        item.style.display =
          filter === "all" || item.classList.contains(filter)
            ? "block"
            : "none";
      });
    });
  });

  // IMAGE LIGHTBOX
  items.forEach(item => {
    item.addEventListener("click", () => {
      const img = item.querySelector("img");
      if (!img || !lightboxImg || !lightbox) return;

      lightboxImg.src = img.src;
      lightbox.style.display = "flex";
    });
  });

  // CLOSE BUTTON
  closeBtn?.addEventListener("click", () => {
    lightbox.style.display = "none";
  });

  // CLOSE ON BACKGROUND CLICK
  lightbox?.addEventListener("click", e => {
    if (e.target === lightbox) {
      lightbox.style.display = "none";
    }
  });

});




// rooms detail auto play slider 
document.addEventListener("DOMContentLoaded", () => {
  const slides = document.querySelectorAll(".room-slide");
  const dotsContainer = document.querySelector(".slider-dots");
  if (!slides.length || !dotsContainer) return;

  let currentSlide = 0;
  let slideInterval;

  //btns
  slides.forEach((_, index) => {
    const dot = document.createElement("span");
    if (index === 0) dot.classList.add("active");
    dot.addEventListener("click", () => {
      goToSlide(index);
      resetAutoplay();
    });
    dotsContainer.appendChild(dot);
  });

  const dots = document.querySelectorAll(".slider-dots span");

  function showSlide(index) {
    if (!slides[index] || !dots[index]) return;

    slides.forEach(slide => slide.classList.remove("active"));
    dots.forEach(dot => dot.classList.remove("active"));

    slides[index].classList.add("active");
    dots[index].classList.add("active");
  }

  function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
  }

  function goToSlide(index) {
    currentSlide = index;
    showSlide(currentSlide);
  }

  function startAutoplay() {
    slideInterval = setInterval(nextSlide, 4000);
  }

  function resetAutoplay() {
    clearInterval(slideInterval);
    startAutoplay();
  }

  startAutoplay();
});

//  selected rooms
document.addEventListener("DOMContentLoaded", function () {
  const roomData = {
    "executive-room": {
      title: "Executive Room with Modern Comforts",
      features: "40 ROOMS",
      desc: "A perfect blend of comfort and functionality, our Executive Rooms are ideal for business and leisure travelers. Enjoy modern interiors, premium amenities, and a relaxing atmosphere after a day exploring Dharamshala.",
      image: "./images/DSC00963.jpg"
    },
    "executive-suite": {
      title: "Executive Suite with Spacious Living Area",
      features: "24 ROOMS",
      desc: "Designed for those who prefer extra space and luxury, the Executive Suite offers a separate living area, elegant décor, and scenic views, making your stay both comfortable and memorable.",
      image: "./images/executivesuite.jpg"
    },
    "presidential-suite": {
      title: "Presidential Suite Offering Ultimate Luxury Stay",
      features: "2 ROOMS",
      desc: "Experience the finest luxury in Dharamshala with our Presidential Suite. Featuring spacious living areas, premium furnishings, and unmatched comfort, it’s perfect for guests seeking an exclusive and indulgent stay.",
      image: "./images/presidentialmain.jpg"
    },
    "twin-bed": {
      title: "Twin Bedded Room for Comfortable Shared Stay",
      features: "4 ROOMS",
      desc: "Our Twin Bedded Rooms are ideal for friends or colleagues traveling together. With two comfortable beds, modern amenities, and a peaceful ambiance, these rooms ensure a restful stay.",
      image: "./images/DSC00821-HDR-2-1-scaled.jpg"
    },
    "deluxe-room": {
      title: "Deluxe Room with Elegant Interior Design",
      features: "3 ROOMS",
      desc: "Relax in style in our Deluxe Rooms, thoughtfully designed with warm interiors and modern comforts. Perfect for couples and families, these rooms offer a cozy retreat with beautiful surroundings.",
      image: "./images/deluxemain.jpg"
    }
  };

  const params = new URLSearchParams(window.location.search);
  const roomKey = params.get("room");
  const room = roomData[roomKey];

  if (!room) return;

  const roomDetails = document.getElementById("roomDetails");
const roomHero = document.getElementById("roomHero");
if (!roomDetails || !roomHero) return;
roomHero.style.backgroundImage = `url(${room.image})`;
  roomDetails.innerHTML = `
    <div class="room-content" data-aos="fade-up">
      <h2 class="room-heading">${room.title}</h2>

      <ul class="room-features">
        <li>${room.features}</li>
      </ul>

      <p class="room-description">${room.desc}</p>

      <a href="contact.html" class="discover-button rect-btn">
        BOOK now
      </a>
    </div>

    <div class="room-image">
      <div class="image-overlay-text">
        <img src="${room.image}" alt="${room.title}">
      </div>
    </div>
  `;
});
// if user select btns for rooms
document.addEventListener("click", function (e) {
  const btn = e.target.closest(".room-button");
  if (btn) {
    const roomKey = btn.dataset.room;
    window.location.href = `room-details.html?room=${roomKey}`;
  }
});


// // selected room images slides
document.addEventListener("DOMContentLoaded", function () {

  let slideIndex = 0;
  let autoplayInterval;

  /* ================= ROOM IMAGES DATA ================= */
  const roomImages = {
    "executive-room": [
      "./images/Executive Room (2).jpg",
      "./images/Executive Room 1.jpg",
      "./images/DSC01054 (1).jpg"
    ],
    "executive-suite": [
      "./images/executivesuite2.jpg",
      "./images/executivesuite1.jpg",
      "/images/DSC00496-HDR-Enhanced-NR-Edit-1-scaled.jpg"
    ],
    "presidential-suite": [
      "./images/Presidential Suite Living room.jpg.jpg",
      "./images/presidentialmain3.jpg",
      "./images/DSC01195-HDR-scaled.jpg"
    ],
    "twin-bed": [
      "./images/DSC00511-HDR-2-scaled.jpg",
      "./images/Executive Twin Bedroom.jpg",
      "./images/twinbebbed.jpg"
    ],
    "deluxe-room": [
      "./images/deluxemain1.jpg",
      "./images/deluxemain2.jpg",
      "./images/deluxemain.jpg"
    ]
  };

  /* ================= LOAD SLIDER ================= */
  function loadSelectedRoomSlider(roomKey) {

    const slider = document.querySelector(".selectedroomslider");
    if (!slider) return;

    const slidesEl = slider.querySelector(".slides");
    const dotsEl = slider.querySelector(".slider-dots");
    if (!slidesEl || !dotsEl) return;

    if (!roomImages[roomKey]) {
      roomKey = "executive-room";
    }

    slidesEl.innerHTML = "";
    dotsEl.innerHTML = "";

    roomImages[roomKey].forEach((img, index) => {

      slidesEl.innerHTML += `
        <div class="slide">
          <img src="${img}" alt="Room Image ${index + 1}">
        </div>
      `;

      dotsEl.innerHTML += `<span class="${index === 0 ? "active" : ""}"></span>`;
    });

    slideIndex = 0;
    updateSlider();
    startAutoplay();
  }

  /* ================= UPDATE SLIDER ================= */
  function updateSlider() {
    const slides = document.querySelector(".selectedroomslider .slides");
    const dots = document.querySelectorAll(".selectedroomslider .slider-dots span");

    if (!slides) return;

    slides.style.transform = `translateX(-${slideIndex * 100}%)`;

    dots.forEach((dot, i) => {
      dot.classList.toggle("active", i === slideIndex);
    });
  }

  /* ================= AUTOPLAY ================= */
  function startAutoplay() {

    clearInterval(autoplayInterval);

    autoplayInterval = setInterval(() => {

      const totalSlides = document.querySelectorAll(".selectedroomslider .slide").length;

      if (totalSlides === 0) return;

      slideIndex = (slideIndex + 1) % totalSlides;

      updateSlider();

    }, 3000);
  }

  /* ================= LOAD BASED ON URL ================= */

  const params = new URLSearchParams(window.location.search);
  const roomKeyFromURL = params.get("room") || "executive-room";

  loadSelectedRoomSlider(roomKeyFromURL);

});


// FAQ LOGIC
document.querySelectorAll(".faq-question").forEach(q => {
  q.addEventListener("click", () => {

    const currentlyActive = document.querySelector(".faq-question.active");

    // Close already open FAQ (if it's not the one clicked)
    if (currentlyActive && currentlyActive !== q) {
      currentlyActive.classList.remove("active");
      currentlyActive.nextElementSibling.style.maxHeight = null;
    }

    // Toggle current FAQ
    q.classList.toggle("active");
    const answer = q.nextElementSibling;

    if (q.classList.contains("active")) {
      answer.style.maxHeight = answer.scrollHeight + "px";
    } else {
      answer.style.maxHeight = null;
    }
  });
});
if (window.jQuery) {
$(document).ready(function(){

/* ================================  DESTINATION WEDDING SLIDER================================ */

if ($('.destination-main-slider').length && $.fn.slick) {
$('.destination-main-slider').slick({
  centerMode: true,
  centerPadding: '0px',
  slidesToShow: 3,
  slidesToScroll: 1,

  infinite: true,
  loop: true,

  speed: 600,
  cssEase: 'ease-in-out',

  arrows: false,
  dots: false,

  autoplay: false,
  autoplaySpeed: 3000,

  pauseOnHover: true,

  responsive: [
    {
      breakpoint: 992,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
        centerMode: true,
        centerPadding: '0px'
      }
    }
  ]
});
}


/* --DESKTOP BUTTON CONTROLS---*/

$('.slider-prev').on('click', function(){
  if (!$('.destination-main-slider').hasClass('slick-initialized')) return;
  $('.destination-main-slider').slick('slickPrev');
});

$('.slider-next').on('click', function(){
  if (!$('.destination-main-slider').hasClass('slick-initialized')) return;
  $('.destination-main-slider').slick('slickNext');
});


/* --MOBILE BUTTON CONTROLS--*/

$('.mobile-prev').on('click', function(){
  if (!$('.destination-main-slider').hasClass('slick-initialized')) return;
  $('.destination-main-slider').slick('slickPrev');
});

$('.mobile-next').on('click', function(){
  if (!$('.destination-main-slider').hasClass('slick-initialized')) return;
  $('.destination-main-slider').slick('slickNext');
});

});
}

// 
  // Set default dates for availability
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        const checkInInput = document.getElementById('checkIn');
        const checkOutInput = document.getElementById('checkOut');
        
        if (checkInInput) {
            checkInInput.value = today.toISOString().split('T')[0];
        }
        if (checkOutInput) {
            checkOutInput.value = tomorrow.toISOString().split('T')[0];
        }
        
        // Book Now button
        document.getElementById('bookBtn')?.addEventListener('click', function() {
            const checkIn = document.getElementById('checkIn').value;
            const checkOut = document.getElementById('checkOut').value;
            window.location.href = `rooms.html?checkin=${checkIn}&checkout=${checkOut}`;
        });