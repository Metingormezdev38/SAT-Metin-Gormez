const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const Class = sequelize.define('Class', {
  id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true
  },
  name: {
    type: DataTypes.STRING,
    allowNull: false,
    validate: {
      notEmpty: {
        msg: 'Ders adı gereklidir'
      }
    }
  },
  instructor: {
    type: DataTypes.STRING,
    allowNull: false,
    validate: {
      notEmpty: {
        msg: 'Eğitmen adı gereklidir'
      }
    }
  },
  day: {
    type: DataTypes.ENUM('Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar'),
    allowNull: false
  },
  time: {
    type: DataTypes.STRING,
    allowNull: false,
    validate: {
      is: {
        args: /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/,
        msg: 'Geçerli bir saat formatı girin (HH:MM)'
      }
    }
  },
  duration: {
    type: DataTypes.INTEGER,
    defaultValue: 60,
    comment: 'Dakika cinsinden'
  },
  maxCapacity: {
    type: DataTypes.INTEGER,
    defaultValue: 20,
    validate: {
      min: 1
    }
  },
  currentBookings: {
    type: DataTypes.INTEGER,
    defaultValue: 0,
    validate: {
      min: 0
    }
  },
  description: {
    type: DataTypes.TEXT,
    defaultValue: ''
  },
  isActive: {
    type: DataTypes.BOOLEAN,
    defaultValue: true
  }
}, {
  tableName: 'classes',
  timestamps: true
});

module.exports = Class;