<?php
// 1. Corregir nombre de archivo y evitar errores de conexión
include("Conexion.php"); 

// FILTRO MES Y AÑO
$mes = $_GET['mes'] ?? date("m");
$anio = $_GET['anio'] ?? date("Y");

// SQL optimizado
$sql = "SELECT * FROM afiliaciones 
        WHERE MONTH(fecha) = '$mes' AND YEAR(fecha) = '$anio'
        ORDER BY id_afiliacion DESC";

$resultado = $conn->query($sql);

// VARIABLES DE CÁLCULO
$pago1 = 0;
$pago2 = 0;
$pendiente = 0;

// Usaremos un array para no tener que hacer dos consultas a la base de datos
$filas = [];

if ($resultado) {
    while($row = $resultado->fetch_assoc()){
        $filas[] = $row; // Guardamos la fila
        
        $dia = date("d", strtotime($row['fecha']));
        $ganancia = $row['ganancia'];

        // Lógica de quincenas de Pucallpa
        if($dia <= 15){
            $pago1 += $ganancia * 0.5;
            $pendiente += $ganancia * 0.5;
        } else {
            $pago2 += $ganancia;
        }
    }
}

$total = $pago1 + $pago2;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Afiliaciones | Gestión Elite</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="margin:0; font-family:'Segoe UI',sans-serif; background:#0f172a; color:white;">

<div style="background:rgba(0,0,0,0.8); min-height:100vh; padding:20px;">
    <div style="max-width:1100px; margin:auto;">
        
        <div style="display:flex; justify-content:space-between; align-items:center; background:#1e293b; padding:15px; border-radius:12px; margin-bottom:20px;">
            <h2 style="margin:0; font-size:18px;">📊 Registro de Afiliaciones</h2>
            <a href="index.php" style="background:#3b82f6; padding:8px 15px; border-radius:8px; text-decoration:none; color:white; font-size:13px; font-weight:bold;">⬅ Volver</a>
        </div>

        <form method="GET" style="background:#1e293b; padding:15px; border-radius:12px; margin-bottom:20px; display:flex; gap:10px; align-items:center; justify-content:center;">
            <span>Mes:</span>
            <input type="number" name="mes" value="<?= $mes ?>" min="1" max="12" style="background:#0f172a; border:1px solid #334155; color:white; padding:5px; border-radius:5px; width:60px;">
            <span>Año:</span>
            <input type="number" name="anio" value="<?= $anio ?>" style="background:#0f172a; border:1px solid #334155; color:white; padding:5px; border-radius:5px; width:80px;">
            <button style="background:#22c55e; color:white; border:none; padding:6px 12px; border-radius:5px; cursor:pointer; font-weight:bold;">🔍 Filtrar</button>
        </form>

        <div style="background:#1e293b; border-radius:12px; overflow:hidden; box-shadow:0 10px 15px -3px rgba(0,0,0,0.5);">
            <table style="width:100%; border-collapse:collapse; font-size:13px;">
                <thead style="background:#334155; color:#94a3b8; text-transform:uppercase;">
                    <tr>
                        <th style="padding:12px;">ID</th>
                        <th>Tipo</th>
                        <th>Cant.</th>
                        <th>Ganancia</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($filas as $row) { ?>
                    <tr style="border-bottom:1px solid #334155; text-align:center; transition:0.3s;" onmouseover="this.style.background='#1e293b'" onmouseout="this.style.background='transparent'">
                        <td style="padding:10px;"><?= $row['id_afiliacion']; ?></td>
                        <td>
                            <span style="background:#6366f1; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:bold;">
                                <?= strtoupper($row['tipo']); ?>
                            </span>
                        </td>
                        <td><?= $row['cantidad']; ?></td>
                        <td style="color:#22c55e; font-weight:bold;">S/ <?= number_format($row['ganancia'],2); ?></td>
                        <td><?= date("d/m/Y", strtotime($row['fecha'])); ?></td>
                        <td>
                            <a href="eliminar_afiliacion.php?id=<?= $row['id_afiliacion']; ?>" 
                               onclick="return confirm('¿Seguro que deseas eliminar este registro?')" 
                               style="background:#ef4444; padding:5px 10px; border-radius:6px; color:white; text-decoration:none; font-size:14px;">❌</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top:20px; display:grid; grid-template-cols: repeat(auto-fit, minmax(200px, 1fr)); gap:15px;">
            <div style="background:#0f172a; padding:15px; border-radius:12px; border-left:4px solid #22c55e; text-align:center;">
                <div style="color:#94a3b8; font-size:12px;">PAGO 1 (1ra Quincena)</div>
                <div style="font-size:18px; font-weight:bold;">S/ <?= number_format($pago1,2) ?></div>
            </div>
            <div style="background:#0f172a; padding:15px; border-radius:12px; border-left:4px solid #3b82f6; text-align:center;">
                <div style="color:#94a3b8; font-size:12px;">PAGO 2 (2da Quincena)</div>
                <div style="font-size:18px; font-weight:bold;">S/ <?= number_format($pago2,2) ?></div>
            </div>
            <div style="background:#0f172a; padding:15px; border-radius:12px; border-left:4px solid #f59e0b; text-align:center;">
                <div style="color:#94a3b8; font-size:12px;">PENDIENTE</div>
                <div style="font-size:18px; font-weight:bold; color:#f59e0b;">S/ <?= number_format($pendiente,2) ?></div>
            </div>
            <div style="background:#312e81; padding:15px; border-radius:12px; text-align:center; grid-column: span 1;">
                <div style="color:#c7d2fe; font-size:12px;">TOTAL MES</div>
                <div style="font-size:22px; font-weight:bold; color:#22c55e;">S/ <?= number_format($total,2) ?></div>
            </div>
        </div>

    </div>
</div>

</body>
</html>