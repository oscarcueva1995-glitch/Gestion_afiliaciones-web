<?php 
session_start();
include("Conexion.php");

// 1. Obtener la ganancia de la primera quincena del mes actual
$sql = "SELECT IFNULL(SUM(ganancia), 0) as total FROM afiliaciones 
        WHERE DAY(fecha) BETWEEN 1 AND 15 
        AND MONTH(fecha) = MONTH(CURDATE()) 
        AND YEAR(fecha) = YEAR(CURDATE())";

$resultado = $conn->query($sql);
$row = $resultado->fetch_assoc();
$totalQuincena = $row['total'];

if ($totalQuincena > 0) {

    $pagoParcial = $totalQuincena / 2;

    // 2. Definir fechas de pago de forma dinámica
    // Pago 1: Día 24 del mes actual
    $fechaPago1 = date('Y-m-24');
    
    // Pago 2: Día 15 del próximo mes (Cálculo robusto)
    $fechaPago2 = date('Y-m-15', strtotime('first day of next month'));

    // 3. Revisar si ya se generaron estos pagos para el mes actual
    // Usamos el mes y año en la descripción para un control más preciso
    $mesActual = date('m-Y');
    $desc1 = "50% primera quincena ($mesActual)";
    $desc2 = "50% restante ($mesActual)";

    $check1 = $conn->query("SELECT id_pago FROM pagos WHERE descripcion='$desc1'")->num_rows;
    $check2 = $conn->query("SELECT id_pago FROM pagos WHERE descripcion='$desc2'")->num_rows;

    $mensajes = [];

    // Insertar Pago 1
    if ($check1 == 0) {
        $sql1 = "INSERT INTO pagos (fecha_pago, monto, estado, descripcion)
                 VALUES ('$fechaPago1', '$pagoParcial', 'pendiente', '$desc1')";
        if($conn->query($sql1)) $mensajes[] = "✅ Pago del día 24 generado.";
    } else {
        $mensajes[] = "ℹ️ El pago del día 24 ya estaba registrado.";
    }

    // Insertar Pago 2
    if ($check2 == 0) {
        $sql2 = "INSERT INTO pagos (fecha_pago, monto, estado, descripcion)
                 VALUES ('$fechaPago2', '$pagoParcial', 'pendiente', '$desc2')";
        if($conn->query($sql2)) $mensajes[] = "✅ Pago del día 15 (próximo mes) generado.";
    } else {
        $mensajes[] = "ℹ️ El pago del día 15 ya estaba registrado.";
    }

    // Mostrar resumen
    foreach($mensajes as $m) echo $m . "<br>";

} else {
    echo "⚠️ No hay ganancias registradas en la primera quincena de este mes.";
}
?>