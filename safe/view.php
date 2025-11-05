<?php
// safe/view.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$uuid = $_GET['u'] ?? '';
// If token not provided in GET, show form to ask token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uuid = $_POST['u'] ?? '';
    $token = $_POST['token'] ?? '';
} else {
    $token = $_GET['t'] ?? '';
}

if (!$uuid) { http_response_code(400); exit('Missing uuid'); }

$stmt = $pdo->prepare("SELECT * FROM items_safe WHERE uuid = :u LIMIT 1");
$stmt->execute([':u'=>$uuid]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$item) { http_response_code(404); exit('Not found'); }

// Ownership check
if ($item['user_id'] != $_SESSION['user']['id']) {
    http_response_code(403); exit('Forbidden: not owner');
}

// If token not yet provided, show form
if (!$token) {
    ?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Enter Access Token</title>
<style>
  body {
    font-family: "Poppins", Arial, sans-serif;
    background: #f8fafc;
    color: #1e293b;
    margin: 0;
    padding: 50px;
  }
  h2 {
    color: #0f172a;
    margin-bottom: 15px;
  }
  form {
    background: #fff;
    padding: 20px 25px;
    border-radius: 10px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.08);
    max-width: 600px;
  }
  input[name="token"] {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 1rem;
    outline: none;
  }
  input[name="token"]:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.2);
  }
  button {
    margin-top: 15px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: white;
    padding: 10px 18px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.2s ease;
  }
  button:hover {
    background: linear-gradient(135deg, #1e40af, #1e3a8a);
  }
  p a {
    display: inline-block;
    margin-top: 18px;
    color: #2563eb;
    text-decoration: none;
  }
  p a:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>
  <h2>Enter Access Token for UUID <?= htmlspecialchars($uuid) ?></h2>
  <form method="post">
    <input type="hidden" name="u" value="<?= htmlspecialchars($uuid) ?>">
    <input name="token" placeholder="Paste your access token here">
    <br><br>
    <button>View</button>
  </form>
  <p><a href="list.php">← Back</a></p>
</body>
</html>
<?php
    exit;
}

// Verify token (compare hash)
$provided_hash = token_hash($token);

// Perbaikan: kolom di database adalah `access_token`
if (!hash_equals($item['access_token'], $provided_hash)) {
    http_response_code(403);
    exit('<h3 style="color:red;font-family:Arial;">Invalid token ❌</h3><p><a href="list.php">Back</a></p>');
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>View SAFE Item</title>
<style>
  body {
    font-family: "Poppins", Arial, sans-serif;
    background: #f8fafc;
    color: #1e293b;
    margin: 0;
    padding: 40px;
  }
  .card {
    background: #fff;
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    max-width: 700px;
  }
  h2 {
    color: #0f172a;
    margin-bottom: 15px;
  }
  p {
    line-height: 1.6;
  }
  .uuid {
    color: #64748b;
    font-size: 0.9rem;
  }
  a {
    display: inline-block;
    margin-top: 18px;
    color: #2563eb;
    text-decoration: none;
    font-weight: 500;
  }
  a:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>
<div class="card">
  <h2><?= htmlspecialchars($item['title']) ?></h2>
  <p><?= nl2br(htmlspecialchars($item['content'])) ?></p>
  <p class="uuid"><i>UUID: <?= htmlspecialchars($item['uuid']) ?></i></p>
  <a href="list.php">← Back to List</a>
</div>
</body>
</html>
