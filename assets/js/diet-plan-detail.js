// Diyet Planı Detay JavaScript

function showAddMealModal() {
    const modal = document.getElementById('meal-modal');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeMealModal() {
    const modal = document.getElementById('meal-modal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('meal-form').reset();
}

document.addEventListener('DOMContentLoaded', function() {
    const mealForm = document.getElementById('meal-form');
    if (mealForm) {
        mealForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleMealAddition();
        });
    }

    // Modal dışına tıklanınca kapat
    const modal = document.getElementById('meal-modal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeMealModal();
            }
        });
    }
});

function handleMealAddition() {
    const form = document.getElementById('meal-form');
    if (!form) {
        console.error('Form bulunamadı');
        showAlert('meal-alert-container', 'Form bulunamadı', 'error');
        return;
    }
    
    // Form validasyonu
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = new FormData(form);
    
    // Debug: Form verilerini kontrol et
    console.log('Form verileri:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
    
    const submitButton = document.getElementById('submit-meal-btn');
    const originalText = submitButton ? submitButton.textContent : 'Öğünü Onayla ve Ekle';

    if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = 'Ekleniyor...';
    }

    const apiUrl = SITE_URL + 'api/add-meal.php';
    console.log('API URL:', apiUrl);
    
    fetch(apiUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('API Response:', data);
        if (data.success) {
            showAlert('meal-alert-container', data.message || 'Öğün başarıyla eklendi', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            const errorMsg = data.message || 'Bir hata oluştu';
            console.error('API Error:', errorMsg);
            showAlert('meal-alert-container', errorMsg, 'error');
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('meal-alert-container', 'Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        }
    });
}

