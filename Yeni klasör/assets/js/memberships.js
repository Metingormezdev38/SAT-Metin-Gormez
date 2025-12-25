// Üyelikler Sayfası JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Süre seçimi değiştiğinde fiyatı güncelle
    const periodOptions = document.querySelectorAll('.period-option');
    periodOptions.forEach(option => {
        option.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                
                // Tüm seçenekleri güncelle
                const membershipCard = this.closest('.membership-card');
                const allOptions = membershipCard.querySelectorAll('.period-option');
                allOptions.forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');
                
                // Fiyat ve süre bilgisini güncelle
                const membershipId = membershipCard.querySelector('.membership-btn')?.getAttribute('data-membership-id');
                if (membershipId) {
                    const price = parseFloat(this.getAttribute('data-price'));
                    const period = this.getAttribute('data-period');
                    
                    const priceElement = document.getElementById(`price-${membershipId}`);
                    const periodElement = document.getElementById(`period-${membershipId}`);
                    const button = membershipCard.querySelector('.membership-btn[data-membership-id]');
                    
                    if (priceElement) {
                        priceElement.textContent = price.toLocaleString('tr-TR') + ' ₺';
                    }
                    
                    if (periodElement) {
                        const periodTexts = {
                            'monthly': '/ aylık',
                            'quarterly': '/ 3 aylık',
                            'yearly': '/ 1 yıllık'
                        };
                        periodElement.textContent = periodTexts[period] || '/ aylık';
                    }
                    
                    if (button) {
                        button.setAttribute('data-membership-price', price);
                        button.setAttribute('data-membership-period', period);
                    }
                }
            }
        });
    });
    
    // Paket seç butonları
    const membershipButtons = document.querySelectorAll('.membership-btn[data-membership-id]');
    
    membershipButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const membershipId = this.getAttribute('data-membership-id');
            const membershipName = this.getAttribute('data-membership-name');
            const membershipPrice = this.getAttribute('data-membership-price');
            const membershipPeriod = this.getAttribute('data-membership-period') || 'monthly';
            
            const periodTexts = {
                'monthly': 'Aylık',
                'quarterly': '3 Aylık',
                'yearly': '1 Yıllık'
            };
            
            // Kullanıcıya onay mesajı göster
            const confirmMessage = `${membershipName} paketini (${periodTexts[membershipPeriod]}) seçmek istediğinizden emin misiniz?\n\nFiyat: ${parseFloat(membershipPrice).toLocaleString('tr-TR')} ₺`;
            
            if (confirm(confirmMessage)) {
                // Ödeme sayfasına yönlendir
                const params = new URLSearchParams({
                    membership_id: membershipId,
                    membership_name: membershipName,
                    membership_price: membershipPrice,
                    membership_period: membershipPeriod
                });
                window.location.href = `${SITE_URL}user/payment.php?${params.toString()}`;
            }
        });
    });
});

function showAlert(title, message, type) {
    const alertContainer = document.getElementById('alert-container') || createAlertContainer();
    
    const alertClass = type === 'success' ? 'form-success' : 'form-error';
    const alertHTML = `
        <div class="${alertClass}" style="margin-bottom: 1rem;">
            <strong>${title}:</strong> ${message}
        </div>
    `;
    
    alertContainer.innerHTML = alertHTML;
    alertContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    
    // 5 saniye sonra otomatik kapat
    setTimeout(() => {
        alertContainer.innerHTML = '';
    }, 5000);
}

function createAlertContainer() {
    const container = document.createElement('div');
    container.id = 'alert-container';
    container.style.position = 'fixed';
    container.style.top = '100px';
    container.style.right = '20px';
    container.style.zIndex = '10000';
    container.style.maxWidth = '400px';
    document.body.appendChild(container);
    return container;
}

