// Kullanıcı Giriş ve Kayıt JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Giriş formu
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleLogin();
        });
    }

    // Kayıt formu
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleRegister();
        });
    }
});

function handleLogin() {
    const form = document.getElementById('login-form');
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;

    submitButton.disabled = true;
    submitButton.textContent = 'Giriş yapılıyor...';

    fetch(SITE_URL + 'api/login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('alert-container', data.message, 'success');
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1000);
        } else {
            showAlert('alert-container', data.message, 'error');
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('alert-container', 'Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    });
}

function handleRegister() {
    const form = document.getElementById('register-form');
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;

    // Şifre kontrolü
    if (password !== passwordConfirm) {
        showAlert('alert-container', 'Şifreler eşleşmiyor', 'error');
        return;
    }

    if (password.length < 6) {
        showAlert('alert-container', 'Şifre en az 6 karakter olmalıdır', 'error');
        return;
    }

    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;

    submitButton.disabled = true;
    submitButton.textContent = 'Kayıt yapılıyor...';

    fetch(SITE_URL + 'api/register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('alert-container', data.message, 'success');
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 2000);
        } else {
            showAlert('alert-container', data.message, 'error');
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('alert-container', 'Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    });
}

