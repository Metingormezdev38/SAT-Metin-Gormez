const express = require('express');
const { body, validationResult } = require('express-validator');
const { User } = require('../models');
const auth = require('../middleware/auth');

const router = express.Router();

// BMI hesaplama
router.post('/calculate', [
  body('height').isFloat({ min: 50, max: 250 }).withMessage('Boy 50-250 cm arasında olmalıdır'),
  body('weight').isFloat({ min: 20, max: 300 }).withMessage('Kilo 20-300 kg arasında olmalıdır')
], async (req, res) => {
  try {
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return res.status(400).json({ 
        success: false, 
        errors: errors.array() 
      });
    }

    const { height, weight } = req.body;

    // BMI hesaplama: kg / (m * m)
    const heightInMeters = height / 100;
    const bmi = weight / (heightInMeters * heightInMeters);
    const roundedBMI = Math.round(bmi * 10) / 10;

    // BMI kategorisi
    let category = '';
    let description = '';
    
    if (roundedBMI < 18.5) {
      category = 'Zayıf';
      description = 'Sağlıklı kilo almak için diyetisyen ve antrenör desteği alabilirsiniz.';
    } else if (roundedBMI < 25) {
      category = 'Normal';
      description = 'Harika! Sağlıklı kilo aralığındasınız. Düzenli egzersiz ile formunuzu koruyun.';
    } else if (roundedBMI < 30) {
      category = 'Fazla Kilolu';
      description = 'Kilo vermek için düzenli egzersiz ve sağlıklı beslenme programı önerilir.';
    } else {
      category = 'Obez';
      description = 'Sağlık uzmanı desteği ile kilo yönetimi programına başlamanız önerilir.';
    }

    res.json({
      success: true,
      data: {
        bmi: roundedBMI,
        category: category,
        description: description,
        height: height,
        weight: weight
      }
    });
  } catch (error) {
    console.error('BMI hesaplama hatası:', error);
    res.status(500).json({ 
      success: false, 
      message: 'BMI hesaplanamadı' 
    });
  }
});

// BMI hesapla ve kullanıcı bilgilerini güncelle (giriş yapmış kullanıcılar için)
router.post('/calculate-and-save', [
  auth,
  body('height').isFloat({ min: 50, max: 250 }).withMessage('Boy 50-250 cm arasında olmalıdır'),
  body('weight').isFloat({ min: 20, max: 300 }).withMessage('Kilo 20-300 kg arasında olmalıdır')
], async (req, res) => {
  try {
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return res.status(400).json({ 
        success: false, 
        errors: errors.array() 
      });
    }

    const { height, weight } = req.body;

    // Kullanıcı bilgilerini güncelle
    await req.user.update({
      height: parseFloat(height),
      weight: parseFloat(weight)
    });

    // BMI hesaplama
    const heightInMeters = height / 100;
    const bmi = weight / (heightInMeters * heightInMeters);
    const roundedBMI = Math.round(bmi * 10) / 10;

    let category = '';
    let description = '';
    
    if (roundedBMI < 18.5) {
      category = 'Zayıf';
      description = 'Sağlıklı kilo almak için diyetisyen ve antrenör desteği alabilirsiniz.';
    } else if (roundedBMI < 25) {
      category = 'Normal';
      description = 'Harika! Sağlıklı kilo aralığındasınız. Düzenli egzersiz ile formunuzu koruyun.';
    } else if (roundedBMI < 30) {
      category = 'Fazla Kilolu';
      description = 'Kilo vermek için düzenli egzersiz ve sağlıklı beslenme programı önerilir.';
    } else {
      category = 'Obez';
      description = 'Sağlık uzmanı desteği ile kilo yönetimi programına başlamanız önerilir.';
    }

    res.json({
      success: true,
      message: 'BMI hesaplandı ve bilgileriniz kaydedildi',
      data: {
        bmi: roundedBMI,
        category: category,
        description: description,
        height: height,
        weight: weight
      }
    });
  } catch (error) {
    console.error('BMI hesaplama ve kaydetme hatası:', error);
    res.status(500).json({ 
      success: false, 
      message: 'BMI hesaplanamadı' 
    });
  }
});

module.exports = router;