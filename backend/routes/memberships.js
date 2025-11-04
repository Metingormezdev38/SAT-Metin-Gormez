const express = require('express');
const { body, validationResult } = require('express-validator');
const { User } = require('../models');
const auth = require('../middleware/auth');

const router = express.Router();

// Üyelik paketleri
const membershipPackages = [
  {
    id: 'basic',
    name: 'Temel Üyelik',
    price: 299,
    duration: 30, // gün
    features: [
      'Tüm grup derslerine katılım',
      'Haftalık ders programı erişimi',
      'BMI hesaplama aracı',
      'Temel ekipman kullanımı'
    ]
  },
  {
    id: 'premium',
    name: 'Premium Üyelik',
    price: 599,
    duration: 30,
    features: [
      'Tüm grup derslerine katılım',
      'Haftalık ders programı erişimi',
      'BMI hesaplama aracı',
      'Tüm ekipman kullanımı',
      'Özel antrenör danışmanlığı',
      'Kişisel antrenman programı'
    ]
  },
  {
    id: 'vip',
    name: 'VIP Üyelik',
    price: 999,
    duration: 30,
    features: [
      'Tüm grup derslerine katılım',
      'Haftalık ders programı erişimi',
      'BMI hesaplama aracı',
      'Tüm ekipman kullanımı',
      'Özel antrenör danışmanlığı',
      'Kişisel antrenman programı',
      'Sınırsız rezervasyon',
      'Öncelikli rezervasyon',
      'Özel VIP alan erişimi'
    ]
  }
];

// Tüm üyelik paketlerini getir
router.get('/packages', (req, res) => {
  res.json({
    success: true,
    data: membershipPackages
  });
});

// Üyelik satın alma (simüle edilmiş - gerçek ödeme entegrasyonu eklenebilir)
router.post('/purchase', [
  auth,
  body('membershipType').isIn(['basic', 'premium', 'vip']).withMessage('Geçerli bir üyelik tipi seçin')
], async (req, res) => {
  try {
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return res.status(400).json({ 
        success: false, 
        errors: errors.array() 
      });
    }

    const { membershipType } = req.body;

    const selectedPackage = membershipPackages.find(pkg => pkg.id === membershipType);
    if (!selectedPackage) {
      return res.status(400).json({ 
        success: false, 
        message: 'Geçersiz üyelik paketi' 
      });
    }

    // Kullanıcının üyeliğini güncelle
    const expiryDate = new Date();
    expiryDate.setDate(expiryDate.getDate() + selectedPackage.duration);

    await req.user.update({
      membershipType: membershipType,
      membershipExpiry: expiryDate
    });

    res.json({
      success: true,
      message: 'Üyelik başarıyla satın alındı',
      data: {
        membershipType: membershipType,
        membershipName: selectedPackage.name,
        expiryDate: expiryDate,
        price: selectedPackage.price
      }
    });
  } catch (error) {
    console.error('Üyelik satın alma hatası:', error);
    res.status(500).json({ 
      success: false, 
      message: 'Üyelik satın alınamadı' 
    });
  }
});

module.exports = router;