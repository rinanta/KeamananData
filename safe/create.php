<?php
// safe/create.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!check_csrf($_POST['csrf'] ?? '')) { 
        http_response_code(400); 
        exit('CSRF fail'); 
    }

    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if ($title === '') { 
        $err = "Title required"; 
    }

    if (empty($err)) {
        $uuid = uuid4();
        $token = token_generate();      // token acak yang akan ditampilkan ke user
        $hash = token_hash($token);     // hash disimpan di database

        // Gunakan kolom access_token
        $stmt = $pdo->prepare("INSERT INTO items_safe (uuid, access_token, user_id, title, content)
                               VALUES (:uuid, :th, :uid, :t, :c)");
        $stmt->execute([
            ':uuid' => $uuid,
            ':th'   => $hash,
            ':uid'  => $_SESSION['user']['id'],
            ':t'    => $title,
            ':c'    => $content
        ]);

        echo "<div class='result'><h3>✅ Item created successfully</h3>";
        echo "<p><b>UUID:</b> " . htmlspecialchars($uuid) . "</p>";
        echo "<p><b>ACCESS TOKEN (save this now):</b><br><pre>" . htmlspecialchars($token) . "</pre></p>";
        echo '<p><a href="list.php" class="btn-back">Back to List</a></p></div>';
        exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Create SAFE Item</title>
<style>
    * { box-sizing: border-box; font-family: "Poppins", sans-serif; margin: 0; padding: 0; }
    body {
        background: linear-gradient(135deg, #6be6a4, #43c7f4);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
    }

    .card {
        width: 100%;
        max-width: 600px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        padding: 32px 36px;
        animation: fadeIn .4s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    h2 {
        color: #15803d;
        margin-bottom: 20px;
        text-align: center;
    }

    p.error {
        color: #b91c1c;
        background: #fee2e2;
        border: 1px solid #fecaca;
        border-radius: 8px;
        padding: 10px;
        text-align: center;
        margin-bottom: 16px;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    input[type="text"], textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color .2s ease;
    }

    input[type="text"]:focus,
    textarea:focus {
        border-color: #10b981;
        outline: none;
    }

    button {
        background: #16a34a;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 10px 16px;
        font-size: 1rem;
        cursor: pointer;
        font-weight: 600;
        transition: background .2s ease;
    }

    button:hover {
        background: #15803d;
    }

    a.btn-back {
        display: inline-block;
        margin-top: 10px;
        color: #15803d;
        text-decoration: none;
        font-weight: 600;
        transition: color .2s;
    }

    a.btn-back:hover {
        text-decoration: underline;
    }

    .result {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        padding: 30px 36px;
        max-width: 600px;
        margin: 40px auto;
        text-align: center;
        animation: fadeIn .4s ease;
    }

    pre {
        background: #f1f5f9;
        padding: 10px;
        border-radius: 6px;
        overflow-x: auto;
        color: #0f172a;
    }

    @media (max-width: 640px) {
        .card, .result { padding: 24px 20px; }
        h2 { font-size: 1.3rem; }
    }
</style>
</head>
<body>
<div class="card">
    <h2>🛡 Create SAFE Item</h2>
    <?php if (!empty($err)): ?>
        <p class="error"><?= htmlspecialchars($err) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="title" placeholder="usernames" 
               value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
        <textarea name="content" rows="6" placeholder="Password"><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <button type="submit">Create Item</button>
    </form>

    <a href="list.php" class="btn-back">← Kembali ke List</a>
</div>
</body>
</html>
