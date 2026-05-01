// Q34: Blog Management REST API — Express.js (in-memory storage)
const express = require('express');
const app = express();
app.use(express.json());

let posts = [];
let nextId = 1;

// Helper
const notFound = (res) => res.status(404).json({ error: 'Post not found' });

// GET all posts
app.get('/api/blogs', (req, res) => {
  res.json({ count: posts.length, posts });
});

// GET single post
app.get('/api/blogs/:id', (req, res) => {
  const post = posts.find(p => p.id === +req.params.id);
  post ? res.json(post) : notFound(res);
});

// POST create
app.post('/api/blogs', (req, res) => {
  const { title, content, author } = req.body;
  if (!title || !content) return res.status(400).json({ error: 'title and content are required' });
  const post = { id: nextId++, title, content, author: author || 'Anonymous', createdAt: new Date().toISOString(), updatedAt: new Date().toISOString() };
  posts.push(post);
  res.status(201).json(post);
});

// PUT update
app.put('/api/blogs/:id', (req, res) => {
  const idx = posts.findIndex(p => p.id === +req.params.id);
  if (idx === -1) return notFound(res);
  const { title, content, author } = req.body;
  posts[idx] = { ...posts[idx], ...(title && { title }), ...(content && { content }), ...(author && { author }), updatedAt: new Date().toISOString() };
  res.json(posts[idx]);
});

// DELETE
app.delete('/api/blogs/:id', (req, res) => {
  const idx = posts.findIndex(p => p.id === +req.params.id);
  if (idx === -1) return notFound(res);
  const deleted = posts.splice(idx, 1)[0];
  res.json({ message: 'Post deleted', post: deleted });
});

// HTML test page
app.get('/', (req, res) => {
  res.send(`<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Q34: Blog API</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>#output{background:#1a1a1a;color:#0f0;padding:12px;border-radius:5px;min-height:100px;font-family:monospace;font-size:13px;white-space:pre-wrap}</style>
</head>
<body class="bg-light">
<div class="container mt-4" style="max-width:700px">
  <h3>📝 Blog Management REST API</h3>
  <p class="text-muted">Base URL: <code>http://localhost:3002/api/blogs</code></p>
  <div class="card mb-3"><div class="card-body">
    <h6>Create Post</h6>
    <div class="row g-2">
      <div class="col"><input id="title" class="form-control" placeholder="Title" /></div>
      <div class="col"><input id="author" class="form-control" placeholder="Author" /></div>
    </div>
    <textarea id="content" class="form-control mt-2" placeholder="Content" rows="2"></textarea>
    <button class="btn btn-primary btn-sm mt-2" onclick="createPost()">POST /api/blogs</button>
  </div></div>
  <div class="d-flex gap-2 mb-3 flex-wrap">
    <button class="btn btn-success btn-sm" onclick="listPosts()">GET All Posts</button>
    <button class="btn btn-info btn-sm" onclick="getPost()">GET by ID</button>
    <input id="postId" class="form-control form-control-sm w-auto" placeholder="ID" style="width:70px!important">
    <button class="btn btn-danger btn-sm" onclick="deletePost()">DELETE by ID</button>
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
function listPosts() { req('/api/blogs'); }
function getPost()  { req('/api/blogs/' + document.getElementById('postId').value); }
function deletePost(){ req('/api/blogs/' + document.getElementById('postId').value, { method:'DELETE' }); }
function createPost() {
  req('/api/blogs', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({ title: document.getElementById('title').value, content: document.getElementById('content').value, author: document.getElementById('author').value })
  });
}
</script>
</body></html>`);
});

const PORT = process.env.PORT || 3002;
app.listen(PORT, () => console.log(`Blog API running at http://localhost:${PORT}`));
