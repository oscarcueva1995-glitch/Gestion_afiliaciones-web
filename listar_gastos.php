<?php 
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit();
}
include("Conexion.php");

// Consulta de gastos
$resultado = $conn->query("SELECT * FROM gastos ORDER BY fecha DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Gastos | Gestión Elite</title>
    <style>
        :root { --accent: #3b82f6; --danger: #ef4444; --text: #f8fafc; }
        body {
            margin:0; font-family:'Inter', sans-serif; color: var(--text);
            background: url('https://image2url.com/r2/default/images/1773734375636-fc0f6144-5917-4a5f-be9b-df37ec8e183b.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .overlay {
            background: radial-gradient(circle, rgba(15,23,42,0.8) 0%, rgba(15,23,42,0.95) 100%);
            min-height: 100vh; backdrop-filter: blur(8px); padding: 30px 20px;
        }
        .container { max-width: 1100px; margin: 0 auto; }
        .card {
            background: rgba(30, 41, 59, 0.75); border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 25px; border-radius: 20px;
        }
        table { width:100%; border-collapse: collapse; margin-top: 15px; }
        th { text-align: left; font-size: 11px; color: var(--accent); text-transform: uppercase; padding: 12px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        td { padding: 12px; font-size: 14px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .badge { background: rgba(255,255,255,0.1); padding: 4px 10px; border-radius: 6px; font-size: 12px; }
        .btn-volver {
            display: inline-block; text-decoration: none; color: white; background: rgba(255,255,255,0.1);
            padding: 10px 20px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.2); margin-top: 20px;
        }
        .delete { color: var(--danger); text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
<div class="overlay">
    <div class="container">
        <div class="card">
            <h2 style="margin-top:0; color: var(--accent);">📋 Historial de Gastos</h2>
            <table>
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                <?php if($resultado && $resultado->num_rows > 0): ?>
                    <?php while($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><span class="badge"><?= ucfirst($fila['tipo']); ?></span></td>
                        <td><?= $fila['descripcion']; ?></td>
                        <td style="font-weight: bold;">S/ <?= number_format($fila['monto'], 2); ?></td>
                        <td style="opacity: 0.6; font-size: 13px;"><?= date('d/m/y', strtotime($fila['fecha'])); ?></td>
                        <td>
                            <a href="eliminar_gasto.php?id=<?= $fila['id_gasto']; ?>" class="delete" onclick="return confirm('¿Eliminar gasto?')">✕</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center; padding:30px; opacity:0.5;">No hay gastos registrados aún.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <a href="index.php" class="btn-volver">🏠 Dashboard Principal</a>
    </div>
</div>
</body>
</html>