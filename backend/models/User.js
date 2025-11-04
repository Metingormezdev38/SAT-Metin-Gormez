const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');
const bcrypt = require('bcryptjs');

const User = sequelize.define('User', {
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
        msg: 'İsim gereklidir'
      }
    }
  },
  email: {
    type: DataTypes.STRING,
    allowNull: false,
    unique: true,
    validate: {
      isEmail: {
        msg: 'Geçerli bir e-posta adresi girin'
      },
      notEmpty: {
        msg: 'E-posta gereklidir'
      }
    }
  },
  password: {
    type: DataTypes.STRING,
    allowNull: false,
    validate: {
      len: {
        args: [6, Infinity],
        msg: 'Şifre en az 6 karakter olmalıdır'
      }
    }
  },
  height: {
    type: DataTypes.FLOAT,
    allowNull: true,
    comment: 'Boy (cm cinsinden)'
  },
  weight: {
    type: DataTypes.FLOAT,
    allowNull: true,
    comment: 'Kilo (kg cinsinden)'
  },
  membershipType: {
    type: DataTypes.ENUM('basic', 'premium', 'vip'),
    allowNull: true,
    defaultValue: null
  },
  membershipExpiry: {
    type: DataTypes.DATE,
    allowNull: true
  }
}, {
  tableName: 'users',
  timestamps: true,
  hooks: {
    beforeCreate: async (user) => {
      if (user.password) {
        user.password = await bcrypt.hash(user.password, 10);
      }
    },
    beforeUpdate: async (user) => {
      if (user.changed('password')) {
        user.password = await bcrypt.hash(user.password, 10);
      }
    }
  }
});

// Şifre karşılaştırma metodu
User.prototype.comparePassword = async function(candidatePassword) {
  return await bcrypt.compare(candidatePassword, this.password);
};

module.exports = User;