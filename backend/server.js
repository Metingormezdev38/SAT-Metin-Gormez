const express = require('express');
const cors = require('cors');
const dotenv = require('dotenv');
const { sequelize } = require('./models');

// Load environment variables
dotenv.config();

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Veritabanı bağlantısını test et ve senkronize et
sequelize.authenticate()
  .then(() => {
    console.log('PostgreSQL bağlantısı başarılı');
    
    // Veritabanını senkronize et (geliştirme ortamı için)
    if (process.env.NODE_ENV !== 'production') {
      sequelize.sync({ alter: true }).then(() => {
        console.log('Veritabanı tabloları senkronize edildi');
      }).catch(err => {
        console.error('Veritabanı senkronizasyon hatası:', err);
      });
    }
  })
  .catch(err => {
    console.error('PostgreSQL bağlantı hatası:', err);
  });

// Routes
app.use('/api/auth', require('./routes/auth'));
app.use('/api/classes', require('./routes/classes'));
app.use('/api/reservations', require('./routes/reservations'));
app.use('/api/bmi', require('./routes/bmi'));
app.use('/api/memberships', require('./routes/memberships'));

// Health check
app.get('/api/health', (req, res) => {
  res.json({ status: 'OK', message: 'API çalışıyor' });
});

// Error handling middleware
app.use((err, req, res, next) => {
  console.error(err.stack);
  res.status(500).json({ 
    success: false, 
    message: 'Sunucu hatası',
    error: process.env.NODE_ENV === 'development' ? err.message : undefined
  });
});

app.listen(PORT, () => {
  console.log(`Server çalışıyor: http://localhost:${PORT}`);
});

module.exports = app;