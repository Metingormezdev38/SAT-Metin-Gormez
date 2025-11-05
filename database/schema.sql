-- Spor Salonu Veritabanı Şeması

CREATE DATABASE IF NOT EXISTS spor_salonu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE spor_salonu;

-- Kullanıcılar tablosu
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    role ENUM('user', 'admin', 'consultant') DEFAULT 'user',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kullanıcı profilleri
CREATE TABLE IF NOT EXISTS user_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    height DECIMAL(5,2) COMMENT 'Boy (cm)',
    weight DECIMAL(5,2) COMMENT 'Kilo (kg)',
    age INT,
    gender ENUM('male', 'female', 'other'),
    activity_level ENUM('sedentary', 'light', 'moderate', 'active', 'very_active') DEFAULT 'moderate',
    goal ENUM('weight_loss', 'muscle_gain', 'maintenance', 'endurance') DEFAULT 'maintenance',
    bmi DECIMAL(4,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Danışmanlar
CREATE TABLE IF NOT EXISTS consultants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    specialization VARCHAR(100),
    experience_years INT,
    bio TEXT,
    rating DECIMAL(3,2) DEFAULT 0.00,
    price_per_session DECIMAL(10,2),
    status ENUM('available', 'busy', 'unavailable') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Danışman seçimleri
CREATE TABLE IF NOT EXISTS consultant_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    consultant_id INT NOT NULL,
    booking_date DATETIME NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (consultant_id) REFERENCES consultants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Diyet listeleri
CREATE TABLE IF NOT EXISTS diet_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    consultant_id INT,
    plan_name VARCHAR(100) NOT NULL,
    daily_calories INT,
    description TEXT,
    start_date DATE,
    end_date DATE,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (consultant_id) REFERENCES consultants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Diyet planı öğünleri
CREATE TABLE IF NOT EXISTS diet_meals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    diet_plan_id INT NOT NULL,
    meal_type ENUM('breakfast', 'lunch', 'dinner', 'snack') NOT NULL,
    meal_name VARCHAR(100) NOT NULL,
    calories INT,
    protein DECIMAL(5,2),
    carbs DECIMAL(5,2),
    fat DECIMAL(5,2),
    description TEXT,
    day_of_week INT COMMENT '1=Monday, 7=Sunday',
    FOREIGN KEY (diet_plan_id) REFERENCES diet_plans(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan admin kullanıcı (şifre: admin123)
-- NOT: Şifre hash'leri setup-passwords.php scripti ile güncellenmelidir
-- İlk kurulumda şifreler: admin123
INSERT INTO users (username, email, password, first_name, last_name, role) VALUES
('admin', 'admin@sporsalonu.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68mmIu98hE2e', 'Admin', 'User', 'admin');

-- Örnek danışman kullanıcıları
INSERT INTO users (username, email, password, first_name, last_name, role) VALUES
('danisman1', 'danisman1@sporsalonu.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68mmIu98hE2e', 'Mehmet', 'Yılmaz', 'consultant'),
('danisman2', 'danisman2@sporsalonu.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68mmIu98hE2e', 'Ayşe', 'Demir', 'consultant');

-- Danışman profilleri
INSERT INTO consultants (user_id, specialization, experience_years, bio, rating, price_per_session) VALUES
(2, 'Beslenme Uzmanı', 5, '5 yıllık tecrübeye sahip beslenme uzmanı. Kilo verme ve kas geliştirme konularında uzman.', 4.8, 500.00),
(3, 'Fitness Koçu', 8, '8 yıllık tecrübeye sahip fitness koçu. Kişisel antrenman ve program hazırlama konularında uzman.', 4.9, 600.00);

