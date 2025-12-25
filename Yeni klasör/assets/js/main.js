// PowerFit Spor Salonu - Ana JavaScript Dosyası

// Sayfa yüklendiğinde animasyonları başlat
document.addEventListener('DOMContentLoaded', function() {
    // Scroll animasyonları
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Fade-in animasyonlu elementleri gözle
    document.querySelectorAll('.fade-in').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });

    // Navbar scroll efekti
    let lastScroll = 0;
    const navbar = document.querySelector('.header');
    
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 100) {
            navbar.style.boxShadow = '0 4px 20px rgba(255, 215, 0, 0.3)';
        } else {
            navbar.style.boxShadow = '0 4px 20px rgba(255, 215, 0, 0.1)';
        }
        
        lastScroll = currentScroll;
    });
});

// Alert gösterimi için yardımcı fonksiyon
function showAlert(containerId, message, type = 'info') {
    const container = document.getElementById(containerId);
    if (!container) return;

    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-error' : 'alert-info';
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${alertClass}`;
    alertDiv.textContent = message;
    
    container.innerHTML = '';
    container.appendChild(alertDiv);

    // 5 saniye sonra otomatik kaldır
    setTimeout(() => {
        alertDiv.style.opacity = '0';
        alertDiv.style.transition = 'opacity 0.3s ease';
        setTimeout(() => {
            alertDiv.remove();
        }, 300);
    }, 5000);
}

// Form gönderimi için genel Ajax fonksiyonu
function submitForm(formId, apiUrl, onSuccess) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;

        // Loading durumu
        submitButton.disabled = true;
        submitButton.textContent = 'Yükleniyor...';

        fetch(apiUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (onSuccess) {
                    onSuccess(data);
                } else if (data.redirect) {
                    window.location.href = data.redirect;
                } else if (data.reload) {
                    window.location.reload();
                } else {
                    showAlert(formId.replace('-form', '-alert-container') || 'alert-container', data.message, 'success');
                    form.reset();
                }
            } else {
                const alertContainer = formId.replace('-form', '-alert-container') || 'alert-container';
                showAlert(alertContainer, data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const alertContainer = formId.replace('-form', '-alert-container') || 'alert-container';
            showAlert(alertContainer, 'Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        });
    });
}

// Modal açma/kapama fonksiyonları
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Sayıları animasyonlu göster
function animateValue(elementId, start, end, duration) {
    const element = document.getElementById(elementId);
    if (!element) return;

    const range = end - start;
    const increment = end > start ? 1 : -1;
    const stepTime = Math.abs(Math.floor(duration / range));
    let current = start;

    const timer = setInterval(() => {
        current += increment;
        element.textContent = current;
        if (current === end) {
            clearInterval(timer);
        }
    }, stepTime);
}

