<?php
header('Content-Type: application/json');
include("Conexion.php"); // Asegúrate de que la 'C' coincida con tu archivo

// Recibir datos de forma segura
$usuario = mysqli_real_escape_string($conn, $_POST['usuario'] ?? '');
$lat     = mysqli_real_escape_string($conn, $_POST['lat'] ?? '');
$lng     = mysqli_real_escape_string($conn, $_POST['lng'] ?? '');

if (empty($usuario) || empty($lat) || empty($lng)) {
    echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
    exit;
}

// 1. Verificar si el usuario ya tiene una ubicación registrada
$existe = $conn->query("SELECT id_ubicacion FROM ubicaciones WHERE usuario='$usuario'");

if ($existe && $existe->num_rows > 0) {
    // 2. Actualizar ubicación existente con la fecha actual (NOW())
    $sql = "UPDATE ubicaciones SET latitud='$lat', longitud='$lng', fecha=NOW() WHERE usuario='$usuario'";
} else {
    // 3. Insertar nueva ubicación
    $sql = "INSERT INTO ubicaciones (usuario, latitud, longitud, fecha) VALUES ('$usuario', '$lat', '$lng', NOW())";
}

if ($conn->query($sql)) {
    echo json_encode(["status" => "success", "message" => "Ubicación actualizada"]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}
?>