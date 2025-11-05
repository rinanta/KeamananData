<?php
require 'config.php';

if (empty($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        /* === RESET DAN FON === */
        * {
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
        }

        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
        }

        /* === NAVBAR (LOGOUT) === */
        .navbar {
            position: absolute;
            top: 20px;
            right: 30px;
        }

        .navbar a {
            text-decoration: none;
            background: rgba(255,255,255,0.2);
            color: #fff;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        .navbar a:hover {
            background: rgba(255,255,255,0.4);
        }

        /* === CARD UTAMA === */
        .dashboard {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 800px;
            animation: fadeIn 0.6s ease-out;
            margin-top: 80px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1 {
            text-align: center;
            color: #444;
            margin-bottom: 5px;
            font-size: 1.8em;
        }

        p.role {
            text-align: center;
            color: #777;
            margin-bottom: 30px;
            font-size: 0.95em;
        }

        /* === BOX CONTAINER === */
        .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .box {
            border-radius: 12px;
            padding: 25px;
            transition: all 0.3s ease;
            color: #222;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .vuln {
            background: #ffe3e3;
            border-left: 6px solid #d63031;
        }

        .safe {
            background: #e7ffe8;
            border-left: 6px solid #00b894;
        }

        .box h3 {
            margin-bottom: 10px;
        }

        .box p {
            font-size: 0.95em;
            margin-bottom: 15px;
            color: #555;
        }

        .box a {
            display: inline-block;
            text-decoration: none;
            background: #667eea;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .box a:hover {
            background: #5a67d8;
            transform: translateY(-2px);
        }

        /* === RESPONSIVE === */
        @media (max-width: 700px) {
            .container {
                grid-template-columns: 1fr;
            }
            .navbar {
                top: 15px;
                right: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="logout.php">Logout</a>
    </div>

    <div class="dashboard">
        <h1>Selamat Datang, <?= htmlspecialchars($user['username']) ?> 👋</h1>
        <p class="role">Role: <strong><?= htmlspecialchars($user['role']) ?></strong></p>

        <div class="container">
            <div class="box vuln">
                <h3>⚠️ VULNERABLE AREA</h3>
                <p>Contoh Broken Access Control (IDOR) — tanpa validasi ownership.</p>
                <a href="vuln/list.php">Masuk ke area VULN</a>
            </div>

            <div class="box safe">
                <h3>✅ SAFE AREA</h3>
                <p>Versi aman dengan UUID + Token + Ownership Check.</p>
                <a href="safe/list.php">Masuk ke area SAFE</a>
            </div>
        </div>
    </div>
</body>
</html>
