<?php
require 'config.php';

echo "<h3>Debug Login dengan password_verify</h3>";

// Test password verify langsung
$hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
$test_passwords = ['password123', 'admin123', 'password', '123456', 'admin'];

echo "<strong>Test password_verify dengan hash dari database:</strong><br>";
foreach ($test_passwords as $pwd) {
    $result = password_verify($pwd, $hash) ? '✓ MATCH' : '✗ NO MATCH';
    echo "Password '$pwd': $result<br>";
}

echo "<br><hr><br>";

// Test login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<strong>Testing Login Form:</strong><br>";
    
    $u = trim($_POST['username']);
    $p = $_POST['password'];
    
    echo "Input Username: '$u'<br>";
    echo "Input Password: '$p'<br><br>";
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u LIMIT 1");
    $stmt->execute([':u' => $u]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "✓ User ditemukan!<br>";
        echo "DB Username: '{$user['username']}'<br>";
        echo "DB Password Hash: '{$user['password']}'<br>";
        echo "DB Role: '{$user['role']}'<br><br>";
        
        $verify_result = password_verify($p, $user['password']);
        echo "password_verify() result: " . ($verify_result ? 'TRUE ✓' : 'FALSE ✗') . "<br>";
        
        if ($verify_result) {
            echo "<strong style='color:green'>✓✓✓ LOGIN BERHASIL! ✓✓✓</strong><br>";
        } else {
            echo "<strong style='color:red'>✗ Password tidak cocok!</strong><br>";
        }
    } else {
        echo "✗ User '$u' tidak ditemukan!<br>";
    }
}
?>

<hr>
<form method="post">
    <h4>Test Login:</h4>
    Username: <input name="username" value="admin"><br><br>
    Password: <input name="password" type="text" value="password123"><br><br>
    <button>Test Login</button>
</form>