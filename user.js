const express = require('express');
const router = express.Router();
const { insertUser } = require('../db/userQueries');
const bcrypt = require('bcrypt');

router.post('/register', async (req, res) => {
  try {
    const { Username, Password, CashappHandle, Email, PhoneNumber, City, ZipCode } = req.body;
    // Validate input here (not shown for brevity)
    const PasswordHash = await bcrypt.hash(Password, 10);
    const userId = await insertUser({
      Username,
      PasswordHash,
      CashappHandle,
      Email,
      PhoneNumber,
      City,
      ZipCode
    });
    res.status(201).json({ userId });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: 'Failed to register user' });
  }
});

module.exports = router;