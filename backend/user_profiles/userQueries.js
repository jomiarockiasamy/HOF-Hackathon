const pool = require('./connection');

/**
 * Inserts a new user into the Users table.
 * @param {Object} user - User data object
 * @param {string} user.Username
 * @param {string} user.PasswordHash
 * @param {string} [user.CashappHandle]
 * @param {string} [user.Email]
 * @param {string} [user.PhoneNumber]
 * @param {string} user.City
 * @param {string} user.ZipCode
 * @returns {Promise<number>} The inserted user's ID
 */
async function insertUser(user) {
  const [result] = await pool.execute(
    `INSERT INTO Users 
      (Username, PasswordHash, CashappHandle, Email, PhoneNumber, City, ZipCode)
     VALUES (?, ?, ?, ?, ?, ?, ?)`,
    [
      user.Username,
      user.PasswordHash,
      user.CashappHandle || null,
      user.Email || null,
      user.PhoneNumber || null,
      user.City,
      user.ZipCode
    ]
  );
  return result.insertId;
}

module.exports = { insertUser };