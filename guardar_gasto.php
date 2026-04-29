<?php 
session_start();
include("Conexion.php");

// Limpieza de datos recibidos
$tipo = $_POST['tipo_gasto'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$monto = floatval($_POST['monto'] ?? 0);
$fecha = $_POST['fecha'] ?? date('Y-m-d');

if($tipo == '' || $descripcion == '' || $monto <= 0){
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

// Inserción segura con prepare
$stmt = $conn->prepare("INSERT INTO gastos (tipo, descripcion, monto, fecha) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssds", $tipo, $descripcion, $monto, $fecha);

if($stmt->execute()){
    // Cálculos del mes para el balance
    $mes = date('m');
    $anio = date('Y');

    // Sumas por tipo
    $qComida = $conn->query("SELECT SUM(monto) as total FROM gastos WHERE tipo='comida' AND MONTH(fecha)='$mes' AND YEAR(fecha)='$anio'");
    $totalComida = $qComida->fetch_assoc()['total'] ?? 0;

    $qOtros = $conn->query("SELECT SUM(monto) as total FROM gastos WHERE tipo='otro' AND MONTH(fecha)='$mes' AND YEAR(fecha)='$anio'");
    $totalOtros = $qOtros->fetch_assoc()['total'] ?? 0;

    // Gastos fijos (SENATI, Laptop, Pandero, Alquiler, Personal)
    $gastosFijos = 314 + 122 + 100 + 250 + 800;

    // Ganancia bruta de afiliaciones
    $qGanancia = $conn->query("SELECT SUM(ganancia) as total FROM afiliaciones WHERE MONTH(fecha)='$mes' AND YEAR(fecha)='$anio'");
    $totalGanancia = $qGanancia->fetch_assoc()['total'] ?? 0;

    // Balance Final
    $balanceNeto = $totalGanancia - ($gastosFijos + $totalComida + $totalOtros);

    echo json_encode([
        'success' => true,
        'totalComida' => $totalComida,
        'balance' => $balanceNeto
    ]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}
?>