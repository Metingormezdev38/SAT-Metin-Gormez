-- Üyelik tablosu ekleme migration
-- Bu dosyayı çalıştırarak mevcut veritabanınıza user_memberships tablosunu ekleyebilirsiniz

USE spor_salonu;

-- Kullanıcı üyelikleri tablosu
CREATE TABLE IF NOT EXISTS user_memberships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    membership_type ENUM('Temel', 'Premium', 'VIP') NOT NULL,
    membership_period ENUM('monthly', 'quarterly', 'yearly') NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_status (user_id, status),
    INDEX idx_end_date (end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

