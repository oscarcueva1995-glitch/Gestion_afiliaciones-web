<?php 
include("Conexion.php");

// Recibiendo datos del formulario
$codigo    = $_POST['codigo'];
$cliente   = $_POST['cliente'];
$ubicacion = $_POST['ubicacion']; 
$telefono  = $_POST['telefono'];
$direccion = $_POST['direccion'];
$servicio  = $_POST['servicio'];
$fecha     = $_POST['fecha'];
$imagen    = $_POST['imagen']; // Asegúrate de que sea la URL o nombre del archivo

$sql = "INSERT INTO renovaciones (codigo, cliente, ubicacion, telefono, direccion, servicio, fecha, imagen)
        VALUES ('$codigo', '$cliente', '$ubicacion', '$telefono', '$direccion', '$servicio', '$fecha', '$imagen')";

if($conn->query($sql)){
    header("Location: renovaciones.php?success=1");
    exit();
} else {
    echo "Error en la base de datos: " . $conn->error;
}
?>