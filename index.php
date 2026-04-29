<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit();
}
include("Conexion.php");

// --- Consultas (Tu lógica original intacta) ---
$res_1_15 = $conn->query("SELECT IFNULL(SUM(cantidad),0) AS total_cant, IFNULL(SUM(ganancia),0) AS total_ganancia FROM afiliaciones WHERE DAY(fecha) BETWEEN 1 AND 15 AND MONTH(fecha)=MONTH(CURDATE()) AND YEAR(fecha)=YEAR(CURDATE())")->fetch_assoc();
$res_16_fin = $conn->query("SELECT IFNULL(SUM(cantidad),0) AS total_cant, IFNULL(SUM(ganancia),0) AS total_ganancia FROM afiliaciones WHERE DAY(fecha) BETWEEN 16 AND 31 AND MONTH(fecha)=MONTH(CURDATE()) AND YEAR(fecha)=YEAR(CURDATE())")->fetch_assoc();

$quincena_1_15_pago_24 = $res_1_15['total_ganancia'] * 0.5;
$quincena_1_15_pago_16_sig = $res_1_15['total_ganancia'] * 0.5;
$quincena_16_fin_pago_fin = $res_16_fin['total_ganancia'];
$ganancia_inmediata = $quincena_1_15_pago_24 + $quincena_16_fin_pago_fin;
$ganancia_diferida = $quincena_1_15_pago_16_sig;

$gastos_fijos = ['SENATI' => 314, 'Laptop' => 122, 'Pandero' => 100, 'Alquiler' => 250, 'Pago Personal' => 800];
$totalGastosFijos = array_sum($gastos_fijos);
$totalGastos = $conn->query("SELECT IFNULL(SUM(monto),0) AS total FROM gastos WHERE MONTH(fecha)=MONTH(CURDATE()) AND YEAR(fecha)=YEAR(CURDATE())")->fetch_assoc()['total'];
$balance = $ganancia_inmediata - ($totalGastosFijos + $totalGastos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Flotante Pro</title>
    <style>
        body {
            margin:0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('https://image2url.com/r2/default/images/1773734375636-fc0f6144-5917-4a5f-be9b-df37ec8e183b.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
        }
        .overlay {
            background: rgba(0, 0, 0, 0.85);
            min-height: 100vh;
            padding: 20px 15px;
            box-sizing: border-box;
        }
        .card {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: 0.3s;
            backdrop-filter: blur(8px);
            cursor: pointer;
        }
        .card:active { transform: scale(0.98); }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        /* --- ESTILOS DEL MODAL (VENTANA FLOTANTE) --- */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 999; 
            left: 0; top: 0; width: 100%; height: 100%; 
            background-color: rgba(0,0,0,0.9);
            justify-content: center; 
            align-items: center;
        }
        .modal-content {
            background: #1c1f2b;
            padding: 25px;
            border-radius: 20px;
            width: 90%;
            max-width: 400px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        input, select, button {
            width: 100%;
            padding: 14px;
            margin-top: 10px;
            border-radius: 10px;
            border: none;
            font-size: 16px;
            box-sizing: border-box;
        }
        button { font-weight: bold; text-transform: uppercase; cursor: pointer; }

        .actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 10px;
            margin-top: 20px;
        }
        .actions a {
            text-align: center;
            padding: 12px;
            text-decoration: none;
            color: white;
            border-radius: 10px;
            font-size: 13px;
            font-weight: bold;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .logout { background: #ff4757; color: white; padding: 10px 18px; border-radius: 10px; text-decoration: none; font-size: 14px;}
        hr { border: 0; height: 1px; background: rgba(255,255,255,0.1); margin: 25px 0; }
        ul { padding-left: 20px; font-size: 14px; opacity: 0.8; }
    </style>
</head>
<body>
<div class="overlay">

    <div class="header">
        <h2>👋 Hola, <?php echo $_SESSION['usuario']; ?></h2>
        <a href="logout.php" class="logout">Salir</a>
    </div>

    <div class="grid">
        <div class="card" style="border-bottom: 4px solid #17a2b8;">
            <small style="color: #17a2b8;">Quincena 1-15</small>
            <h4>Pago 24: S/ <?php echo number_format($quincena_1_15_pago_24,2); ?></h4>
            <h4>Pago 16: S/ <?php echo number_format($quincena_1_15_pago_16_sig,2); ?></h4>
        </div>
        <div class="card" style="border-bottom: 4px solid #6610f2;">
            <small style="color: #6610f2;">Quincena 16-Fin</small>
            <h4 style="font-size: 1.4rem;">S/ <?php echo number_format($quincena_16_fin_pago_fin,2); ?></h4>
        </div>
        <div class="card" style="border-bottom: 4px solid #28a745;">
            <small style="color: #28a745;">Ganancia Hoy</small>
            <h4 style="font-size: 1.8rem;">S/ <?php echo number_format($ganancia_inmediata,2); ?></h4>
        </div>
    </div>

    <div class="grid">
        <div class="card" onclick="abrirModal('modalAfi')" style="text-align: center; background: rgba(40, 167, 69, 0.2);">
            <h3 style="margin:0;">📝 NUEVA AFILIACIÓN</h3>
            <small>Toca para registrar</small>
        </div>
        <div class="card" onclick="abrirModal('modalGasto')" style="text-align: center; background: rgba(255, 71, 87, 0.2);">
            <h3 style="margin:0;">💸 REGISTRAR GASTO</h3>
            <small>Toca para registrar</small>
        </div>
    </div>

    <hr>

    <div class="grid">
        <div class="card">
            <small>Fijos</small>
            <h4 style="color:#ff4757;">S/ <?php echo number_format($totalGastosFijos,2); ?></h4>
            <ul>
                <?php foreach($gastos_fijos as $n => $m) echo "<li>$n: S/ $m</li>"; ?>
            </ul>
        </div>
        <div class="card">
            <small>Variables</small>
            <h4 id="totalGastosComida" style="color:#ffa502;">S/ <?php echo number_format($totalGastos,2); ?></h4>
        </div>
        <div class="card" style="background: rgba(40, 167, 69, 0.2);">
            <small>Balance Neto</small>
            <h4 id="balanceActual" style="font-size: 2rem; color: #2ed573;">S/ <?php echo number_format($balance,2); ?></h4>
        </div>
    </div>

    <div class="actions">
        <a href="personal.php" style="border-color: #17a2b8;">Personal</a>
        <a href="listar.php">Afiliaciones</a>
        <a href="listar_gastos.php">Gastos</a>
        <a href="pagos.php">Pagos</a>
        <a href="renovaciones.php">Renovaciones</a>
    </div>
</div>

<div id="modalAfi" class="modal">
    <div class="modal-content">
        <h3 style="margin-top:0;">📝 Nueva Afiliación</h3>
        <form action="guardar_afiliacion.php" method="POST">
            <select name="tipo" required>
                <option value="">¿Qué tipo es?</option>
                <option value="rebranding">Rebranding</option>
                <option value="nueva">Nueva</option>
            </select>
            <input type="number" name="cantidad" placeholder="¿Cuántas?" required>
            <input type="date" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
            <button type="submit" style="background: #28a745; color: white;">Guardar</button>
            <button type="button" onclick="cerrarModal('modalAfi')" style="background:none; color:#888;">Cancelar</button>
        </form>
    </div>
</div>

<div id="modalGasto" class="modal">
    <div class="modal-content">
        <h3 style="margin-top:0; color: #ff4757;">💸 Registrar Gasto</h3>
        <form id="formGasto">
            <select name="tipo_gasto" required>
                <option value="comida">Comida</option>
                <option value="otro">Otro</option>
            </select>
            <input type="text" name="descripcion" placeholder="¿En qué gastaste?" required>
            <input type="number" step="0.01" name="monto" placeholder="S/ Monto" required>
            <input type="date" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
            <button type="submit" style="background: #ff4757; color: white;">Registrar Gasto</button>
            <button type="button" onclick="cerrarModal('modalGasto')" style="background:none; color:#888;">Cancelar</button>
        </form>
    </div>
</div>

<script>
function abrirModal(id) { document.getElementById(id).style.display = "flex"; }
function cerrarModal(id) { document.getElementById(id).style.display = "none"; }

// Tu lógica de AJAX original para gastos
const formGasto = document.getElementById('formGasto');
formGasto.addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);
    fetch('guardar_gasto.php', { method:'POST', body:formData })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            document.querySelector('#totalGastosComida').innerText = 'S/ ' + parseFloat(data.totalGastos).toFixed(2);
            document.querySelector('#balanceActual').innerText = 'S/ ' + parseFloat(data.balance).toFixed(2);
            formGasto.reset();
            cerrarModal('modalGasto');
        }
    });
});

// Cerrar al hacer clic fuera del cuadro
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = "none";
    }
}
</script>
</body>
</html>