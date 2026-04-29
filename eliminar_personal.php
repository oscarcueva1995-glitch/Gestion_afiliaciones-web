<?php 
include("Conexion.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Ejecutamos la eliminación
    if ($conn->query("DELETE FROM afiliaciones_personal WHERE id=$id")) {
        header("Location: personal.php?deleted=1");
    } else {
        echo "Error al eliminar: " . $conn->error;
    }
} else {
    header("Location: personal.php");
}
?>