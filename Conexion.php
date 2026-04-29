<?php
// Datos del servidor real en InfinityFree
$host = "sql100.infinityfree.com";
$user = "if0_41715471";
$pass = "CONTRASEÑA_PROTEGIDA"; // Tu contraseña de hosting
$db   = "if0_41715471_db_gestion";


$conn = new mysqli($host, $user, $pass, $db);

// Ajustar caracteres a utf8mb4 para soportar todos los caracteres y emojis
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
