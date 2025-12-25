// Admin Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    loadAdminStats();
    
    // Her 30 saniyede bir istatistikleri yenile
    setInterval(loadAdminStats, 30000);
});

function loadAdminStats() {
    fetch(SITE_URL + 'api/admin-stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.stats) {
                updateStats(data.stats);
            }
        })
        .catch(error => {
            console.error('Error loading stats:', error);
        });
}

function updateStats(stats) {
    // İstatistikleri güncelle
    const statElements = {
        'stat-total-users': stats.total_users || 0,
        'stat-total-consultants': stats.total_consultants || 0,
        'stat-total-diet-plans': stats.total_diet_plans || 0,
        'stat-total-bookings': stats.total_bookings || 0,
        'stat-active-users': stats.active_users || 0,
        'stat-new-users': stats.new_users_week || 0
    };

    Object.keys(statElements).forEach(elementId => {
        const element = document.getElementById(elementId);
        if (element) {
            const currentValue = parseInt(element.textContent) || 0;
            const newValue = statElements[elementId];
            
            if (currentValue !== newValue) {
                animateValue(elementId, currentValue, newValue, 500);
            }
        }
    });
}

