// Q10: Student Registration System — Node.js + SQLite
const express = require('express');
const Database = require('better-sqlite3');

const app = express();
const db = new Database('students.db');

// Create table
db.exec(`CREATE TABLE IF NOT EXISTS students (
  id      INTEGER PRIMARY KEY AUTOINCREMENT,
  name    TEXT NOT NULL,
  email   TEXT UNIQUE NOT NULL,
  course  TEXT NOT NULL
)`);

app.use(express.urlencoded({ extended: true }));
app.use(express.json());

// List page
app.get('/', (req, res) => {
  const students = db.prepare('SELECT * FROM students ORDER BY id DESC').all();
  res.send(`<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Q10: Student Registration</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
  <h3>Student Registration System</h3>
  <div class="card mb-4 shadow-sm"><div class="card-body">
    <h5>Register New Student</h5>
    <form method="POST" action="/register" class="row g-2">
      <div class="col-md-3"><input class="form-control" name="name" placeholder="Full Name" required></div>
      <div class="col-md-4"><input type="email" class="form-control" name="email" placeholder="Email" required></div>
      <div class="col-md-3"><input class="form-control" name="course" placeholder="Course" required></div>
      <div class="col-md-2"><button class="btn btn-primary w-100">Register</button></div>
    </form>
  </div></div>
  <div class="card shadow-sm"><div class="card-body p-0">
    <table class="table table-striped mb-0">
      <thead class="table-dark"><tr><th>#</th><th>Name</th><th>Email</th><th>Course</th></tr></thead>
      <tbody>
        ${students.map(s => `<tr>
          <td>${s.id}</td>
          <td>${s.name}</td>
          <td>${s.email}</td>
          <td>${s.course}</td>
        </tr>`).join('')}
      </tbody>
    </table>
    ${students.length === 0 ? '<p class="text-muted p-3">No students registered yet.</p>' : ''}
  </div></div>
</div>
</body></html>`);
});

// Register
app.post('/register', (req, res) => {
  const { name, email, course } = req.body;
  if (!name || !email || !course) return res.redirect('/?err=missing');
  try {
    db.prepare('INSERT INTO students (name, email, course) VALUES (?, ?, ?)').run(name, email, course);
  } catch (e) { /* duplicate email */ }
  res.redirect('/');
});

// REST API — GET all students
app.get('/api/students', (req, res) => {
  const students = db.prepare('SELECT * FROM students ORDER BY id DESC').all();
  res.json({ count: students.length, students });
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => console.log(`Server running at http://localhost:${PORT}`));
