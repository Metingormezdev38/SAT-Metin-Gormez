const express = require('express');
const { body, validationResult } = require('express-validator');
const jwt = require('jsonwebtoken');
const { User } = require('../models');
const auth = require('../middleware/auth');

const router = express.Router();

// JWT token oluşturma
const generateToken = (userId) => {
  return jwt.sign({ userId }, process.env.JWT_SECRET || 'default-secret', { expiresIn: '7d' });
};

// Kayıt Ol
router.post('/register', [
  body('name').trim().notEmpty().withMessage('İsim gereklidir'),
  body('email').isEmail().withMessage('Geçerli bir e-posta adresi girin'),
  body('password').isLength({ min: 6 }).withMessage('Şifre en az 6 karakter olmalıdır')
], async (req, res) => {
  try {
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return res.status(400).json({ 
        success: false, 
        message: 'Validasyon hatası',
        errors: errors.array() 
      });
    }

    const { name, email, password } = req.body;

    // E-posta kontrolü
    const existingUser = await User.findOne({ where: { email: email.toLowerCase() } });
    if (existingUser) {
      return res.status(400).json({ 
        success: false, 
        message: 'Bu e-posta adresi zaten kullanılıyor' 
      });
    }

    // Yeni kullanıcı oluştur
    const user = await User.create({ 
      name, 
      email: email.toLowerCase(), 
      password 
    });

    const token = generateToken(user.id);

    res.status(201).json({
      success: true,
      message: 'Kayıt başarılı',
      token,
      user: {
        id: user.id,
        name: user.name,
        email: user.email
      }
    });
  } catch (error) {
    console.error('Kayıt hatası:', error);
    res.status(500).json({ 
      success: false, 
      message: 'Kayıt sırasında bir hata oluştu' 
    });
  }
});

// Giriş Yap
router.post('/login', [
  body('email').isEmail().withMessage('Geçerli bir e-posta adresi girin'),
  body('password').notEmpty().withMessage('Şifre gereklidir')
], async (req, res) => {
  try {
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return res.status(400).json({ 
        success: false, 
        message: 'Validasyon hatası',
        errors: errors.array() 
      });
    }

    const { email, password } = req.body;

    // Kullanıcıyı bul
    const user = await User.findOne({ where: { email: email.toLowerCase() } });
    if (!user) {
      return res.status(401).json({ 
        success: false, 
        message: 'E-posta veya şifre hatalı' 
      });
    }

    // Şifre kontrolü
    const isMatch = await user.comparePassword(password);
    if (!isMatch) {
      return res.status(401).json({ 
        success: false, 
        message: 'E-posta veya şifre hatalı' 
      });
    }

    const token = generateToken(user.id);

    res.json({
      success: true,
      message: 'Giriş başarılı',
      token,
      user: {
        id: user.id,
        name: user.name,
        email: user.email,
        membershipType: user.membershipType
      }
    });
  } catch (error) {
    console.error('Giriş hatası:', error);
    res.status(500).json({ 
      success: false, 
      message: 'Giriş sırasında bir hata oluştu' 
    });
  }
});

// Kullanıcı bilgilerini getir
router.get('/me', auth, async (req, res) => {
  try {
    res.json({
      success: true,
      user: {
        id: req.user.id,
        name: req.user.name,
        email: req.user.email,
        height: req.user.height,
        weight: req.user.weight,
        membershipType: req.user.membershipType,
        membershipExpiry: req.user.membershipExpiry
      }
    });
  } catch (error) {
    res.status(500).json({ 
      success: false, 
      message: 'Kullanıcı bilgileri alınamadı' 
    });
  }
});

module.exports = router;