// API Base URL
const API_BASE_URL = 'http://localhost:3000/api';

// Helper function to get auth token from session
function getAuthToken() {
    // PHP session'dan token'ı almak için cookie'den okuma yapılabilir
    // Şimdilik localStorage kullanıyoruz
    return localStorage.getItem('auth_token');
}

// Helper function to make API calls
async function apiCall(endpoint, method = 'GET', data = null) {
    const url = API_BASE_URL + endpoint;
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        }
    };

    const token = getAuthToken();
    if (token) {
        options.headers['Authorization'] = 'Bearer ' + token;
    }

    if (data && (method === 'POST' || method === 'PUT')) {
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(url, options);
        const result = await response.json();
        return {
            status: response.status,
            data: result
        };
    } catch (error) {
        console.error('API Error:', error);
        return {
            status: 500,
            data: { success: false, message: 'Bağlantı hatası' }
        };
    }
}

// Show error message
function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    const form = document.querySelector('.form-container') || document.querySelector('main');
    form.insertBefore(errorDiv, form.firstChild);
    
    setTimeout(() => {
        errorDiv.remove();
    }, 5000);
}

// Show success message
function showSuccess(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.textContent = message;
    const form = document.querySelector('.form-container') || document.querySelector('main');
    form.insertBefore(successDiv, form.firstChild);
    
    setTimeout(() => {
        successDiv.remove();
    }, 5000);
}

// Load classes
async function loadClasses() {
    const response = await apiCall('/classes');
    if (response.status === 200 && response.data.success) {
        displayClasses(response.data.data);
    } else {
        showError('Dersler yüklenemedi');
    }
}

// Display classes in grid
function displayClasses(classes) {
    const container = document.getElementById('classes-container');
    if (!container) return;

    if (classes.length === 0) {
        container.innerHTML = '<p>Henüz ders bulunmamaktadır.</p>';
        return;
    }

    container.innerHTML = classes.map(classItem => `
        <div class="class-card">
            <h3>${classItem.name}</h3>
            <p class="instructor">Eğitmen: ${classItem.instructor}</p>
            <p class="schedule">${classItem.day} - ${classItem.time}</p>
            <p class="capacity">Kapasite: ${classItem.currentBookings}/${classItem.maxCapacity}</p>
            ${getAuthToken() ? `
                <button class="btn" onclick="makeReservation('${classItem._id}')">Rezervasyon Yap</button>
            ` : `
                <p style="color: var(--text-light); font-size: 0.9rem;">Rezervasyon yapmak için giriş yapın</p>
            `}
        </div>
    `).join('');
}

// Make reservation
async function makeReservation(classId) {
    if (!getAuthToken()) {
        showError('Rezervasyon yapmak için giriş yapmanız gerekiyor');
        window.location.href = 'login.php';
        return;
    }

    const reservationDate = prompt('Rezervasyon tarihi (YYYY-MM-DD formatında):');
    if (!reservationDate) return;

    const response = await apiCall('/reservations', 'POST', {
        classId: classId,
        reservationDate: reservationDate
    });

    if (response.status === 201 && response.data.success) {
        showSuccess('Rezervasyon başarıyla oluşturuldu!');
        if (window.location.pathname.includes('reservations.php')) {
            loadReservations();
        }
    } else {
        showError(response.data.message || 'Rezervasyon oluşturulamadı');
    }
}

// Load reservations
async function loadReservations() {
    const response = await apiCall('/reservations/my-reservations');
    if (response.status === 200 && response.data.success) {
        displayReservations(response.data.data);
    } else {
        showError('Rezervasyonlar yüklenemedi');
    }
}

// Display reservations
function displayReservations(reservations) {
    const container = document.getElementById('reservations-container');
    if (!container) return;

    if (reservations.length === 0) {
        container.innerHTML = '<p>Henüz rezervasyonunuz bulunmamaktadır.</p>';
        return;
    }

    container.innerHTML = reservations.map(reservation => {
        const date = new Date(reservation.reservationDate);
        const classData = reservation.classId;
        return `
            <div class="reservation-item">
                <div class="reservation-info">
                    <h4>${classData.name}</h4>
                    <p>Eğitmen: ${classData.instructor}</p>
                    <p>Tarih: ${date.toLocaleDateString('tr-TR')}</p>
                    <p>Gün: ${classData.day} - ${classData.time}</p>
                </div>
                <button class="btn-cancel" onclick="cancelReservation('${reservation._id}')">İptal Et</button>
            </div>
        `;
    }).join('');
}

// Cancel reservation
async function cancelReservation(reservationId) {
    if (!confirm('Rezervasyonu iptal etmek istediğinize emin misiniz?')) {
        return;
    }

    const response = await apiCall(`/reservations/${reservationId}`, 'DELETE');
    if (response.status === 200 && response.data.success) {
        showSuccess('Rezervasyon iptal edildi');
        loadReservations();
    } else {
        showError(response.data.message || 'Rezervasyon iptal edilemedi');
    }
}

// Calculate BMI
async function calculateBMI() {
    const height = parseFloat(document.getElementById('height').value);
    const weight = parseFloat(document.getElementById('weight').value);

    if (!height || !weight || height < 50 || height > 250 || weight < 20 || weight > 300) {
        showError('Lütfen geçerli boy (50-250 cm) ve kilo (20-300 kg) değerleri girin');
        return;
    }

    const token = getAuthToken();
    const endpoint = token ? '/bmi/calculate-and-save' : '/bmi/calculate';
    
    const response = await apiCall(endpoint, 'POST', { height, weight });
    
    if (response.status === 200 && response.data.success) {
        displayBMIResult(response.data.data);
    } else {
        showError(response.data.message || 'BMI hesaplanamadı');
    }
}

// Display BMI result
function displayBMIResult(data) {
    const resultDiv = document.getElementById('bmi-result');
    if (!resultDiv) return;

    resultDiv.innerHTML = `
        <div class="bmi-result">
            <h3>BMI Sonucunuz</h3>
            <div class="bmi-value">${data.bmi}</div>
            <div class="bmi-category">${data.category}</div>
            <p class="bmi-description">${data.description}</p>
            <p style="margin-top: 1rem; color: var(--text-light);">
                Boy: ${data.height} cm | Kilo: ${data.weight} kg
            </p>
        </div>
    `;
    resultDiv.style.display = 'block';
}

// Load membership packages
async function loadMemberships() {
    const response = await apiCall('/memberships/packages');
    if (response.status === 200 && response.data.success) {
        displayMemberships(response.data.data);
    } else {
        showError('Üyelik paketleri yüklenemedi');
    }
}

// Display membership packages
function displayMemberships(packages) {
    const container = document.getElementById('memberships-container');
    if (!container) return;

    container.innerHTML = packages.map(pkg => `
        <div class="membership-card ${pkg.id === 'premium' ? 'featured' : ''}">
            <h3>${pkg.name}</h3>
            <div class="membership-price">
                ${pkg.price}₺ <span>/ay</span>
            </div>
            <ul class="membership-features">
                ${pkg.features.map(feature => `<li>${feature}</li>`).join('')}
            </ul>
            ${getAuthToken() ? `
                <button class="btn" onclick="purchaseMembership('${pkg.id}')">Satın Al</button>
            ` : `
                <p style="color: var(--text-light); margin-top: 1rem;">Satın almak için giriş yapın</p>
            `}
        </div>
    `).join('');
}

// Purchase membership
async function purchaseMembership(membershipType) {
    if (!getAuthToken()) {
        showError('Üyelik satın almak için giriş yapmanız gerekiyor');
        window.location.href = 'login.php';
        return;
    }

    if (!confirm('Bu üyelik paketini satın almak istediğinize emin misiniz?')) {
        return;
    }

    const response = await apiCall('/memberships/purchase', 'POST', { membershipType });
    if (response.status === 200 && response.data.success) {
        showSuccess('Üyelik başarıyla satın alındı!');
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    } else {
        showError(response.data.message || 'Üyelik satın alınamadı');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load classes if classes page
    if (document.getElementById('classes-container')) {
        loadClasses();
    }

    // Load reservations if reservations page
    if (document.getElementById('reservations-container')) {
        loadReservations();
    }

    // Load memberships if memberships page
    if (document.getElementById('memberships-container')) {
        loadMemberships();
    }
});
