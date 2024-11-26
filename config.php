<?php
$host = 'localhost'; 
$dbname = 'web_egs'; 
$username = 'root';  
$password = ''; 

// Membuat koneksi MySQLi
$conn = new mysqli($host, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
