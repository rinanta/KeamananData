<?php
// vuln/create.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $uid = (int)$_SESSION['user']['id'];
    // VULNERABLE: string concatenation into SQL (demonstrate SQLi)
    $sql = "INSERT INTO items_vuln (user_id, title, content) VALUES ($uid, '{$title}', '{$content}')";
    $pdo->exec($sql);
    header('Location: list.php'); exit;
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Create VULN Item</title>
<style>
    /* Reset & font */
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: "Poppins", sans-serif; }
    body {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px;
        background: linear-gradient(135deg, #ff7a7a, #ffb199); /* vulnerable = warm tone */
        color: #222;
    }

    /* Card */
    .card {
        width: 100%;
        max-width: 760px;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        padding: 28px;
        animation: floatIn .45s ease-out;
    }
    @keyframes floatIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    header.top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
    }

    header.top h2 {
        font-size: 1.35rem;
        color: #b02a2a;
        display: flex;
        gap: 10px;
        align-items: center;
    }

    header.top p.small {
        color: #777;
        font-size: 0.9rem;
    }

    form {
        margin-top: 10px;
    }

    label {
        display: block;
        font-size: 0.92rem;
        color: #444;
        margin-bottom: 6px;
        margin-top: 12px;
    }

    input[type="text"],
    textarea {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #e0dede;
        border-radius: 10px;
        font-size: 1rem;
        resize: vertical;
        transition: box-shadow .18s ease, border-color .18s ease;
    }

    input[type="text"]:focus,
    textarea:focus {
        outline: none;
        border-color: #ff6b6b;
        box-shadow: 0 6px 14px rgba(255,107,107,0.12);
    }

    .form-row { margin-bottom: 8px; }

    .actions {
        margin-top: 18px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
    }

    button.primary {
        background: linear-gradient(135deg, #ff6b6b, #ff4d4d);
        color: #fff;
        padding: 10px 16px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: transform .12s ease, box-shadow .18s ease;
    }
    button.primary:hover { transform: translateY(-3px); box-shadow: 0 8px 18px rgba(255,77,77,0.18); }

    a.link {
        color: #ff4d4d;
        text-decoration: none;
        font-weight: 600;
        background: transparent;
        padding: 8px 10px;
        border-radius: 8px;
    }
    a.link:hover { text-decoration: underline; }

    .hint {
        color: #8a2a2a;
        font-size: 0.9rem;
        margin-top: 10px;
    }

    /* small screens */
    @media (max-width: 560px) {
        .card { padding: 18px; }
        header.top h2 { font-size: 1.15rem; }
    }
</style>
</head>
<body>
  <div class="card">
    <header class="top">
      <h2>⚠️ Create VULN Item</h2>
      <p class="small">Demo: Broken Access Control (IDOR)</p>
    </header>

    <form method="post" autocomplete="off" novalidate>
      <div class="form-row">
        <label for="title">Title</label>
        <input id="title" name="title" type="text" placeholder="Username" required>
      </div>

      <div class="form-row">
        <label for="content">Content</label>
        <textarea id="content" name="content" rows="6" placeholder="Password"></textarea>
      </div>

      <div class="actions">
        <button class="primary" type="submit">Create</button>
        <a class="link" href="list.php">← Back to list</a>
      </div>

      <p class="hint">Note: Form ini sengaja rentan untuk tujuan praktikum — jangan dipakai di produksi.</p>
    </form>
  </div>
</body>
</html>
