// BMI Hesaplayıcı JavaScript

document.addEventListener('DOMContentLoaded', function() {
    const bmiForm = document.getElementById('bmi-form');
    if (bmiForm) {
        bmiForm.addEventListener('submit', function(e) {
            e.preventDefault();
            calculateBMI();
        });
    }
});

function calculateBMI() {
    const form = document.getElementById('bmi-form');
    const height = parseFloat(document.getElementById('height').value);
    const weight = parseFloat(document.getElementById('weight').value);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;

    if (!height || !weight || height <= 0 || weight <= 0) {
        showAlert('alert-container', 'Lütfen geçerli boy ve kilo değerleri girin', 'error');
        return;
    }

    submitButton.disabled = true;
    submitButton.textContent = 'Hesaplanıyor...';

    const formData = new FormData(form);

    fetch(SITE_URL + 'api/calculate-bmi.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayBMIResult(data);
        } else {
            showAlert('alert-container', data.message, 'error');
        }
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('alert-container', 'Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    });
}

function displayBMIResult(data) {
    const resultDiv = document.getElementById('bmi-result');
    const bmiValue = document.getElementById('bmi-value');
    const bmiCategory = document.getElementById('bmi-category');
    const bmiDescription = document.getElementById('bmi-description');

    bmiValue.textContent = data.bmi;
    bmiCategory.textContent = data.category;
    bmiDescription.textContent = data.description;

    // Kategoriye göre renk
    if (data.bmi < 18.5) {
        bmiValue.style.color = '#4CAF50'; // Yeşil
    } else if (data.bmi < 25) {
        bmiValue.style.color = '#FFD700'; // Sarı
    } else if (data.bmi < 30) {
        bmiValue.style.color = '#FFA500'; // Turuncu
    } else {
        bmiValue.style.color = '#f44336'; // Kırmızı
    }

    resultDiv.classList.remove('hidden');
    resultDiv.style.animation = 'fadeInUp 0.5s ease';
}

