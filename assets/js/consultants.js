// Danışman Randevu JavaScript

// Danışman bilgilerini sakla
const consultantData = {};

function bookConsultant(consultantId, consultantName, consultantSpecialization) {
    // Modal'ı aç
    showBookingModal();
    
    // Dropdown'da danışmanı seç
    const consultantSelect = document.getElementById('consultant_select');
    if (consultantSelect && consultantId) {
        consultantSelect.value = consultantId;
        // Change event'ini tetikle
        consultantSelect.dispatchEvent(new Event('change'));
    }
}

// Danışman seçimi için direkt modal açma fonksiyonu
function openBookingModal() {
    showBookingModal();
}

function showBookingModal() {
    const modal = document.getElementById('booking-modal');
    if (!modal) return;
    
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Minimum tarihi bugün olarak ayarla
    const today = new Date().toISOString().slice(0, 16);
    const bookingDateInput = document.getElementById('booking_date');
    if (bookingDateInput) {
        bookingDateInput.min = today;
        
        // Eğer tarih seçilmemişse, bugünden 1 saat sonrasını varsayılan yap
        if (!bookingDateInput.value) {
            const tomorrow = new Date();
            tomorrow.setHours(tomorrow.getHours() + 1);
            bookingDateInput.value = tomorrow.toISOString().slice(0, 16);
        }
    }
}

function closeBookingModal() {
    const modal = document.getElementById('booking-modal');
    if (!modal) return;
    
    modal.classList.add('hidden');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
    
    // Form'u resetle
    const bookingForm = document.getElementById('booking-form');
    const consultantSelect = document.getElementById('consultant_select');
    const detailsDiv = document.getElementById('selected-consultant-details');
    
    if (bookingForm) {
        bookingForm.reset();
    }
    
    if (consultantSelect) {
        consultantSelect.value = '';
    }
    
    // Danışman detaylarını gizle
    if (detailsDiv) {
        detailsDiv.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Tüm "Randevu Al" butonlarına event listener ekle
    const bookButtons = document.querySelectorAll('.book-consultant-btn');
    bookButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Danışman kartını bul
            const consultantCard = button.closest('.consultant-card');
            if (consultantCard) {
                const consultantId = consultantCard.getAttribute('data-consultant-id');
                const consultantName = consultantCard.getAttribute('data-consultant-name') || '';
                const consultantSpecialization = consultantCard.getAttribute('data-consultant-specialization') || '';
                
                // Danışman bilgileriyle modal'ı aç
                bookConsultant(consultantId, consultantName, consultantSpecialization);
            }
        });
    });
    
    // Danışman dropdown değişikliği
    const consultantSelect = document.getElementById('consultant_select');
    if (consultantSelect) {
        consultantSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const consultantId = this.value;
            const consultantName = selectedOption.getAttribute('data-name') || '';
            const consultantSpecialization = selectedOption.getAttribute('data-specialization') || '';
            
            // Danışman detaylarını göster
            const detailsDiv = document.getElementById('selected-consultant-details');
            const detailDisplay = document.getElementById('consultant-detail-display');
            
            if (consultantId && detailsDiv && detailDisplay) {
                const detailText = consultantName + (consultantSpecialization ? ' - ' + consultantSpecialization : '');
                detailDisplay.textContent = detailText;
                detailsDiv.style.display = 'block';
                
                // Data'yı sakla
                consultantData.id = consultantId;
                consultantData.name = consultantName;
                consultantData.specialization = consultantSpecialization;
            } else if (detailsDiv) {
                detailsDiv.style.display = 'none';
            }
        });
    }
    
    const bookingForm = document.getElementById('booking-form');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleBooking();
        });
    }

    // Modal dışına tıklanınca kapat
    const modal = document.getElementById('booking-modal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeBookingModal();
            }
        });
    }
});

function handleBooking() {
    const form = document.getElementById('booking-form');
    const consultantSelect = document.getElementById('consultant_select');
    const consultantId = consultantSelect ? consultantSelect.value : '';
    const bookingDate = document.getElementById('booking_date').value;
    
    // Validasyon
    if (!consultantId || consultantId === '' || consultantId === '0') {
        showAlert('booking-alert-container', 'Lütfen bir danışman seçin', 'error');
        if (consultantSelect) consultantSelect.focus();
        return;
    }
    
    if (!bookingDate) {
        showAlert('booking-alert-container', 'Lütfen randevu tarihi ve saatini seçin', 'error');
        return;
    }
    
    // Tarih kontrolü - geçmiş tarih olmamalı
    const selectedDate = new Date(bookingDate);
    const now = new Date();
    if (selectedDate < now) {
        showAlert('booking-alert-container', 'Geçmiş bir tarih seçemezsiniz', 'error');
        return;
    }
    
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;

    submitButton.disabled = true;
    submitButton.textContent = 'Randevu alınıyor...';

    fetch(SITE_URL + 'api/book-consultant.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('booking-alert-container', data.message, 'success');
            setTimeout(() => {
                closeBookingModal();
                window.location.reload();
            }, 2000);
        } else {
            showAlert('booking-alert-container', data.message, 'error');
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('booking-alert-container', 'Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    });
}

