<?php
require __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Setup Database Tables</title>
<style>
    /* ======== RESET & STYLE DASAR ======== */
    * {
        box-sizing: border-box;
        font-family: "Poppins", sans-serif;
        margin: 0;
        padding: 0;
    }

    body {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #333;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        min-height: 100vh;
        padding: 40px 20px;
    }

    .card {
        background: #fff;
        width: 100%;
        max-width: 800px;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    h3 {
        text-align: center;
        color: #444;
        margin-bottom: 20px;
        font-size: 1.6em;
    }

    hr {
        border: none;
        border-top: 2px solid #eee;
        margin-bottom: 20px;
    }

    .log {
        background: #f9f9f9;
        border-radius: 10px;
        padding: 20px;
        line-height: 1.7;
        font-family: Consolas, monospace;
        font-size: 0.95em;
        color: #444;
        max-height: 400px;
        overflow-y: auto;
    }

    .ok { color: #27ae60; font-weight: 600; }
    .warn { color: #f39c12; font-weight: 600; }
    .err { color: #c0392b; font-weight: 600; }

    a.button {
        display: inline-block;
        margin-top: 25px;
        text-decoration: none;
        background: #667eea;
        color: #fff;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    a.button:hover {
        background: #5a67d8;
        transform: translateY(-2px);
    }

    footer {
        margin-top: 30px;
        color: #eee;
        font-size: 0.85em;
    }
</style>
</head>
<body>
    <div class="card">
        <h3>Setup Database Tables</h3>
        <hr>
        <div class="log">
<?php
try {
    // --- Fungsi bantu ---
    if (!function_exists('uuid4')) {
        function uuid4() {
            return sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
        }
    }

    if (!function_exists('token_generate')) {
        function token_generate($length = 32) {
            return bin2hex(random_bytes($length));
        }
    }

    if (!function_exists('token_hash')) {
        function token_hash($token) {
            return hash('sha256', $token);
        }
    }

    echo "<span class='ok'>→ Menghubungkan ke database...</span><br>";

    // --- USERS TABLE ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin','user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<span class='ok'>✓ Tabel users berhasil dibuat</span><br>";

    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO users (username, password, role) VALUES
            ('admin', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'admin'),
            ('user1', '" . password_hash('user1pass', PASSWORD_DEFAULT) . "', 'user'),
            ('user2', '" . password_hash('user2pass', PASSWORD_DEFAULT) . "', 'user')
        ");
        echo "<span class='ok'>✓ Sample users berhasil ditambahkan</span><br>";
    }

    // --- VULN TABLE ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS items_vuln (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        content TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "<span class='ok'>✓ Tabel items_vuln berhasil dibuat</span><br>";

    // --- SAFE TABLE ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS items_safe (
        id INT AUTO_INCREMENT PRIMARY KEY,
        uuid VARCHAR(36) UNIQUE NOT NULL,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        content TEXT,
        access_token VARCHAR(64) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_uuid (uuid),
        INDEX idx_token (access_token)
    )");
    echo "<span class='ok'>✓ Tabel items_safe berhasil dibuat</span><br>";

    // --- SAMPLE DATA ---
    echo "<br><strong>📦 Menambahkan sample data...</strong><br>";
    $pdo->exec("INSERT INTO items_vuln (user_id, title, content) VALUES 
        (2, 'Item User1 #1', 'Ini adalah konten rahasia user1'),
        (2, 'Item User1 #2', 'Data pribadi user1 yang sensitif'),
        (3, 'Item User2 #1', 'Konten milik user2'),
        (3, 'Item User2 #2', 'Dokumen rahasia user2')
    ");
    echo "<span class='ok'>✓ Sample data items_vuln inserted</span><br>";

    $stmt = $pdo->prepare("INSERT INTO items_safe (uuid, user_id, title, content, access_token) VALUES (?, ?, ?, ?, ?)");
    for ($i = 1; $i <= 4; $i++) {
        $uuid = uuid4();
        $token = token_hash(token_generate());
        $user_id = ($i <= 2) ? 2 : 3;
        $title = "Safe Item User" . (($user_id == 2) ? "1" : "2") . " #$i";
        $content = "Konten aman user" . (($user_id == 2) ? "1" : "2");
        $stmt->execute([$uuid, $user_id, $title, $content, $token]);
    }
    echo "<span class='ok'>✓ Sample data items_safe inserted</span><br>";

    echo "<br><strong class='ok'>✅ Setup database berhasil!</strong><br>";
} catch (Exception $e) {
    echo "<strong class='err'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</strong>";
}
?>
        </div>
        <div style="text-align:center;">
            <a href="../index.php" class="button">⬅️ Kembali ke Dashboard</a>
        </div>
    </div>
    <footer>
        © <?= date('Y') ?> Praktikum Keamanan Web — Setup Page
    </footer>
</body>
</html>
