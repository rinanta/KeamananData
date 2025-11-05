<?php
// safe/edit.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$uuid = $_GET['u'] ?? ($_POST['uuid'] ?? '');
if (!$uuid) { http_response_code(400); exit('Missing uuid'); }

$stmt = $pdo->prepare("SELECT * FROM items_safe WHERE uuid = :u LIMIT 1");
$stmt->execute([':u'=>$uuid]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$item) { http_response_code(404); exit('Not found'); }

// Ownership check
if ($item['user_id'] != $_SESSION['user']['id']) {
    http_response_code(403); exit('Forbidden: not owner');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!check_csrf($_POST['csrf'] ?? '')) { http_response_code(400); exit('CSRF fail'); }
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $stmt = $pdo->prepare("UPDATE items_safe SET title = :t, content = :c WHERE uuid = :u");
    $stmt->execute([':t'=>$title, ':c'=>$content, ':u'=>$uuid]);
    header('Location: list.php'); exit;
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit SAFE Item</title>
<style>
  body {
    font-family: "Poppins", Arial, sans-serif;
    background: #f8fafc;
    margin: 0;
    padding: 40px;
    color: #1e293b;
  }
  h2 {
    color: #0f172a;
    margin-bottom: 20px;
  }
  form {
    background: #fff;
    padding: 20px 25px;
    border-radius: 12px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.08);
    max-width: 640px;
  }
  input[type="text"], input[name="title"], textarea {
    width: 100%;
    font-size: 1rem;
    padding: 10px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    outline: none;
    box-sizing: border-box;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  input[name="title"]:focus, textarea:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.2);
  }
  textarea {
    resize: vertical;
    min-height: 150px;
  }
  button {
    margin-top: 12px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff;
    padding: 10px 18px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.25s, transform 0.15s;
  }
  button:hover {
    background: linear-gradient(135deg, #1e40af, #1e3a8a);
    transform: translateY(-1px);
  }
  p a {
    display: inline-block;
    margin-top: 18px;
    color: #2563eb;
    text-decoration: none;
    font-weight: 500;
  }
  p a:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>

<h2>Edit SAFE Item (<?= htmlspecialchars($item['uuid']) ?>)</h2>

<form method="post">
  <label for="title"><b>Title</b></label><br>
  <input name="title" id="title" type="text"
         value="<?= htmlspecialchars($item['title']) ?>"><br><br>

  <label for="content"><b>Content</b></label><br>
  <textarea name="content" id="content" rows="6"><?= htmlspecialchars($item['content']) ?></textarea><br><br>

  <input type="hidden" name="uuid" value="<?= htmlspecialchars($item['uuid']) ?>">
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <button type="submit">💾 Save</button>
</form>

<p><a href="list.php">← Back to List</a></p>

</body>
</html>
