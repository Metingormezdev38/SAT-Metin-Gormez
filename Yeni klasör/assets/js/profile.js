// Profil Güncelleme JavaScript

document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleProfileUpdate();
        });
    }
});

function handleProfileUpdate() {
    const form = document.getElementById('profile-form');
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;

    submitButton.disabled = true;
    submitButton.textContent = 'Kaydediliyor...';

    fetch(SITE_URL + 'api/update-profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const message = data.message || 'Profil bilgileriniz kaydedildi.';
            showAlert('alert-container', message, 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
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

