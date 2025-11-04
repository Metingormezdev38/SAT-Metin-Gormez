const express = require('express');
const { body, validationResult } = require('express-validator');
const { Class } = require('../models');
const auth = require('../middleware/auth');

const router = express.Router();

// Tüm dersleri getir
router.get('/', async (req, res) => {
  try {
    const classes = await Class.findAll({ 
      where: { isActive: true },
      order: [
        ['day', 'ASC'],
        ['time', 'ASC']
      ]
    });
    res.json({
      success: true,
      data: classes
    });
  } catch (error) {
    console.error('Ders listesi hatası:', error);
    res.status(500).json({ 
      success: false, 
      message: 'Dersler yüklenemedi' 
    });
  }
});

// Tek bir dersi getir
router.get('/:id', async (req, res) => {
  try {
    const classItem = await Class.findByPk(req.params.id);
    if (!classItem) {
      return res.status(404).json({ 
        success: false, 
        message: 'Ders bulunamadı' 
      });
    }
    res.json({
      success: true,
      data: classItem
    });
  } catch (error) {
    res.status(500).json({ 
      success: false, 
      message: 'Ders yüklenemedi' 
    });
  }
});

// Yeni ders oluştur (admin için - isteğe bağlı)
router.post('/', [
  auth,
  body('name').trim().notEmpty().withMessage('Ders adı gereklidir'),
  body('instructor').trim().notEmpty().withMessage('Eğitmen adı gereklidir'),
  body('day').isIn(['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar']).withMessage('Geçerli bir gün seçin'),
  body('time').matches(/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/).withMessage('Geçerli bir saat formatı girin (HH:MM)')
], async (req, res) => {
  try {
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return res.status(400).json({ 
        success: false, 
        errors: errors.array() 
      });
    }

    const classItem = await Class.create(req.body);

    res.status(201).json({
      success: true,
      message: 'Ders oluşturuldu',
      data: classItem
    });
  } catch (error) {
    console.error('Ders oluşturma hatası:', error);
    res.status(500).json({ 
      success: false, 
      message: 'Ders oluşturulamadı' 
    });
  }
});

module.exports = router;