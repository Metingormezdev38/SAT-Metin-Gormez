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

    // Kategoriye göre renk ve ikon belirle
    let bmiColor, bmiIcon, bmiGradient;
    if (data.bmi < 18.5) {
        bmiColor = '#4ade80'; // Yeşil
        bmiIcon = '📉';
        bmiGradient = 'linear-gradient(135deg, #4ade80 0%, #22c55e 100%)';
    } else if (data.bmi < 25) {
        bmiColor = '#FFD700'; // Altın
        bmiIcon = '✨';
        bmiGradient = 'linear-gradient(135deg, #FFD700 0%, #FFA500 100%)';
    } else if (data.bmi < 30) {
        bmiColor = '#FFA500'; // Turuncu
        bmiIcon = '⚡';
        bmiGradient = 'linear-gradient(135deg, #FFA500 0%, #ff8c00 100%)';
    } else {
        bmiColor = '#f44336'; // Kırmızı
        bmiIcon = '🔥';
        bmiGradient = 'linear-gradient(135deg, #f44336 0%, #d32f2f 100%)';
    }

    // BMI değerini formatla
    const formattedBMI = parseFloat(data.bmi).toFixed(2);
    
    // Sonuç kartını güncelle
    resultDiv.innerHTML = `
        <div class="bmi-result-card" style="--bmi-color: ${bmiColor}; --bmi-gradient: ${bmiGradient};">
            <div class="bmi-result-icon">${bmiIcon}</div>
            <div class="bmi-result-value" style="background: ${bmiGradient}; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                ${formattedBMI}
            </div>
            <div class="bmi-result-category">${data.category}</div>
            <div class="bmi-result-description">${data.description}</div>
        </div>
    `;

    resultDiv.classList.remove('hidden');
    resultDiv.style.animation = 'bmiFadeInUp 0.6s ease';
    
    // BMI'ye göre diyet planı önerisi göster
    showDietPlanSuggestion(data);
}

function showDietPlanSuggestion(data) {
    const resultDiv = document.getElementById('bmi-result');
    
    // Öneri kartını oluştur
    let suggestionDiv = document.getElementById('diet-plan-suggestion');
    if (!suggestionDiv) {
        suggestionDiv = document.createElement('div');
        suggestionDiv.id = 'diet-plan-suggestion';
        suggestionDiv.className = 'bmi-diet-suggestion-card';
        resultDiv.parentNode.insertBefore(suggestionDiv, resultDiv.nextSibling);
    }
    
    const form = document.getElementById('bmi-form');
    const height = parseFloat(document.getElementById('height').value);
    const weight = parseFloat(document.getElementById('weight').value);
    
    suggestionDiv.innerHTML = `
        <div class="diet-suggestion-header">
            <div class="diet-suggestion-icon">💡</div>
            <h3 class="diet-suggestion-title">BMI'nize Özel Diyet Planı</h3>
        </div>
        <p class="diet-suggestion-text">
            BMI değerinize göre size özel bir diyet planı oluşturabiliriz. 
            Bu plan, <strong>${data.category}</strong> kategorisindeki hedeflerinize uygun olarak hazırlanacaktır.
        </p>
        <button id="create-diet-plan-btn" class="diet-plan-create-btn">
            <span>OTOMATİK DİYET PLANI OLUŞTUR</span>
            <span class="btn-arrow">→</span>
        </button>
    `;
    
    suggestionDiv.style.animation = 'dietSuggestionFadeIn 0.8s ease 0.3s both';
    
    // Buton tıklama olayı
    document.getElementById('create-diet-plan-btn').addEventListener('click', function() {
        createDietPlanFromBMI(data.bmi, height, weight);
    });
}

function createDietPlanFromBMI(bmi, height, weight) {
    const btn = document.getElementById('create-diet-plan-btn');
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Oluşturuluyor...';
    
    const formData = new FormData();
    formData.append('bmi', bmi);
    formData.append('height', height);
    formData.append('weight', weight);
    
    fetch(SITE_URL + 'api/create-diet-plan-from-bmi.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Başarı mesajı göster
            const suggestionDiv = document.getElementById('diet-plan-suggestion');
            if (suggestionDiv) {
                suggestionDiv.innerHTML = `
                    <div class="diet-plan-success">
                        <div class="success-icon">✓</div>
                        <div class="success-message">${data.message}</div>
                        <div class="success-loading">Yönlendiriliyorsunuz...</div>
                    </div>
                `;
            }
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 2000);
        } else {
            showAlert('alert-container', data.message, 'error');
            btn.disabled = false;
            btn.textContent = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('alert-container', 'Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        btn.disabled = false;
        btn.textContent = originalText;
    });
}

