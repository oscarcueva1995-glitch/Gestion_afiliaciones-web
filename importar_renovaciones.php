<?php
session_start();
// Usamos el nombre exacto que tienes en el servidor
include("Conexion.php"); 

if(isset($_POST['datos'])){
    $lineas = explode("\n", $_POST['datos']);
    $contador = 0;

    foreach($lineas as $linea){
        $linea = trim($linea);
        if(empty($linea)) continue;

        // Separar por TAB (Excel usa tabulaciones)
        $datos = preg_split('/\t+/', $linea);

        // Validamos que al menos existan los datos básicos (Servicio, Cliente, Fecha)
        if(count($datos) >= 7){

            $servicio  = $conn->real_escape_string(trim($datos[0]));
            $cliente   = $conn->real_escape_string(trim($datos[1]));
            $direccion = $conn->real_escape_string(trim($datos[2]));
            $latitud   = $conn->real_escape_string(trim($datos[3]));
            $longitud  = $conn->real_escape_string(trim($datos[4]));
            $telefono  = $conn->real_escape_string(trim($datos[5]));

            // 🔥 CORRECCIÓN DE FECHA: Limpiar espacios invisibles
            $fecha_raw = trim($datos[6]);
            $fecha = null;

            // Formato esperado: DD/MM/YYYY
            if(preg_match('/\d{1,2}\/\d{1,2}\/\d{4}/', $fecha_raw)){
                $partes = explode('/', $fecha_raw);
                // Convertir a formato MySQL: YYYY-MM-DD
                $fecha = $partes[2]."-".$partes[1]."-".$partes[0];
            }

            // Datos opcionales
            $qr     = isset($datos[7]) ? $conn->real_escape_string(trim($datos[7])) : '';
            $imagen = isset($datos[8]) ? $conn->real_escape_string(trim($datos[8])) : '';

            // ⚖️ Lógica de Distribución Equitativa
            $gestor_asignado = ($contador % 2 == 0) ? 'A' : 'B';

            // Solo insertar si la fecha es válida para evitar basura en la BD
            if($fecha){
                $sql = "INSERT INTO renovaciones 
                (servicio, cliente, direccion, telefono, fecha, qr, imagen, latitud, longitud, gestor)
                VALUES 
                ('$servicio','$cliente','$direccion','$telefono','$fecha','$qr','$imagen','$latitud','$longitud', '$gestor_asignado')";

                if($conn->query($sql)){
                    $contador++;
                }
            }
        }
    }

    // Redirigir con mensaje de éxito usando SweetAlert o JS
    echo "<script>alert('✅ Se importaron " . $contador . " registros repartidos entre A y B'); window.location='renovaciones.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Importar Renovaciones | Gestión Elite</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body{ font-family:'Segoe UI', sans-serif; padding:20px; background:#0f172a; color:white; }
        .container{ max-width: 800px; margin: 0 auto; }
        textarea{ width:100%; border-radius:12px; padding:15px; background:#1e293b; color:#cbd5e1; border:1px solid #334155; font-size:13px; resize: vertical; }
        button{ padding:12px 24px; background:#22c55e; color:white; border:none; border-radius:8px; cursor:pointer; font-weight:bold; transition: 0.3s; width: 100%; }
        button:hover{ background:#16a34a; transform: translateY(-2px); }
        a{ color:#94a3b8; text-decoration:none; font-size: 14px; }
        .info-box{ background: rgba(59, 130, 246, 0.1); padding:15px; border-radius:10px; margin-bottom:20px; border:1px solid #3b82f6; color: #93c5fd; }
    </style>
</head>
<body>

<div class="container">
    <h2>📥 Importar Renovaciones</h2>

    <div class="info-box">
        💡 <b>Instrucciones:</b> Copia las columnas desde tu Excel (Servicio, Cliente, Dirección, Lat, Lng, Tel, Fecha) y pégalas aquí. El sistema balanceará la carga entre tus gestores.
    </div>

    <form method="POST">
        <textarea name="datos" rows="12" placeholder="Servicio	Cliente	Dirección	Latitud	Longitud	Teléfono	Fecha(DD/MM/YYYY)"></textarea>
        <br><br>
        <button type="submit">🚀 Iniciar Carga de Datos</button>
    </form>

    <br>
    <center><a href="renovaciones.php">⬅ Volver al Panel de Control</a></center>
</div>

</body>
</html>