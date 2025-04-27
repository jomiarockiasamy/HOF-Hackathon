const mysql = require('mysql2/promise');

const pool = mysql.createPool({
  host: 'localhost',      // your DB host
  user: 'your_username',  // your DB username
  password: 'your_password', // your DB password
  database: 'users.db', 
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
});

module.exports = pool;