<?php
// 1. Corregir espacio en include y usar el nombre exacto del archivo
include("Conexion.php"); 

// 2. Recibir datos con una validación mínima
$tipo     = $_POST['tipo'] ?? '';
$cantidad = (int)($_POST['cantidad'] ?? 0);
$fecha    = $_POST['fecha'] ?? date('Y-m-d'); // Si no hay fecha, usa la de hoy

if ($cantidad > 0 && !empty($tipo)) {
    
    // 💰 PRECIO POR TIPO (Lógica de negocio Pucallpa)
    if($tipo == "rebranding"){
        $precio = 5.5;
    } else {
        $precio = 3;
    }

    // ✅ CALCULAR GANANCIA
    $ganancia = $cantidad * $precio;

    // ✅ GUARDAR (Usando comillas para evitar errores de sintaxis SQL)
    $sql = "INSERT INTO afiliaciones (tipo, cantidad, ganancia, fecha) 
            VALUES ('$tipo', '$cantidad', '$ganancia', '$fecha')";

    if($conn->query($sql)) {
        // Redirigir con éxito
        header("Location: index.php?status=success");
    } else {
        // Mostrar error si la tabla no existe o los campos están mal
        echo "Error en la base de datos: " . $conn->error;
    }

} else {
    // Si los datos están vacíos, volver al inicio
    header("Location: index.php?status=error");
}
exit();
?>