const express = require('express');
const { body, validationResult } = require('express-validator');
const { Reservation, Class } = require('../models');
const auth = require('../middleware/auth');

const router = express.Router();

// Kullanıcının rezervasyonlarını getir
router.get('/my-reservations', auth, async (req, res) => {
  try {
    const reservations = await Reservation.findAll({
      where: { userId: req.user.id },
      include: [{
        model: Class,
        as: 'class',
        attributes: ['id', 'name', 'instructor', 'day', 'time', 'duration']
      }],
      order: [['reservationDate', 'ASC']]
    });

    res.json({
      success: true,
      data: reservations
    });
  } catch (error) {
    console.error('Rezervasyon listesi hatası:', error);
    res.status(500).json({ 
      success: false, 
      message: 'Rezervasyonlar yüklenemedi' 
    });
  }
});

// Rezervasyon yap
router.post('/', [
  auth,
  body('classId').notEmpty().withMessage('Ders ID gereklidir'),
  body('reservationDate').isISO8601().withMessage('Geçerli bir tarih formatı girin')
], async (req, res) => {
  try {
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return res.status(400).json({ 
        success: false, 
        errors: errors.array() 
      });
    }

    const { classId, reservationDate } = req.body;

    // Ders kontrolü
    const classItem = await Class.findByPk(classId);
    if (!classItem) {
      return res.status(404).json({ 
        success: false, 
        message: 'Ders bulunamadı' 
      });
    }

    // Kapasite kontrolü
    if (classItem.currentBookings >= classItem.maxCapacity) {
      return res.status(400).json({ 
        success: false, 
        message: 'Bu ders için kontenjan dolmuş' 
      });
    }

    // Aynı rezervasyon kontrolü
    const existingReservation = await Reservation.findOne({
      where: {
        userId: req.user.id,
        classId: parseInt(classId),
        reservationDate: new Date(reservationDate)
      }
    });

    if (existingReservation) {
      return res.status(400).json({ 
        success: false, 
        message: 'Bu ders için zaten rezervasyonunuz var' 
      });
    }

    // Rezervasyon oluştur
    const reservation = await Reservation.create({
      userId: req.user.id,
      classId: parseInt(classId),
      reservationDate: new Date(reservationDate)
    });

    // Ders kapasitesini güncelle
    await classItem.update({
      currentBookings: classItem.currentBookings + 1
    });

    await reservation.reload({
      include: [{
        model: Class,
        as: 'class',
        attributes: ['id', 'name', 'instructor', 'day', 'time']
      }]
    });

    res.status(201).json({
      success: true,
      message: 'Rezervasyon başarıyla oluşturuldu',
      data: reservation
    });
  } catch (error) {
    if (error.name === 'SequelizeUniqueConstraintError') {
      return res.status(400).json({ 
        success: false, 
        message: 'Bu ders için zaten rezervasyonunuz var' 
      });
    }
    console.error('Rezervasyon hatası:', error);
    res.status(500).json({ 
      success: false, 
      message: 'Rezervasyon oluşturulamadı' 
    });
  }
});

// Rezervasyon iptal et
router.delete('/:id', auth, async (req, res) => {
  try {
    const reservation = await Reservation.findOne({
      where: {
        id: req.params.id,
        userId: req.user.id
      }
    });

    if (!reservation) {
      return res.status(404).json({ 
        success: false, 
        message: 'Rezervasyon bulunamadı' 
      });
    }

    // Ders kapasitesini güncelle
    const classItem = await Class.findByPk(reservation.classId);
    if (classItem) {
      await classItem.update({
        currentBookings: Math.max(0, classItem.currentBookings - 1)
      });
    }

    await reservation.destroy();

    res.json({
      success: true,
      message: 'Rezervasyon iptal edildi'
    });
  } catch (error) {
    console.error('Rezervasyon iptal hatası:', error);
    res.status(500).json({ 
      success: false, 
      message: 'Rezervasyon iptal edilemedi' 
    });
  }
});

module.exports = router;