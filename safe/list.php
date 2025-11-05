<?php
// safe/list.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$stmt = $pdo->prepare("SELECT id, uuid, title, created_at FROM items_safe WHERE user_id = :u ORDER BY created_at DESC");
$stmt->execute([':u' => $_SESSION['user']['id']]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>SAFE — Items (Your Items)</title>
<style>
    /* ==== Reset dan dasar ==== */
    * { box-sizing: border-box; font-family: "Poppins", sans-serif; margin: 0; padding: 0; }
    body {
        background: linear-gradient(135deg, #6be6a4, #43c7f4);
        min-height: 100vh;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 40px 20px;
        color: #222;
    }

    /* ==== Container utama ==== */
    .card {
        width: 100%;
        max-width: 900px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        padding: 28px 32px;
        animation: fadeIn .4s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ==== Header ==== */
    header.top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    header.top h2 {
        font-size: 1.5rem;
        color: #15803d;
    }
    header.top nav a {
        text-decoration: none;
        color: #15803d;
        font-weight: 600;
        margin-left: 16px;
        transition: color .2s ease;
    }
    header.top nav a:hover {
        color: #0d5f2e;
        text-decoration: underline;
    }

    /* ==== Tabel ==== */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 0.95rem;
    }
    thead {
        background: #e9f9ee;
    }
    th, td {
        padding: 12px 14px;
        text-align: left;
        border-bottom: 1px solid #e3e3e3;
    }
    th {
        font-weight: 600;
        color: #0e5d2e;
    }
    tr:hover td {
        background: #f8fef9;
    }

    /* ==== Tombol & Link ==== */
    .action-links a,
    .action-links button {
        margin-right: 6px;
        text-decoration: none;
        display: inline-block;
        padding: 6px 10px;
        font-size: 0.9rem;
        border-radius: 6px;
        cursor: pointer;
        border: none;
        transition: all 0.15s ease;
    }
    .action-links a.view {
        background: #d1fae5;
        color: #065f46;
    }
    .action-links a.edit {
        background: #bfdbfe;
        color: #1e3a8a;
    }
    .action-links button {
        background: #fee2e2;
        color: #991b1b;
    }
    .action-links a.view:hover { background: #a7f3d0; }
    .action-links a.edit:hover { background: #93c5fd; }
    .action-links button:hover { background: #fecaca; }

    /* ==== Info kosong ==== */
    .empty {
        text-align: center;
        padding: 30px;
        color: #555;
        font-style: italic;
    }

    /* ==== Responsif ==== */
    @media (max-width: 640px) {
        th, td { padding: 8px; font-size: 0.88rem; }
        header.top h2 { font-size: 1.25rem; }
        header.top nav a { margin-left: 10px; font-size: 0.9rem; }
    }
</style>
</head>
<body>
  <div class="card">
    <header class="top">
      <h2>🛡 SAFE — Your Items</h2>
      <nav>
        <a href="create.php">+ Create</a>
        <a href="../index.php">Dashboard</a>
      </nav>
    </header>

    <?php if (empty($rows)): ?>
        <p class="empty">Belum ada item. Yuk, tambahkan item pertama kamu!</p>
    <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>UUID</th>
          <th>Title</th>
          <th>Created</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rows as $r): ?>
        <tr>
          <td><?=htmlspecialchars($r['uuid'])?></td>
          <td><?=htmlspecialchars($r['title'])?></td>
          <td><?=htmlspecialchars($r['created_at'])?></td>
          <td class="action-links">
            <a class="view" href="view.php?u=<?=urlencode($r['uuid'])?>">View</a>
            <a class="edit" href="edit.php?u=<?=urlencode($r['uuid'])?>">Edit</a>
            <form action="delete.php" method="post" style="display:inline" onsubmit="return confirm('Delete item ini?')">
              <input type="hidden" name="uuid" value="<?=htmlspecialchars($r['uuid'])?>">
              <input type="hidden" name="csrf" value="<?=csrf_token()?>">
              <button>Delete</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</body>
</html>
