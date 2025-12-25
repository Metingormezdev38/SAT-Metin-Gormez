// Auth Modal JavaScript

// Modal açma fonksiyonu
function openAuthModal(type = 'login') {
    const modal = document.getElementById('authModal');
    const loginModal = document.getElementById('loginModal');
    const registerModal = document.getElementById('registerModal');
    
    if (!modal || !loginModal || !registerModal) return;
    
    // Önce tüm formları gizle
    loginModal.style.display = 'none';
    registerModal.style.display = 'none';
    
    // Seçilen formu göster
    if (type === 'login') {
        loginModal.style.display = 'block';
    } else if (type === 'register') {
        registerModal.style.display = 'block';
    }
    
    // Modal'ı göster
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Form alanlarına odaklan
    setTimeout(() => {
        if (type === 'login') {
            document.getElementById('modal-username')?.focus();
        } else {
            document.getElementById('modal-first_name')?.focus();
        }
    }, 100);
}

// Modal kapatma fonksiyonu
function closeAuthModal() {
    const modal = document.getElementById('authModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        
        // Formları temizle
        const loginForm = document.getElementById('modal-login-form');
        const registerForm = document.getElementById('modal-register-form');
        if (loginForm) loginForm.reset();
        if (registerForm) registerForm.reset();
        
        // Alert'leri temizle
        const loginAlert = document.getElementById('login-alert-container');
        const registerAlert = document.getElementById('register-alert-container');
        if (loginAlert) loginAlert.innerHTML = '';
        if (registerAlert) registerAlert.innerHTML = '';
    }
}

// Modal formlar arası geçiş
function switchAuthModal(type) {
    const loginModal = document.getElementById('loginModal');
    const registerModal = document.getElementById('registerModal');
    
    // Alert'leri temizle
    const loginAlert = document.getElementById('login-alert-container');
    const registerAlert = document.getElementById('register-alert-container');
    if (loginAlert) loginAlert.innerHTML = '';
    if (registerAlert) registerAlert.innerHTML = '';
    
    if (type === 'login') {
        loginModal.style.display = 'block';
        registerModal.style.display = 'none';
        setTimeout(() => {
            document.getElementById('modal-username')?.focus();
        }, 100);
    } else if (type === 'register') {
        registerModal.style.display = 'block';
        loginModal.style.display = 'none';
        setTimeout(() => {
            document.getElementById('modal-first_name')?.focus();
        }, 100);
    }
}

// Modal dışına tıklanınca kapat
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('authModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeAuthModal();
            }
        });
    }
    
    // ESC tuşu ile kapat
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('authModal');
            if (modal && modal.style.display === 'flex') {
                closeAuthModal();
            }
        }
    });
    
    // Modal login formu
    const modalLoginForm = document.getElementById('modal-login-form');
    if (modalLoginForm) {
        modalLoginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleModalLogin();
        });
    }
    
    // Modal register formu
    const modalRegisterForm = document.getElementById('modal-register-form');
    if (modalRegisterForm) {
        modalRegisterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleModalRegister();
        });
    }
});

// Modal login işlemi
function handleModalLogin() {
    const form = document.getElementById('modal-login-form');
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    submitButton.disabled = true;
    submitButton.innerHTML = '<span>Giriş yapılıyor...</span>';

    fetch(SITE_URL + 'api/login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('login-alert-container', data.message, 'success');
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1000);
        } else {
            showAlert('login-alert-container', data.message, 'error');
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('login-alert-container', 'Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    });
}

// Modal register işlemi
function handleModalRegister() {
    const form = document.getElementById('modal-register-form');
    const password = document.getElementById('modal-register-password').value;
    const passwordConfirm = document.getElementById('modal-password_confirm').value;

    // Şifre kontrolü
    if (password !== passwordConfirm) {
        showAlert('register-alert-container', 'Şifreler eşleşmiyor', 'error');
        return;
    }

    if (password.length < 6) {
        showAlert('register-alert-container', 'Şifre en az 6 karakter olmalıdır', 'error');
        return;
    }

    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    submitButton.disabled = true;
    submitButton.innerHTML = '<span>Kayıt yapılıyor...</span>';

    fetch(SITE_URL + 'api/register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('register-alert-container', data.message, 'success');
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 2000);
        } else {
            showAlert('register-alert-container', data.message, 'error');
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('register-alert-container', 'Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    });
}

