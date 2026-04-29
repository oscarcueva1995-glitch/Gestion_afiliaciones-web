<?php
// 1. Cabecera para que el celular sepa que recibe datos procesables (JSON)
header('Content-Type: application/json; charset=utf-8');

// 2. Errores desactivados para producción (así no rompen el JSON), 
// pero configurados para que no detengan el script.
ini_set('display_errors', 0); 

if(!isset($_GET['numero'])){
    echo json_encode(["status" => "error", "message" => "Número no recibido"]);
    exit;
}

$numero = $_GET['numero'];

// 3. Limpieza del número (solo dígitos)
$numero = preg_replace('/[^0-9]/', '', $numero);

if(strlen($numero) < 9){ // En Perú los celulares tienen 9 dígitos
    echo json_encode(["status" => "error", "message" => "Número inválido (requiere 9 dígitos)"]);
    exit;
}

// --- AQUÍ CONECTAMOS CON TU LÓGICA DE NEGOCIO ---
// Ejemplo: Consultar si este número ya fue afiliado hoy en Pucallpa
include("Conexion.php");

$consulta = $conn->query("SELECT * FROM afiliaciones_personal WHERE nombre_personal LIKE '%$numero%' LIMIT 1");

if($consulta && $consulta->num_rows > 0) {
    $datos = $consulta->fetch_assoc();
    $respuesta = [
        "status" => "success",
        "numero" => $numero,
        "estado" => "Ya registrado",
        "gestor" => $datos['nombre_personal'],
        "mensaje" => "Este número ya fue atendido hoy"
    ];
} else {
    // Si es nuevo
    $respuesta = [
        "status" => "success",
        "numero" => $numero,
        "estado" => "Disponible",
        "mensaje" => "Número libre para nueva afiliación"
    ];
}

// 4. Enviar la respuesta final al celular
echo json_encode($respuesta);
?>