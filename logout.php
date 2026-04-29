<?php
session_start();
include("Conexion.php"); // Asegúrate de que la 'C' coincida con el nombre del archivo

// ... (aquí va tu código de validación de usuario)

if($ok){
    $_SESSION['usuario'] = $user['nombre'];
    header('Location: index.php'); // Esto te lleva al panel de control
    exit();
} else {
    header('Location: login.php?error=1');
    exit();
}
?>