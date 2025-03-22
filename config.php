<?php
$host = "localhost";
$username = "root"; // Thay đổi nếu username khác
$password = "";     // Thay đổi nếu có mật khẩu
$dbname = "app";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>