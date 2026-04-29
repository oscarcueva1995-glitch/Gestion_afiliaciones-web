<?php 
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit();
}

include("Conexion.php");

// --- SOLUCIÓN PARA NGROK / CELULAR ---
// Detecta automáticamente el protocolo (http/https) y el dominio (localhost o ngrok)
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/Gestion_afiliaciones/";

// CONSULTAS PARA EL DASHBOARD
$personas = $conn->query("SELECT DISTINCT nombre_personal FROM afiliaciones_personal");

$totales = $conn->query("
    SELECT nombre_personal, SUM(cantidad) as total 
    FROM afiliaciones_personal 
    GROUP BY nombre_personal 
    ORDER BY total DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | Gestión de Personal Elite</title>

<style>
    :root {
        --accent: #3b82f6;
        --success: #10b981;
        --warning: #facc15;
        --danger: #ef4444;
        --text: #f8fafc;
    }

    body {
        font-family: 'Inter', 'Segoe UI', sans-serif;
        margin: 0;
        color: var(--text);
        background: url('https://image2url.com/r2/default/images/1773734375636-fc0f6144-5917-4a5f-be9b-df37ec8e183b.jpg') no-repeat center center fixed;
        background-size: cover;
    }

    .overlay {
        background: radial-gradient(circle, rgba(15,23,42,0.8) 0%, rgba(15,23,42,0.95) 100%);
        min-height: 100vh;
        backdrop-filter: blur(8px);
    }

    /* NAVBAR CORREGIDA */
    .navbar {
        background: rgba(31, 41, 55, 0.8);
        backdrop-filter: blur(15px);
        padding: 10px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        position: sticky;
        top: 0;
        z-index: 3000;
    }

    .navbar h3 { margin: 0; font-weight: 600; font-size: 18px; }

    .nav-buttons { display: flex; gap: 10px; align-items: center; }

    .navbar a, .btn-open {
        color: white;
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 8px;
        font-size: 13px;
        transition: 0.3s;
        border: none;
        cursor: pointer;
    }

    .btn-open { background: var(--accent); font-weight: bold; }
    .volver { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); }
    .logout { background: var(--danger); }

    .container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 30px 20px;
    }

    /* MODAL */
    .modal {
        display: none; 
        position: fixed; 
        z-index: 4000; 
        left: 0; top: 0; width: 100%; height: 100%; 
        background: rgba(0,0,0,0.85); 
        justify-content: center; 
        align-items: center;
    }

    .modal-content {
        background: #1e293b;
        padding: 25px;
        border-radius: 20px;
        width: 90%;
        max-width: 400px;
        border: 1px solid rgba(255,255,255,0.1);
    }

    /* CARDS Y RANKING */
    .card {
        background: rgba(30, 41, 59, 0.75);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 25px;
        border-radius: 20px;
        margin-bottom: 25px;
    }

    .ranking-item {
        background: rgba(255,255,255,0.05);
        margin-bottom: 15px;
        padding: 15px;
        border-radius: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .status-badge { font-size: 11px; font-weight: bold; padding: 4px 10px; border-radius: 6px; margin-top: 8px; display: inline-block; color: white; }

    .progress-circle {
        width: 70px; height: 70px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; position: relative;
        background: conic-gradient(var(--color) var(--progress), rgba(255,255,255,0.1) 0deg);
    }

    .progress-circle::after {
        content: attr(data-pct); position: absolute; width: 55px; height: 55px;
        background: #1e293b; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;
    }

    input, select {
        width: 100%; padding: 12px; margin-bottom: 12px; border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.2); background: #0f172a; color: white; box-sizing: border-box;
    }

    .btn-submit { width: 100%; padding: 14px; background: var(--accent); color: white; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; text-transform: uppercase; }

    .premio-box { margin-top: 20px; padding: 15px; background: rgba(16, 185, 129, 0.1); border: 1px solid var(--success); border-radius: 15px; text-align: center; }
    .premio-box img { width: 140px; border-radius: 10px; margin-bottom: 10px; }

    table { width: 100%; border-collapse: collapse; }
    th { text-align: left; font-size: 11px; color: var(--accent); padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.1); }
    td { padding: 10px; font-size: 13px; border-bottom: 1px solid rgba(255,255,255,0.05); }

    .historial-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; }
    .delete { color: var(--danger); text-decoration: none; font-weight: bold; }
</style>
</head>

<body>

<div class="overlay">

    <div class="navbar">
        <h3>👥 Gestión Personal</h3>
        <div class="nav-buttons">
            <button class="btn-open" onclick="abrirModal()">+ REGISTRAR</button>
            <a href="<?= $base_url; ?>index.php" class="volver">🏠 Panel</a>
            <a href="<?= $base_url; ?>logout.php" class="logout">Salir</a>
        </div>
    </div>

    <div class="container">

        <div class="card">
            <h3>🏆 Ranking Mensual</h3>
            <div class="ranking-container">
                <?php 
                $pos = 1;
                while($t = $totales->fetch_assoc()){ 
                    $meta = 150; 
                    $minimo_pago = 138; 
                    $total = $t['total'];
                    $porcentaje = min(100, round(($total / $meta) * 100));
                    $grados = ($porcentaje / 100) * 360;

                    if($total >= $meta){ 
                        $color = "#3b82f6"; $estado = "💎 Meta Superada"; $bg_badge = "#1d4ed8"; 
                    } elseif($total >= $minimo_pago){ 
                        $color = "#10b981"; $estado = "✔ Pago Completo"; $bg_badge = "#047857"; 
                    } elseif($total >= 100){ 
                        $color = "#facc15"; $estado = "⚠️ En progreso"; $bg_badge = "#a16207"; 
                    } else { 
                        $color = "#ef4444"; $estado = "❌ Bajo rendimiento"; $bg_badge = "#b91c1c"; 
                    }

                    $medal = ($pos==1?"🥇 ":($pos==2?"🥈 ":($pos==3?"🥉 ":"")));
                ?>
                    <div class="ranking-item">
                        <div>
                            <b><?= $medal . $t['nombre_personal']; ?></b>
                            <span style="display:block; opacity: 0.7; font-size: 13px; margin-top: 4px;"><?= $total; ?> / <?= $meta; ?> acumuladas</span>
                            <span class="status-badge" style="background: <?= $bg_badge; ?>;"><?= $estado; ?></span>
                        </div>
                        <div class="progress-circle" data-pct="<?= $porcentaje; ?>%" style="--progress: <?= $grados; ?>deg; --color: <?= $color; ?>;"></div>
                    </div>
                <?php $pos++; } ?>
            </div>

            <div class="premio-box">
                <img src="https://image2url.com/r2/default/images/1773815124926-78920847-73ef-403f-8ba8-cb080b130958.jpeg" alt="Premio">
                <h3 style="color: #34d399; margin: 5px 0; border:none; font-size: 20px;">S/ 200 + S/ 50 al 1° Lugar</h3>
                <p style="opacity: 0.8; font-size: 12px; margin:0;">Meta: 150 | Pago completo desde 138</p>
            </div>
        </div>

        <h2 style="margin-bottom: 20px; font-weight: 400; color: var(--accent);">📄 Historial Detallado</h2>

        <div class="historial-grid">
            <?php 
            $personas->data_seek(0);
            while($p = $personas->fetch_assoc()){ 
                $nombre = $p['nombre_personal'];
                $historial = $conn->query("SELECT * FROM afiliaciones_personal WHERE nombre_personal='$nombre' ORDER BY id DESC LIMIT 15");
            ?>
            <div class="card">
                <h4 style="margin: 0 0 10px 0; color: var(--accent); border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 5px;"><?= $nombre; ?></h4>
                <table>
                    <thead>
                        <tr><th>Tipo</th><th>Cant</th><th>Fecha</th><th></th></tr>
                    </thead>
                    <tbody>
                        <?php while($row = $historial->fetch_assoc()){ ?>
                        <tr>
                            <td><?= ucfirst($row['tipo']); ?></td>
                            <td><b><?= $row['cantidad']; ?></b></td>
                            <td style="opacity: 0.6; font-size: 11px;"><?= date('d/m/y', strtotime($row['fecha'])); ?></td>
                            <td><a class="delete" href="<?= $base_url; ?>eliminar_personal.php?id=<?= $row['id']; ?>" onclick="return confirm('¿Eliminar?')">✕</a></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<div id="modalRegistro" class="modal">
    <div class="modal-content">
        <h3 style="margin-top:0; color: var(--accent);">✍️ Nueva Afiliación</h3>
        <form action="<?= $base_url; ?>guardar_personal.php" method="POST">
            <input type="text" name="nombre" placeholder="Nombre completo" required>
            <select name="tipo" required>
                <option value="">-- Tipo --</option>
                <option value="rebranding">Rebranding</option>
                <option value="nueva">Nueva Afiliación</option>
            </select>
            <input type="number" name="cantidad" placeholder="Cantidad" required>
            <input type="date" name="fecha" value="<?= date('Y-m-d'); ?>" required>
            <button type="submit" class="btn-submit">GUARDAR</button>
            <button type="button" onclick="cerrarModal()" style="background:none; border:none; color:#888; width:100%; margin-top:15px; cursor:pointer;">Cancelar</button>
        </form>
    </div>
</div>

<script>
    function abrirModal() { document.getElementById("modalRegistro").style.display = "flex"; }
    function cerrarModal() { document.getElementById("modalRegistro").style.display = "none"; }
    window.onclick = function(event) {
        if (event.target == document.getElementById("modalRegistro")) { cerrarModal(); }
    }
</script>

</body>
</html>
