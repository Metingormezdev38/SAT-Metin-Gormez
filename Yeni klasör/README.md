# PowerFit Spor Salonu Web Sitesi

Modern, responsive ve modüler yapıda geliştirilmiş spor salonu yönetim sistemi.

## Özellikler

- ✅ Kullanıcı giriş ve kayıt sistemi
- ✅ Admin paneli (Ajax ile dinamik)
- ✅ Diyet listesi oluşturma ve yönetimi
- ✅ Danışman seçme ve randevu alma
- ✅ BMI (Vücut Kitle İndeksi) hesaplama
- ✅ Kullanıcı profil yönetimi
- ✅ Responsive tasarım (mobil uyumlu)
- ✅ Siyah-sarı tema ile modern tasarım
- ✅ Animasyonlar ve efektler
- ✅ PDO ile güvenli veritabanı bağlantısı

## Kurulum

### 1. Veritabanı Kurulumu

1. XAMPP'ı başlatın ve phpMyAdmin'e gidin
2. `database/schema.sql` dosyasını phpMyAdmin'de çalıştırın
3. Veritabanı oluşturulacak ve örnek veriler eklenecektir

### 2. Veritabanı Ayarları

`config/database.php` dosyasında veritabanı bilgilerini kontrol edin:
- Host: `localhost`
- Veritabanı: `spor_salonu`
- Kullanıcı: `root`
- Şifre: `` (boş)

### 3. Site URL Ayarları

`config/config.php` dosyasında `SITE_URL` değerini kontrol edin:
```php
define('SITE_URL', 'http://localhost/');
```

### 4. Varsayılan Giriş Bilgileri

**Admin:**
- Kullanıcı Adı: `admin`
- Şifre: `admin123`

**Danışman (Örnek):**
- Kullanıcı Adı: `danisman1`
- Şifre: `admin123`

## Dosya Yapısı

```
/
├── index.php              # Ana sayfa
├── config/                # Yapılandırma dosyaları
│   ├── config.php
│   └── database.php
├── includes/              # Header ve footer modülleri
│   ├── header.php
│   └── footer.php
├── assets/                # Statik dosyalar
│   ├── css/
│   │   └── style.css      # Ana CSS dosyası
│   ├── js/
│   │   ├── main.js        # Genel JavaScript
│   │   ├── auth.js        # Giriş/kayıt
│   │   ├── profile.js     # Profil güncelleme
│   │   ├── bmi.js         # BMI hesaplama
│   │   ├── consultants.js # Danışman işlemleri
│   │   ├── diet-plans.js  # Diyet planları
│   │   ├── diet-plan-detail.js
│   │   ├── admin-auth.js  # Admin giriş
│   │   └── admin-dashboard.js
│   └── images/
├── user/                  # Kullanıcı sayfaları
│   ├── login.php
│   ├── register.php
│   ├── dashboard.php
│   ├── profile.php
│   ├── bmi-calculator.php
│   ├── consultants.php
│   ├── diet-plans.php
│   └── diet-plan-detail.php
├── admin/                 # Admin sayfaları
│   ├── login.php
│   └── dashboard.php
├── api/                   # API endpoint'leri
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│   ├── update-profile.php
│   ├── calculate-bmi.php
│   ├── book-consultant.php
│   ├── create-diet-plan.php
│   ├── add-meal.php
│   ├── admin-login.php
│   └── admin-stats.php
└── database/
    └── schema.sql         # Veritabanı şeması
```

## Kullanım

1. Tarayıcınızda `http://localhost/` adresine gidin
2. Yeni kullanıcı kaydı oluşturun veya admin olarak giriş yapın
3. Diyet planları oluşturabilir, danışman seçebilir ve BMI hesaplayabilirsiniz

## Teknolojiler

- **Backend:** PHP 7.4+
- **Veritabanı:** MySQL (PDO)
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **CSS Framework:** Tailwind CSS (CDN)
- **Tasarım:** Özel CSS (Siyah-Sarı tema)

## Notlar

- Tüm CSS kodları `assets/css/style.css` dosyasında toplanmıştır (satır içi CSS yok)
- Tüm JavaScript kodları modüler yapıda ayrı dosyalarda organize edilmiştir
- PDO prepared statements kullanılarak SQL injection koruması sağlanmıştır
- Responsive tasarım ile tüm cihazlarda uyumlu çalışır

## Lisans

Bu proje eğitim amaçlı geliştirilmiştir.

