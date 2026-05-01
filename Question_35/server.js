// Q35: Task Manager REST API — Express.js (in-memory storage)
const express = require('express');
const app = express();
app.use(express.json());

let tasks = [];
let nextId = 1;

const notFound = (res) => res.status(404).json({ error: 'Task not found' });

// GET all tasks (optional ?status=pending|completed)
app.get('/api/tasks', (req, res) => {
  const { status } = req.query;
  const result = status ? tasks.filter(t => t.status === status) : tasks;
  res.json({ count: result.length, tasks: result });
});

// GET single task
app.get('/api/tasks/:id', (req, res) => {
  const task = tasks.find(t => t.id === +req.params.id);
  task ? res.json(task) : notFound(res);
});

// POST add task
app.post('/api/tasks', (req, res) => {
  const { title, description } = req.body;
  if (!title) return res.status(400).json({ error: 'title is required' });
  const task = { id: nextId++, title, description: description || '', status: 'pending', createdAt: new Date().toISOString() };
  tasks.push(task);
  res.status(201).json(task);
});

// PATCH update status
app.patch('/api/tasks/:id', (req, res) => {
  const task = tasks.find(t => t.id === +req.params.id);
  if (!task) return notFound(res);
  const { status } = req.body;
  if (status && ['pending', 'completed'].includes(status)) task.status = status;
  if (req.body.title) task.title = req.body.title;
  if (req.body.description !== undefined) task.description = req.body.description;
  res.json(task);
});

// DELETE task
app.delete('/api/tasks/:id', (req, res) => {
  const idx = tasks.findIndex(t => t.id === +req.params.id);
  if (idx === -1) return notFound(res);
  const deleted = tasks.splice(idx, 1)[0];
  res.json({ message: 'Task deleted', task: deleted });
});

// HTML test page
app.get('/', (req, res) => {
  res.send(`<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Q35: Task Manager API</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>#output{background:#1a1a1a;color:#0f0;padding:12px;border-radius:5px;min-height:100px;font-family:monospace;font-size:13px;white-space:pre-wrap}</style>
</head>
<body class="bg-light">
<div class="container mt-4" style="max-width:700px">
  <h3>✅ Task Manager REST API</h3>
  <p class="text-muted">Base URL: <code>http://localhost:3003/api/tasks</code></p>
  <div class="card mb-3"><div class="card-body">
    <h6>Add Task</h6>
    <div class="row g-2">
      <div class="col"><input id="taskTitle" class="form-control" placeholder="Task title" /></div>
      <div class="col"><input id="taskDesc"  class="form-control" placeholder="Description (optional)" /></div>
    </div>
    <button class="btn btn-primary btn-sm mt-2" onclick="addTask()">POST /api/tasks</button>
  </div></div>
  <div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
    <button class="btn btn-success btn-sm" onclick="listTasks()">GET All</button>
    <button class="btn btn-warning btn-sm" onclick="listTasks('pending')">GET Pending</button>
    <button class="btn btn-info btn-sm" onclick="listTasks('completed')">GET Completed</button>
    <input id="taskId" class="form-control form-control-sm" placeholder="ID" style="width:70px">
    <button class="btn btn-secondary btn-sm" onclick="completeTask()">Mark Complete</button>
    <button class="btn btn-danger btn-sm" onclick="deleteTask()">DELETE</button>
  </div>
  <strong>Response:</strong>
  <div id="output">Click a button to make a request...</div>
</div>
<script>
async function req(url, opts={}) {
  try {
    const r = await fetch(url, opts);
    const d = await r.json();
    document.getElementById('output').textContent = JSON.stringify(d, null, 2);
  } catch(e) { document.getElementById('output').textContent = 'Error: ' + e.message; }
}
function listTasks(s='') { req('/api/tasks' + (s ? '?status='+s : '')); }
function deleteTask()    { req('/api/tasks/' + document.getElementById('taskId').value, { method:'DELETE' }); }
function completeTask()  { req('/api/tasks/' + document.getElementById('taskId').value, { method:'PATCH', headers:{'Content-Type':'application/json'}, body: JSON.stringify({status:'completed'}) }); }
function addTask() {
  req('/api/tasks', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({ title: document.getElementById('taskTitle').value, description: document.getElementById('taskDesc').value })
  });
}
</script>
</body></html>`);
});

const PORT = process.env.PORT || 3003;
app.listen(PORT, () => console.log(`Task API running at http://localhost:${PORT}`));
