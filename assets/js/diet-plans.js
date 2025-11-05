// Diyet Planları JavaScript

function showCreateDietPlanModal() {
    const modal = document.getElementById('diet-plan-modal');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Minimum tarihi bugün olarak ayarla
    const today = new Date().toISOString().slice(0, 10);
    document.getElementById('start_date').min = today;
    document.getElementById('end_date').min = today;
}

function closeDietPlanModal() {
    const modal = document.getElementById('diet-plan-modal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('diet-plan-form').reset();
}

document.addEventListener('DOMContentLoaded', function() {
    const dietPlanForm = document.getElementById('diet-plan-form');
    if (dietPlanForm) {
        dietPlanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleDietPlanCreation();
        });
    }

    // Modal dışına tıklanınca kapat
    const modal = document.getElementById('diet-plan-modal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeDietPlanModal();
            }
        });
    }

    // Bitiş tarihi başlangıç tarihinden sonra olmalı
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    if (startDate && endDate) {
        startDate.addEventListener('change', function() {
            endDate.min = this.value;
        });
    }
});

function handleDietPlanCreation() {
    const form = document.getElementById('diet-plan-form');
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;

    submitButton.disabled = true;
    submitButton.textContent = 'Oluşturuluyor...';

    fetch(SITE_URL + 'api/create-diet-plan.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('diet-plan-alert-container', data.message, 'success');
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1500);
        } else {
            showAlert('diet-plan-alert-container', data.message, 'error');
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('diet-plan-alert-container', 'Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    });
}

