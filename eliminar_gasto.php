<?php 
// 1. Siempre iniciar sesión y conexión primero
session_start();
include("Conexion.php");

// 2. Verificar que el ID llegue por la URL
if (isset($_GET['id'])) {
    // Convertimos a entero para evitar errores de tipo y ataques SQL
    $id = (int)$_GET['id'];

    // 3. Importante: Verifica si en tu tabla es 'id' o 'id_gasto'
    // Según tu archivo anterior, usaremos 'id_gasto'
    $stmt = $conn->prepare("DELETE FROM gastos WHERE id_gasto = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirigir con éxito
        header("Location: listar_gastos.php?mensaje=eliminado");
        exit();
    } else {
        // Si hay error en la base de datos
        echo "Error al eliminar: " . $conn->error;
    }
} else {
    // Si alguien entra sin ID, regresarlo a la lista
    header("Location: listar_gastos.php");
    exit();
}
?>