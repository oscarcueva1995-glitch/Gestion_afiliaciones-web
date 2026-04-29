<?php 
include("Conexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $cantidad = $_POST['cantidad'];
    $fecha = $_POST['fecha'];

    // Nota: Para producción, considera usar sentencias preparadas para evitar inyección SQL
    $sql = "INSERT INTO afiliaciones_personal (nombre_personal, tipo, cantidad, fecha)
            VALUES ('$nombre', '$tipo', '$cantidad', '$fecha')";

    if ($conn->query($sql)) {
        header("Location: personal.php?ok=1");
    } else {
        echo "Error al guardar: " . $conn->error;
    }
} else {
    header("Location: personal.php");
}
?>