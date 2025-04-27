const express = require('express');
const sqlite3 = require('sqlite3').verbose();
const path = require('path');

const app = express();
const PORT = 3000;

// Middleware to parse JSON
app.use(express.json());

// Connect to the SQLite database
const dbPath = path.join(__dirname, 'requests.db');
const db = new sqlite3.Database(dbPath, (err) => {
    if (err) {
        console.error('Error connecting to the database:', err.message);
    } else {
        console.log('Connected to the SQLite database.');
    }
});

// Get all requests
app.get('/requests', (req, res) => {
    const query = 'SELECT * FROM HelpRequests';
    db.all(query, [], (err, rows) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json(rows);
        }
    });
});

// Get a single request by ID
app.get('/requests/:id', (req, res) => {
    const query = 'SELECT * FROM requests WHERE id = ?';
    const params = [req.params.id];
    db.get(query, params, (err, row) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else if (!row) {
            res.status(404).json({ error: 'Request not found' });
        } else {
            res.json(row);
        }
    });
});

// Create a new request
app.post('/requests', (req, res) => {
    const { title, description, category, zip, keywords, priorityScore, anonymous } = req.body;

    if (!title || !description || !category) {
        return res.status(400).json({ error: 'Title, description, and category are required.' });
    }

    const query = `INSERT INTO HelpRequests (title, description, category, zip, keywords, priorityScore, anonymous)
                   VALUES (?, ?, ?, ?, ?, ?, ?)`;
    const params = [title, description, category, zip, keywords, priorityScore, anonymous];
    db.run(query, params, function (err) {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.status(201).json({ id: this.lastID });
        }
    });
});


// Update a request by ID
app.put('/requests/:id', (req, res) => {
    const { title, description } = req.body;
    const query = 'UPDATE requests SET title = ?, description = ? WHERE id = ?';
    const params = [title, description, req.params.id];
    db.run(query, params, function (err) {
        if (err) {
            res.status(500).json({ error: err.message });
        } else if (this.changes === 0) {
            res.status(404).json({ error: 'Request not found' });
        } else {
            res.json({ message: 'Request updated successfully' });
        }
    });
});

// Delete a request by ID
app.delete('/requests/:id', (req, res) => {
    const query = 'DELETE FROM requests WHERE id = ?';
    const params = [req.params.id];
    db.run(query, params, function (err) {
        if (err) {
            res.status(500).json({ error: err.message });
        } else if (this.changes === 0) {
            res.status(404).json({ error: 'Request not found' });
        } else {
            res.json({ message: 'Request deleted successfully' });
        }
    });
});

// Start the server
app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});

// Close the database connection when the process ends
process.on('SIGINT', () => {
    db.close((err) => {
        if (err) {
            console.error('Error closing the database:', err.message);
        } else {
            console.log('Database connection closed.');
        }
        process.exit(0);
    });
});