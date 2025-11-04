# Spor Salonu Web Sitesi - Hibrit Mimari

Profesyonel spor salonu web sitesi projesi. PHP frontend ve Node.js API backend ile hibrit mimari kullanılmıştır.

## 🎨 Tasarım

- **Tema**: Siyah ve Sarı kurumsal renk paleti
- **Responsive**: Mobil uyumlu tasarım
- **Modern UI**: Kullanıcı dostu arayüz

## 🏗️ Mimari

- **Frontend**: PHP (Sunucu tarafı render)
- **Backend API**: Node.js (Express.js)
- **Veritabanı**: PostgreSQL (SQL)
- **ORM**: Sequelize
- **Styling**: CSS3 (Siyah-Sarı tema)

## ✨ Özellikler

### Üyelik Yönetimi
- ✅ Kullanıcı kayıt sistemi
- ✅ Giriş/Çıkış sistemi
- ✅ JWT token tabanlı kimlik doğrulama
- ✅ Üyelik paketleri (Temel, Premium, VIP)

### Ders Yönetimi
- ✅ Ders programı görüntüleme
- ✅ Online rezervasyon sistemi
- ✅ Rezervasyon iptal etme
- ✅ Kapasite takibi

### BMI Hesaplayıcı
- ✅ Vücut Kitle Endeksi hesaplama
- ✅ Kategori belirleme (Zayıf, Normal, Fazla Kilolu, Obez)
- ✅ Kullanıcı bilgilerini kaydetme (giriş yapmış kullanıcılar için)

### İletişim
- ✅ İletişim formu
- ✅ Harita konumu
- ✅ İletişim bilgileri

## 📋 Gereksinimler

- **Node.js** (v14 veya üzeri)
- **PostgreSQL** (v12 veya üzeri)
- **PHP** (v7.4 veya üzeri)
- **cURL** (PHP için)

## 🚀 Kurulum

### 1. Backend Kurulumu

```bash
cd backend
npm install
```

### 2. PostgreSQL Kurulumu

PostgreSQL'in kurulu ve çalışır durumda olduğundan emin olun. Veritabanını oluşturun:

```sql
CREATE DATABASE spor_salonu;
```

### 3. Environment Variables

`backend/.env` dosyası oluşturun:

```env
PORT=3000
DB_HOST=localhost
DB_PORT=5432
DB_NAME=spor_salonu
DB_USER=postgres
DB_PASSWORD=postgres
JWT_SECRET=your-super-secret-jwt-key-change-this-in-production
NODE_ENV=development
```

### 4. Veritabanı Seed

Örnek dersleri veritabanına eklemek için:

```bash
cd backend
node seed.js
```

### 4. Backend'i Başlatma

```bash
cd backend
npm start
```

veya geliştirme modu için:

```bash
npm run dev
```

Backend API `http://localhost:3000` adresinde çalışacaktır.

**Not:** İlk çalıştırmada Sequelize otomatik olarak tabloları oluşturacaktır. Geliştirme ortamında `server.js` dosyasındaki `sequelize.sync()` otomatik çalışır.

### 5. Frontend Kurulumu

PHP'nin çalıştığından emin olun. PHP built-in server kullanarak:

```bash
cd frontend
php -S localhost:8000
```

veya Apache/Nginx kullanarak `frontend` klasörünü web root olarak ayarlayın.

Frontend `http://localhost:8000` adresinde erişilebilir olacaktır.

## 📁 Proje Yapısı

```
spor-salonu-sitesi/
├── backend/
│   ├── models/          # MongoDB modelleri
│   ├── routes/           # API route'ları
│   ├── middleware/       # Middleware fonksiyonları
│   ├── server.js         # Ana server dosyası
│   ├── seed.js           # Veritabanı seed scripti
│   └── package.json
├── frontend/
│   ├── assets/
│   │   ├── css/          # CSS dosyaları
│   │   └── js/           # JavaScript dosyaları
│   ├── includes/         # PHP include dosyaları
│   ├── config.php        # Yapılandırma
│   ├── index.php         # Ana sayfa
│   ├── register.php      # Kayıt sayfası
│   ├── login.php         # Giriş sayfası
│   ├── classes.php       # Ders programı
│   ├── reservations.php  # Rezervasyonlar
│   ├── bmi.php           # BMI hesaplayıcı
│   ├── memberships.php    # Üyelik paketleri
│   └── contact.php       # İletişim
└── README.md
```

## 🔌 API Endpoints

### Authentication
- `POST /api/auth/register` - Kullanıcı kaydı
- `POST /api/auth/login` - Kullanıcı girişi
- `GET /api/auth/me` - Kullanıcı bilgileri (Auth gerekli)

### Classes
- `GET /api/classes` - Tüm dersleri listele
- `GET /api/classes/:id` - Tek ders detayı
- `POST /api/classes` - Yeni ders oluştur (Auth gerekli)

### Reservations
- `GET /api/reservations/my-reservations` - Kullanıcı rezervasyonları (Auth gerekli)
- `POST /api/reservations` - Rezervasyon yap (Auth gerekli)
- `DELETE /api/reservations/:id` - Rezervasyon iptal (Auth gerekli)

### BMI
- `POST /api/bmi/calculate` - BMI hesapla (Giriş gerekmez)
- `POST /api/bmi/calculate-and-save` - BMI hesapla ve kaydet (Auth gerekli)

### Memberships
- `GET /api/memberships/packages` - Üyelik paketlerini listele
- `POST /api/memberships/purchase` - Üyelik satın al (Auth gerekli)

## 🔐 Güvenlik Notları

⚠️ **ÖNEMLİ: GitHub'a yüklemeden önce:**

1. ✅ `.env` dosyası `.gitignore`'da olduğundan emin olun (zaten var)
2. ✅ `.env` dosyasını asla commit etmeyin
3. ✅ Production ortamında `JWT_SECRET` değerini mutlaka güçlü bir değerle değiştirin
4. ✅ Veritabanı şifrelerini güvenli tutun
5. ✅ HTTPS kullanımı önerilir
6. ✅ CORS ayarlarını production için sınırlandırın
7. ⚠️ Kodda `default-secret` fallback değeri var - sadece development için, production'da mutlaka `.env` dosyasında gerçek secret kullanın

## 🛠️ Geliştirme

### Yeni Ders Ekleme

Backend seed scriptini güncelleyerek veya API üzerinden yeni dersler eklenebilir.

### Yeni Özellik Ekleme

1. Backend'de yeni route/model oluşturun
2. Frontend'de ilgili PHP sayfasını oluşturun
3. JavaScript API çağrılarını ekleyin
4. CSS stillerini güncelleyin

## 📝 Notlar

- Bu proje eğitim amaçlıdır
- Production kullanımı için ek güvenlik önlemleri alınmalıdır
- Ödeme entegrasyonu eklenmemiştir (simüle edilmiştir)
- E-posta gönderimi için ek yapılandırma gerekebilir

## 📄 Lisans

Bu proje eğitim amaçlıdır.

## 🤝 Katkıda Bulunma

Öneriler ve iyileştirmeler için issue açabilirsiniz.

---

**Geliştirici Notları:**
- PHP frontend, dinamik verileri Node.js API'den çeker
- Session yönetimi PHP tarafında, token yönetimi Node.js tarafında yapılır
- API çağrıları için cURL kullanılır (PHP) ve Fetch API (JavaScript)
