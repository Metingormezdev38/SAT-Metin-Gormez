const sequelize = require('./config/database');
const { User, Class, Reservation } = require('./models');
const dotenv = require('dotenv');

dotenv.config();

const sampleClasses = [
  {
    name: 'Yoga',
    instructor: 'Ayşe Yılmaz',
    day: 'Pazartesi',
    time: '09:00',
    duration: 60,
    maxCapacity: 15,
    description: 'Esneklik ve zihin-beden uyumu için yoga dersi'
  },
  {
    name: 'Pilates',
    instructor: 'Mehmet Demir',
    day: 'Pazartesi',
    time: '18:00',
    duration: 45,
    maxCapacity: 12,
    description: 'Güçlendirme ve esneklik için pilates'
  },
  {
    name: 'Zumba',
    instructor: 'Zeynep Kaya',
    day: 'Salı',
    time: '19:00',
    duration: 60,
    maxCapacity: 25,
    description: 'Eğlenceli dans ve kardiyovasküler egzersiz'
  },
  {
    name: 'CrossFit',
    instructor: 'Ali Yıldız',
    day: 'Çarşamba',
    time: '07:00',
    duration: 60,
    maxCapacity: 20,
    description: 'Yoğun fonksiyonel antrenman'
  },
  {
    name: 'Spinning',
    instructor: 'Fatma Öz',
    day: 'Çarşamba',
    time: '20:00',
    duration: 45,
    maxCapacity: 30,
    description: 'Yüksek enerjili bisiklet antrenmanı'
  },
  {
    name: 'Kickboxing',
    instructor: 'Can Arslan',
    day: 'Perşembe',
    time: '19:00',
    duration: 60,
    maxCapacity: 18,
    description: 'Dövüş sanatı ve kardiyovasküler egzersiz'
  },
  {
    name: 'Boks',
    instructor: 'Emre Şahin',
    day: 'Cuma',
    time: '18:00',
    duration: 60,
    maxCapacity: 15,
    description: 'Teknik boks antrenmanı'
  },
  {
    name: 'Aerobik',
    instructor: 'Selin Doğan',
    day: 'Cumartesi',
    time: '10:00',
    duration: 45,
    maxCapacity: 20,
    description: 'Eğlenceli aerobik egzersizleri'
  },
  {
    name: 'Dans',
    instructor: 'Burak Çelik',
    day: 'Pazar',
    time: '14:00',
    duration: 60,
    maxCapacity: 22,
    description: 'Latin ve modern dans kombinasyonu'
  }
];

async function seedDatabase() {
  try {
    // Veritabanı bağlantısını test et
    await sequelize.authenticate();
    console.log('PostgreSQL bağlantısı başarılı');

    // Veritabanını senkronize et
    await sequelize.sync({ force: false });
    console.log('Veritabanı tabloları hazır');

    // Mevcut dersleri temizle
    await Class.destroy({ where: {}, truncate: true });
    console.log('Mevcut dersler temizlendi');

    // Yeni dersleri ekle
    await Class.bulkCreate(sampleClasses);
    console.log(`${sampleClasses.length} ders başarıyla eklendi`);

    console.log('Veritabanı seed işlemi tamamlandı!');
    process.exit(0);
  } catch (error) {
    console.error('Seed hatası:', error);
    process.exit(1);
  }
}

seedDatabase();