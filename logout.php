<?php
session_start();
session_unset(); // hapus semua data session
session_destroy(); // hancurkan session

// Redirect kembali ke halaman login
header("Location: login.php");
exit;
?>
