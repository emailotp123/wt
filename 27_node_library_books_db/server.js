// Q27: Library Book Management — Node.js + SQLite
const express = require('express');
const Database = require('better-sqlite3');

const app = express();
const db = new Database('library.db');

db.exec(`CREATE TABLE IF NOT EXISTS books (
  book_id INTEGER PRIMARY KEY AUTOINCREMENT,
  title   TEXT NOT NULL,
  author  TEXT NOT NULL,
  year    INTEGER NOT NULL
)`);

app.use(express.urlencoded({ extended: true }));
app.use(express.json());

app.get('/', (req, res) => {
  const books = db.prepare('SELECT * FROM books ORDER BY book_id DESC').all();
  res.send(`<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Q27: Library Books</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
  <h3>📚 Library Book Management</h3>
  <div class="card mb-4 shadow-sm"><div class="card-body">
    <h5>Add Book</h5>
    <form method="POST" action="/add" class="row g-2">
      <div class="col-md-4"><input class="form-control" name="title" placeholder="Book Title" required></div>
      <div class="col-md-3"><input class="form-control" name="author" placeholder="Author" required></div>
      <div class="col-md-2"><input type="number" class="form-control" name="year" placeholder="Year" min="1000" max="2099" required></div>
      <div class="col-md-2"><button class="btn btn-success w-100">Add Book</button></div>
    </form>
  </div></div>
  <div class="card shadow-sm"><div class="card-header"><strong>All Books (${books.length})</strong></div>
  <div class="card-body p-0">
    <table class="table table-striped mb-0">
      <thead class="table-dark"><tr><th>#</th><th>Title</th><th>Author</th><th>Year</th></tr></thead>
      <tbody>
        ${books.map(b => `<tr>
          <td>${b.book_id}</td>
          <td><strong>${b.title}</strong></td>
          <td>${b.author}</td>
          <td>${b.year}</td>
        </tr>`).join('') || '<tr><td colspan="4" class="text-muted text-center p-3">No books yet.</td></tr>'}
      </tbody>
    </table>
  </div></div>
</div>
</body></html>`);
});

app.post('/add', (req, res) => {
  const { title, author, year } = req.body;
  const parsedYear = parseInt(year);
  if (title && author && year && !isNaN(parsedYear) && parsedYear >= 1000 && parsedYear <= 2099) {
    db.prepare('INSERT INTO books (title, author, year) VALUES (?, ?, ?)').run(title.trim(), author.trim(), parsedYear);
  }
  res.redirect('/');
});

// REST API
app.get('/api/books', (req, res) => {
  const books = db.prepare('SELECT * FROM books ORDER BY book_id DESC').all();
  res.json({ count: books.length, books });
});

const PORT = process.env.PORT || 3001;
app.listen(PORT, () => console.log(`Library server at http://localhost:${PORT}`));
