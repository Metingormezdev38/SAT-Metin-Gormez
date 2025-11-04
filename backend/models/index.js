const sequelize = require('../config/database');
const User = require('./User');
const Class = require('./Class');
const Reservation = require('./Reservation');

// İlişkileri tanımla
User.hasMany(Reservation, { foreignKey: 'userId', as: 'reservations' });
Reservation.belongsTo(User, { foreignKey: 'userId', as: 'user' });

Class.hasMany(Reservation, { foreignKey: 'classId', as: 'reservations' });
Reservation.belongsTo(Class, { foreignKey: 'classId', as: 'class' });

module.exports = {
  sequelize,
  User,
  Class,
  Reservation
};
