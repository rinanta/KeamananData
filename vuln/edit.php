<?php
// vuln/edit.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { http_response_code(400); exit('Bad Request'); }

// Load (no ownership check)
$row = $pdo->query("SELECT * FROM items_vuln WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
if (!$row) { http_response_code(404); exit('Not found'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    // VULNERABLE: direct concatenation (demo purpose)
    $sql = "UPDATE items_vuln SET title = '{$title}', content = '{$content}' WHERE id = $id";
    $pdo->exec($sql);
    header('Location: list.php'); exit;
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Edit VULN Item</title>
<style>
  body {
    font-family: "Poppins", Arial, sans-serif;
    background: #f8fafc;
    color: #1e293b;
    margin: 0;
    padding: 40px;
  }
  .container {
    background: #fff;
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    max-width: 700px;
  }
  h2 {
    color: #b91c1c;
    margin-bottom: 15px;
  }
  input[type="text"], textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 1rem;
    outline: none;
  }
  input[type="text"]:focus, textarea:focus {
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220,38,38,0.2);
  }
  button {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: white;
    padding: 10px 18px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    margin-top: 15px;
    transition: background 0.2s ease;
  }
  button:hover {
    background: linear-gradient(135deg, #b91c1c, #7f1d1d);
  }
  a {
    display: inline-block;
    margin-top: 18px;
    color: #dc2626;
    text-decoration: none;
  }
  a:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>
  <div class="container">
    <h2>Edit VULN Item (ID <?= htmlspecialchars($row['id']) ?>)</h2>
    <form method="post">
      <input type="text" name="title" value="<?= htmlspecialchars($row['title']) ?>" placeholder="Title"><br><br>
      <textarea name="content" rows="6" placeholder="Content"><?= htmlspecialchars($row['content']) ?></textarea><br><br>
      <button>Save</button>
    </form>
    <a href="list.php">← Back</a>
  </div>
</body>
</html>
