<?php
// vuln/list.php
require_once __DIR__ . '/../config.php';

// Cek login
if (empty($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

// Cek apakah tabel exists, jika tidak redirect ke setup
try {
    $res = $pdo->query("SELECT items_vuln.*, users.username 
                        FROM items_vuln 
                        JOIN users ON items_vuln.user_id = users.id 
                        ORDER BY items_vuln.id DESC");
    $items = $res->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Jika tabel belum ada, tampilkan pesan error yang jelas
    if ($e->getCode() == '42S02') {
        die('
            <h2>Error: Tabel belum dibuat!</h2>
            <p>Silakan jalankan setup terlebih dahulu:</p>
            <p><a href="../setup_tables.php" style="background:#f00;color:#fff;padding:10px 20px;text-decoration:none;border-radius:5px;">
                Klik di sini untuk Setup Database
            </a></p>
            <p>Setelah setup selesai, kembali ke <a href="../index.php">Dashboard</a></p>
        ');
    }
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VULN - Items List</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            background-color: #fee; 
        }
        h2 { color: #c00; }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            background: white; 
        }
        th { 
            background: #c00; 
            color: white; 
            padding: 10px; 
            text-align: left; 
        }
        td { 
            padding: 10px; 
            border-bottom: 1px solid #ddd; 
        }
        a { 
            color: #06c; 
            text-decoration: none; 
        }
        a:hover { 
            text-decoration: underline; 
        }
        .nav { 
            margin-bottom: 20px; 
            padding: 10px; 
            background: #fdd; 
            border-radius: 5px; 
        }
        .warning {
            background: #ff9;
            padding: 10px;
            border-left: 4px solid #c00;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h2>⚠️ VULNERABLE AREA — Items</h2>
    
    <div class="warning">
        <strong>Warning:</strong> Area ini sengaja dibuat vulnerable untuk demo Broken Access Control (IDOR).
        ID menggunakan sequential integer yang bisa ditebak!
    </div>

    <div class="nav">
        <a href="create.php"><strong>+ Create New Item</strong></a> | 
        <a href="../index.php">← Back to Dashboard</a>
    </div>

    <?php if (empty($items)): ?>
        <p><em>Belum ada data. <a href="create.php">Buat item pertama</a></em></p>
    <?php else: ?>
        <table border="1" cellpadding="6">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Author</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['id']) ?></td>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <!-- Intentionally not escaped for stored XSS demonstration -->
                    <td><?= $r['content'] ?></td>
                    <td><?= htmlspecialchars($r['username']) ?></td>
                    <td>
                        <a href="edit.php?id=<?= $r['id'] ?>">Edit</a> |
                        <a href="delete.php?id=<?= $r['id'] ?>" 
                           onclick="return confirm('Hapus item ini?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div style="margin-top: 20px; padding: 10px; background: #fdd; border-radius: 5px;">
        <strong>IDOR Demo:</strong> Coba edit URL dengan ID berbeda (misalnya edit.php?id=1, id=2, dst.) 
        untuk mengakses data user lain!
    </div>
</body>
</html>