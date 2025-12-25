// Hero Slider JavaScript - Otomatik kaydırma

document.addEventListener('DOMContentLoaded', function() {
    const slider = document.querySelector('.hero-slider-container');
    if (!slider) return;
    
    const slides = slider.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.slider-dot');
    const prevBtn = document.querySelector('.slider-prev');
    const nextBtn = document.querySelector('.slider-next');
    
    if (slides.length <= 1) return; // Tek görsel varsa slider çalışmasın
    
    let currentSlide = 0;
    let autoSlideInterval;
    const slideInterval = 5000; // 5 saniyede bir değişsin
    
    // Aktif slide'ı göster
    function showSlide(index) {
        // Index'i sınırla
        if (index >= slides.length) {
            currentSlide = 0;
        } else if (index < 0) {
            currentSlide = slides.length - 1;
        } else {
            currentSlide = index;
        }
        
        // Tüm slide'ları gizle
        slides.forEach((slide, i) => {
            slide.classList.remove('active');
            if (dots[i]) {
                dots[i].classList.remove('active');
            }
        });
        
        // Aktif slide'ı göster
        slides[currentSlide].classList.add('active');
        if (dots[currentSlide]) {
            dots[currentSlide].classList.add('active');
        }
    }
    
    // Sonraki slide'a geç
    function nextSlide() {
        showSlide(currentSlide + 1);
    }
    
    // Önceki slide'a geç
    function prevSlide() {
        showSlide(currentSlide - 1);
    }
    
    // Otomatik kaydırmayı başlat
    function startAutoSlide() {
        autoSlideInterval = setInterval(nextSlide, slideInterval);
    }
    
    // Otomatik kaydırmayı durdur
    function stopAutoSlide() {
        if (autoSlideInterval) {
            clearInterval(autoSlideInterval);
        }
    }
    
    // Dot'lara tıklama
    dots.forEach((dot, index) => {
        dot.addEventListener('click', function() {
            stopAutoSlide();
            showSlide(index);
            startAutoSlide();
        });
    });
    
    // Önceki buton
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            stopAutoSlide();
            prevSlide();
            startAutoSlide();
        });
    }
    
    // Sonraki buton
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            stopAutoSlide();
            nextSlide();
            startAutoSlide();
        });
    }
    
    // Mouse slider üzerindeyken otomatik kaydırmayı durdur
    const sliderContainer = document.querySelector('.hero-slider');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', stopAutoSlide);
        sliderContainer.addEventListener('mouseleave', startAutoSlide);
    }
    
    // Klavye ile kontrol (opsiyonel)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            stopAutoSlide();
            prevSlide();
            startAutoSlide();
        } else if (e.key === 'ArrowRight') {
            stopAutoSlide();
            nextSlide();
            startAutoSlide();
        }
    });
    
    // İlk slide'ı göster ve otomatik kaydırmayı başlat
    showSlide(0);
    startAutoSlide();
    
    // Sayfa görünürlüğü değiştiğinde otomatik kaydırmayı kontrol et
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopAutoSlide();
        } else {
            startAutoSlide();
        }
    });
});

