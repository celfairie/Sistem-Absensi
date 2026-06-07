<?php
$host = 'localhost';
$dbname = 'db_absensi';
$user = 'root'; // User bawaan Laragon
$pass = '';     // Password bawaan Laragon kosong

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    // Set error mode ke exception agar error database mudah dilacak
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi ke database gagal: " . $e->getMessage());
}
?>