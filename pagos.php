<?php
declare(strict_types=1);
session_start();

// 1. Capa de Seguridad y Conexión
if (!isset($_SESSION['usuario'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
        exit;
    }
    header('Location: login.php');
    exit;
}

require_once "Conexion.php"; // Corregido espacio

/**
 * CONTROLADOR DE LÓGICA DE NEGOCIO - GESTIÓN DE PAGOS
 */
class PaymentController {
    private mysqli $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
        // Permite que los números vengan como float/int y no como strings
        $this->db->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true);
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $action = $data['action'] ?? '';

            switch ($action) {
                case 'generar': return $this->generarPagos();
                case 'actualizar_estado': return $this->cambiarEstado((int)$data['id'], $data['nuevo_estado']);
                case 'eliminar': return $this->eliminarPago((int)$data['id']);
                default: return ['status' => 'error', 'message' => 'Acción no válida'];
            }
        }
        return null;
    }

    private function generarPagos(): array {
        // Consulta de ganancias de la 1ra quincena
        $query = "SELECT IFNULL(SUM(ganancia), 0) as total FROM afiliaciones 
                  WHERE DAY(fecha) BETWEEN 1 AND 15 
                  AND MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())";
        
        $res = $this->db->query($query);
        $total = (float)($res->fetch_assoc()['total'] ?? 0);

        if ($total <= 0) return ['status' => 'warning', 'message' => 'No hay ganancias registradas en la primera quincena.'];

        $this->db->begin_transaction();
        try {
            $mesActual = date('m-Y');
            $this->insertar($total/2, date('Y-m-24'), "50% 1ra Quincena ($mesActual)");
            $this->insertar($total/2, date('Y-m-15', strtotime('first day of next month')), "50% Restante ($mesActual)");
            
            $this->db->commit();
            return ['status' => 'success', 'message' => 'Pagos generados correctamente para la quincena.'];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['status' => 'error', 'message' => 'Error en DB: ' . $e->getMessage()];
        }
    }

    private function insertar($monto, $fecha, $desc) {
        $stmt = $this->db->prepare("INSERT INTO pagos (fecha_pago, monto, estado, descripcion) VALUES (?, ?, 'pendiente', ?)");
        $stmt->bind_param("sds", $fecha, $monto, $desc);
        return $stmt->execute();
    }

    private function cambiarEstado(int $id, string $estado): array {
        $stmt = $this->db->prepare("UPDATE pagos SET estado = ? WHERE id_pago = ?");
        $stmt->bind_param("si", $estado, $id);
        return $stmt->execute() ? ['status' => 'success', 'message' => 'Estado actualizado'] : ['status' => 'error'];
    }

    private function eliminarPago(int $id): array {
        $stmt = $this->db->prepare("DELETE FROM pagos WHERE id_pago = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute() ? ['status' => 'success', 'message' => 'Registro eliminado'] : ['status' => 'error'];
    }

    public function listar() {
        return $this->db->query("SELECT * FROM pagos ORDER BY fecha_pago DESC");
    }
}

// 2. Procesamiento
$controller = new PaymentController($conn);
$apiResponse = $controller->handleRequest();

if ($apiResponse) {
    header('Content-Type: application/json');
    echo json_encode($apiResponse);
    exit;
}

$pagos = $controller->listar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterprise Payment Suite | Pucallpa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background: #0b0f1a; color: #e2e8f0; font-family: 'Inter', sans-serif; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); }
    </style>
</head>
<body class="p-4 md:p-10">

<div class="max-w-6xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-white">💰 Control de Pagos</h1>
            <p class="text-slate-400">Gestión de desembolsos y comisiones</p>
        </div>
        <div class="flex gap-3">
            <a href="index.php" class="glass px-6 py-2.5 rounded-xl flex items-center gap-2 hover:bg-slate-700 transition">
                <i class="ph ph-house"></i> Inicio
            </a>
            <button onclick="ejecutarAccion('generar')" class="bg-indigo-600 hover:bg-indigo-500 px-6 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-indigo-500/20 transition">
                <i class="ph ph-lightning"></i> Generar Pagos
            </button>
        </div>
    </div>

    <div class="glass rounded-3xl overflow-hidden border border-white/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-800/50 text-slate-400 text-[10px] uppercase tracking-[0.2em]">
                        <th class="p-6">ID</th>
                        <th class="p-6">Fecha y Concepto</th>
                        <th class="p-6">Monto Bruto</th>
                        <th class="p-6">Estado</th>
                        <th class="p-6">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-pagos">
                    <?php if ($pagos && $pagos->num_rows > 0): ?>
                        <?php while($f = $pagos->fetch_assoc()): ?>
                        <tr class="border-b border-slate-700/50 hover:bg-white/5 transition" id="row-<?php echo $f['id_pago']; ?>">
                            <td class="p-6 font-mono text-slate-500 text-sm">#<?php echo $f['id_pago']; ?></td>
                            <td class="p-6">
                                <span class="block font-bold text-white"><?php echo date('d M, Y', strtotime($f['fecha_pago'])); ?></span>
                                <span class="text-xs text-slate-500 italic"><?php echo htmlspecialchars($f['descripcion']); ?></span>
                            </td>
                            <td class="p-6 text-emerald-400 font-bold text-lg">
                                S/ <?php echo number_format((float)$f['monto'], 2); ?>
                            </td>
                            <td class="p-6">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black tracking-widest <?php echo $f['estado'] === 'pendiente' ? 'bg-amber-500/10 text-amber-500 border border-amber-500/20' : 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20'; ?>">
                                    <?php echo strtoupper($f['estado']); ?>
                                </span>
                            </td>
                            <td class="p-6">
                                <div class="flex gap-4">
                                    <?php if($f['estado'] === 'pendiente'): ?>
                                        <button onclick="actualizarEstado(<?php echo $f['id_pago']; ?>, 'pagado')" class="text-emerald-500 hover:scale-125 transition" title="Marcar pagado">
                                            <i class="ph-bold ph-check-circle text-2xl"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button onclick="confirmarEliminar(<?php echo $f['id_pago']; ?>)" class="text-rose-500 hover:scale-125 transition" title="Eliminar">
                                        <i class="ph-bold ph-trash text-2xl"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="p-10 text-center text-slate-500">No hay registros de pagos generados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
async function ejecutarAccion(action) {
    Swal.fire({
        title: 'Procesando...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    try {
        const response = await fetch(window.location.href, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action })
        });
        const result = await response.json();
        
        Swal.fire({
            icon: result.status,
            title: result.status === 'success' ? '¡Éxito!' : 'Atención',
            text: result.message,
            background: '#1e293b',
            color: '#fff',
            confirmButtonColor: '#6366f1'
        }).then(() => { if(result.status === 'success') location.reload(); });

    } catch (error) {
        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
    }
}

async function actualizarEstado(id, nuevo_estado) {
    const res = await fetch(window.location.href, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'actualizar_estado', id, nuevo_estado })
    });
    const data = await res.json();
    if(data.status === 'success') location.reload();
}

function confirmarEliminar(id) {
    Swal.fire({
        title: '¿Eliminar registro?',
        text: "Esta acción es irreversible.",
        icon: 'warning',
        showCancelButton: true,
        background: '#1e293b',
        color: '#fff',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ef4444'
    }).then(async (result) => {
        if (result.isConfirmed) {
            const res = await fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'eliminar', id })
            });
            const data = await res.json();
            if(data.status === 'success') {
                const row = document.getElementById(`row-${id}`);
                row.style.opacity = '0';
                setTimeout(() => row.remove(), 300);
            }
        }
    });
}
</script>
</body>
</html>