<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start("ob_gzhandler");

session_start();
include("Conexion.php");

/**
 * --- 1. SECCIÓN DE ACCIONES (DB) ---
 */
// Capturamos el gestor actual para no perder el filtro al recargar
$g_url = isset($_GET['gestor']) ? $_GET['gestor'] : '';

if (isset($_GET['visitar'])) {
    $id = intval($_GET['visitar']);
    $conn->query("UPDATE renovaciones SET visitado = IF(visitado=1,0,1), trucho=0 WHERE id=$id");
    header("Location: renovaciones.php?gestor=$g_url");
    exit();
}

if (isset($_GET['trucho'])) {
    $id = intval($_GET['trucho']);
    $conn->query("UPDATE renovaciones SET trucho = IF(trucho=1,0,1), visitado=0 WHERE id=$id");
    header("Location: renovaciones.php?gestor=$g_url");
    exit();
}

if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM renovaciones WHERE id=$id");
    header("Location: renovaciones.php?gestor=$g_url");
    exit();
}

if (isset($_GET['borrar_todo'])) {
    $conn->query("DELETE FROM renovaciones");
    header("Location: renovaciones.php");
    exit();
}

/**
 * --- 2. LÓGICA DE FILTROS ---
 */
$filtro = "WHERE 1=1";

// Filtro por GESTOR
if ($g_url == 'A' || $g_url == 'B') {
    $filtro .= " AND gestor = '$g_url'";
}

$isCercania = (!empty($_GET['lat']) && !empty($_GET['lng']));
$radio = isset($_GET['radio']) ? floatval($_GET['radio']) : 5; // km por defecto

if ($isCercania) {
    $lat_temp = floatval($_GET['lat']);
    $lng_temp = floatval($_GET['lng']);
    // Mostrar solo no visitados/no truchos y dentro del radio seleccionado
    $filtro .= " AND visitado = 0 AND trucho = 0";
    $filtro .= " AND (6371 * ACOS(COS(RADIANS($lat_temp)) * COS(RADIANS(latitud)) * COS(RADIANS(longitud) - RADIANS($lng_temp)) + SIN(RADIANS($lat_temp)) * SIN(RADIANS(latitud)))) <= $radio";
} elseif (isset($_GET['ver_estado'])) {
    $est = $_GET['ver_estado'];
    if ($est == 'pendientes') $filtro .= " AND visitado = 0 AND trucho = 0";
    if ($est == 'visitados')  $filtro .= " AND visitado = 1";
    if ($est == 'truchos')    $filtro .= " AND trucho = 1";
}

if (!empty($_GET['desde']) && !empty($_GET['hasta'])) {
    $desde = $conn->real_escape_string($_GET['desde']);
    $hasta = $conn->real_escape_string($_GET['hasta']);
    $filtro .= " AND fecha BETWEEN '$desde' AND '$hasta'";
}

/**
 * --- 3. CONTADORES Y ORDENAMIENTO ---
 */
$resumen = $conn->query("SELECT 
    SUM(CASE WHEN visitado = 0 AND trucho = 0 THEN 1 ELSE 0 END) as pendientes,
    SUM(CASE WHEN visitado = 1 THEN 1 ELSE 0 END) as visitados,
    SUM(CASE WHEN trucho = 1 THEN 1 ELSE 0 END) as truchos
    FROM renovaciones $filtro");
$counts = $resumen->fetch_assoc();

if ($isCercania) {
    $lat = floatval($_GET['lat']); 
    $lng = floatval($_GET['lng']);
    $orden = "ORDER BY (6371 * ACOS(COS(RADIANS($lat)) * COS(RADIANS(latitud)) * COS(RADIANS(longitud) - RADIANS($lng)) + SIN(RADIANS($lat)) * SIN(RADIANS(latitud)))) ASC";
} else {
    $orden = "ORDER BY visitado ASC, trucho ASC, id DESC";
}

$datos = $conn->query("SELECT * FROM renovaciones $filtro $orden");
$mapa = $conn->query("SELECT * FROM renovaciones $filtro");

function cortar($t, $l = 30) {
    return strlen($t) > $l ? substr($t, 0, $l) . '...' : $t;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Rutas - Pucallpa</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <style>
        body { background:#0f172a; color:white; font-family:'Segoe UI', sans-serif; padding:8px; margin:0; }
        .resaltar { animation: brillo 1s infinite alternate; box-shadow: 0 0 25px 10px #22c55e; border: 2px solid #22c55e; z-index: 10; }
        @keyframes brillo { from { transform: scale(1); } to { transform: scale(1.05); } }

        /* Selector de Gestor */
        .gestor-selector { display:flex; gap:5px; margin-bottom:10px; }
        .btn-g { flex:1; padding:12px; border-radius:8px; border:none; color:white; font-weight:bold; text-decoration:none; text-align:center; font-size:12px; }
        .g-a { background:#8b5cf6; } .g-b { background:#ec4899; } .g-all { background:#64748b; }
        .active-g { border: 2px solid white; box-shadow: 0 0 10px rgba(255,255,255,0.3); }

        .stats-bar { display:flex; justify-content:space-around; background:#1e293b; padding:10px; border-radius:10px; margin-bottom:10px; font-size:11px; border:1px solid #334155; }
        .stats-bar a { text-decoration:none; color:inherit; text-align:center; flex:1; }
        .stats-bar b { font-size:14px; display:block; }
        .active-filter { border-bottom:3px solid #3b82f6; }

        .main-nav { text-align:center; margin-bottom:10px; display:flex; gap:5px; justify-content:center; flex-wrap: wrap; }
        .btn-nav { padding:8px 15px; border-radius:8px; color:white; text-decoration:none; font-size:13px; font-weight:bold; }
        /* Buscador compacto */
        .search-compact { display:flex; gap:6px; align-items:center; justify-content:center; margin:6px 0; }
        .search-compact input { width:200px; max-width:40vw; padding:6px 8px; border-radius:8px; border:none; background:#0f172a; color:white; font-size:13px; }
        .search-compact .btn { padding:6px 8px; font-size:13px; width:auto; }
        
        #map { height:250px; border-radius:10px; margin-bottom:10px; filter: invert(100%) hue-rotate(180deg) brightness(95%); }
        .cards { display:grid; grid-template-columns:repeat(2,1fr); gap:8px; }
        
        .item { background:#1e293b; border-radius:10px; overflow:hidden; border:1px solid #334155; position:relative; }
        .item img { width:100%; height:100px; object-fit:cover; }
        .item.visitado { border:2px solid #22c55e; }
        .item.trucho { border:2px solid #facc15; }
        
        .info { padding:6px; font-size:11px; }
        .btn { padding:5px; border-radius:5px; color:white; text-decoration:none; display:inline-block; margin-top:4px; font-size:11px; text-align:center; border:none; cursor:pointer; width:48%; }
        .full { width:98% !important; }
        .azul { background:#3b82f6; } .verde { background:#22c55e; } .amarillo { background:#facc15; color:black; } .morado { background:#6366f1; } .rojo { background:#ef4444; }

        .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); justify-content:center; align-items:center; z-index:9999; }
        .modal img { max-width:95%; max-height:95%; }
        .toast { position:fixed; left:50%; transform:translateX(-50%); bottom:20px; background:rgba(0,0,0,0.8); color:white; padding:8px 12px; border-radius:8px; display:none; z-index:10000; }
    </style>
</head>
<body>

<h3 style="text-align:center; margin:10px 0; font-size:16px;">📍 CONTROL - <?= $g_url ? "GESTOR $g_url" : "TODOS" ?></h3>

<div class="gestor-selector">
    <a href="?gestor=A" class="btn-g g-a <?= $g_url=='A'?'active-g':'' ?>">👤 GESTOR A</a>
    <a href="?gestor=B" class="btn-g g-b <?= $g_url=='B'?'active-g':'' ?>">👤 GESTOR B</a>
    <a href="renovaciones.php" class="btn-g g-all <?= $g_url==''?'active-g':'' ?>">👥 TODOS</a>
</div>

<div class="stats-bar">
    <a href="?gestor=<?= $g_url ?>&ver_estado=pendientes" class="<?= ($_GET['ver_estado']??'')=='pendientes'?'active-filter':'' ?>">⏳ Pendientes<b><?= (int)$counts['pendientes'] ?></b></a>
    <a href="?gestor=<?= $g_url ?>&ver_estado=visitados" class="<?= ($_GET['ver_estado']??'')=='visitados'?'active-filter':'' ?>">✅ Visitados<b><?= (int)$counts['visitados'] ?></b></a>
    <a href="?gestor=<?= $g_url ?>&ver_estado=truchos" class="<?= ($_GET['ver_estado']??'')=='truchos'?'active-filter':'' ?>">⚠️ Truchos<b><?= (int)$counts['truchos'] ?></b></a>
</div>

<div class="main-nav">
    <a href="importar_renovaciones.php" class="btn-nav" style="background:#f59e0b;">📥 Importar</a>
    <a href="?borrar_todo=1" class="btn-nav" style="background:#ef4444;" onclick="return confirm('¿Eliminar TODO?')">🗑️ Limpiar</a>
    <!-- 'Ver Todo' removed to avoid duplication with 'TODOS' selector -->
</div>

<form method="GET" class="main-nav">
    <input type="hidden" name="gestor" value="<?= $g_url ?>">
    <input type="date" name="desde" value="<?= $_GET['desde'] ?? '' ?>" style="padding:5px; border-radius:5px; border:none;">
    <input type="date" name="hasta" value="<?= $_GET['hasta'] ?? '' ?>" style="padding:5px; border-radius:5px; border:none;">
    <input type="hidden" name="lat" id="lat" value="<?= htmlspecialchars($_GET['lat'] ?? '') ?>">
    <input type="hidden" name="lng" id="lng" value="<?= htmlspecialchars($_GET['lng'] ?? '') ?>">
    <select name="radio" id="radio" style="padding:6px; border-radius:6px; border:none; background:#0f172a; color:white;">
        <?php $rads = [1,3,5,10]; foreach($rads as $rv): ?>
            <option value="<?= $rv ?>" <?= (isset($_GET['radio']) && $_GET['radio']==$rv) || (!isset($_GET['radio']) && $rv==5) ? 'selected' : '' ?>><?= $rv ?> km</option>
        <?php endforeach; ?>
    </select>
    <button type="button" id="btnRuta" onclick="usarGPS(this)" style="background:#3b82f6; color:white; border:none; padding:8px; border-radius:5px; font-weight:bold;"><?= $isCercania ? "📍 MODO RUTA ({$radio} km)" : "📍 MODO RUTA" ?></button>
    <button type="submit" style="background:#22c55e; color:white; border:none; padding:8px; border-radius:5px; font-weight:bold;">🔍 Filtrar</button>
</form>

<!-- Buscador compacto -->
<div class="search-compact">
    <input id="searchInput" type="text" placeholder="Buscar nombre o teléfono" aria-label="Buscar">
    <button class="btn azul" onclick="buscar()">🔎</button>
    <button class="btn rojo" onclick="limpiarBusqueda()">✖</button>
</div>

<div id="map"></div>

<div class="cards">
<?php while($r = $datos->fetch_assoc()): 
    $estadoClase = $r['trucho'] ? 'trucho' : ($r['visitado'] ? 'visitado' : 'pendiente');?>
    <div id="item<?= $r['id'] ?>" class="item <?= $estadoClase ?>">
        <?php if($r['imagen']): ?>
            <img src="<?= $r['imagen'] ?>" loading="lazy" onclick="verImagen(this.src)">
        <?php endif; ?>

        <div class="info">
            <b style="display:inline-block;" title="<?= htmlspecialchars($r['cliente']) ?>"><?= htmlspecialchars(cortar($r['cliente'], 18)) ?></b>
            <button onclick="copiarTexto('<?= addslashes($r['cliente']) ?>')" title="Copiar nombre" style="margin-left:6px; padding:4px 6px; font-size:11px; border-radius:6px; background:#64748b; border:none; color:white; cursor:pointer;">📋</button>
            <br>
            📍 <?= htmlspecialchars(cortar($r['direccion'], 25)) ?><br>
            📞 <?= htmlspecialchars($r['telefono'] ?? 'S/N') ?>
            
            <div style="display:flex; flex-wrap:wrap; gap:2px; margin-top:5px;">
                <?php if($r['latitud']): ?>
                    <a href="https://www.google.com/maps?q=<?= $r['latitud'] ?>,<?= $r['longitud'] ?>" target="_blank" class="btn verde">🚗 Ir</a>
                <?php endif; ?>
                
                <button onclick="validarNumero('<?= $r['telefono'] ?>')" class="btn azul">🔎 Val.</button>

                <?php if(!empty($r['qr'])): ?>
                    <button onclick='copiarQR(<?= json_encode($r['qr']) ?>)' class="btn morado">📋 Copiar QR</button>
                <?php endif; ?>

                <?php if($r['trucho']): ?>
                    <a href="?trucho=<?= $r['id'] ?>&gestor=<?= $g_url ?>" class="btn amarillo full">⚠️ QUITAR TRUCHO</a>
                <?php elseif($r['visitado']): ?>
                    <a href="?visitar=<?= $r['id'] ?>&gestor=<?= $g_url ?>" class="btn verde full">✅ DESMARCAR</a>
                <?php else: ?>
                    <a href="?visitar=<?= $r['id'] ?>&gestor=<?= $g_url ?>" class="btn azul" style="flex:1;">✔ Visitar</a>
                    <a href="?trucho=<?= $r['id'] ?>&gestor=<?= $g_url ?>" class="btn amarillo" style="flex:1;">⚠️ Trucho</a>
                <?php endif; ?>
                
                <a href="?eliminar=<?= $r['id'] ?>&gestor=<?= $g_url ?>" class="btn rojo full" onclick="return confirm('¿Eliminar?')">❌ Eliminar</a>
            </div>
        </div>
    </div>
<?php endwhile; ?>
</div>

<!-- Modal para ver imagen grande -->
<div class="modal" id="modal" onclick="if(event.target.id==='modal') this.style.display='none'">
    <img id="imgGrande">
</div>

<!-- Street View modal removed -->

<div class="modal" id="modalConsulta" onclick="if(event.target.id==='modalConsulta') this.style.display='none'">
    <div style="background:white; padding:10px; border-radius:10px; width:95%; height:90%;"><iframe id="frameConsulta" style="width:100%; height:100%; border:none;"></iframe></div>
</div>
<div id="toast" class="toast"></div>

<script>
    var map = L.map('map', {zoomControl:false}).setView([-8.3791,-74.5539],13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    var bounds = L.latLngBounds();

    function getIcon(color) {
        return new L.Icon({
            iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-${color}.png`,
            iconSize: [18, 30], iconAnchor: [9, 30]
        });
    }

    <?php
    $mapa->data_seek(0);
    echo "var puntos = [];\n";
    while($row=$mapa->fetch_assoc()){
        if($row['latitud']){
            $color = $row['trucho'] ? "gold" : ($row['visitado'] ? "green" : "red");
            // Construir contenido del popup (imagen + nombre + acciones)
            $imgHtml = '';
            if(!empty($row['imagen'])){
                $imgEsc = addslashes($row['imagen']);
                $imgHtml = "<img src='".$imgEsc."' style='max-width:140px; display:block; margin:6px auto; cursor:pointer;' onclick=\"verImagen('".$imgEsc."')\">";
            }
            $clienteEsc = addslashes($row['cliente']);
            $clienteJsEsc = str_replace("'","\\'", $row['cliente']);
            // Crear una variable de marcador única y guardarla en el array puntos
            $markerVar = 'marker'.intval($row['id']);
            $popup = "<div style='text-align:center;'>".$imgHtml."<b>".$clienteEsc."</b><br><a href=\"javascript:void(0)\" onclick=\"resaltarCard(".$row['id'].")\">Ver Info</a> | <a href=\"javascript:void(0)\" onclick=\"copiarTexto('".$clienteJsEsc."')\">📋 Copiar</a></div>";
            echo "var {$markerVar} = L.marker([{$row['latitud']},{$row['longitud']}], {icon: getIcon('".$color."')}).addTo(map).bindPopup(".json_encode($popup).");\n";
            echo "puntos.push({id: ".json_encode($row['id']).", lat: ".json_encode($row['latitud']).", lng: ".json_encode($row['longitud']).", cliente: ".json_encode($row['cliente']).", marker: {$markerVar}});\n";
            echo "bounds.extend([{$row['latitud']},{$row['longitud']}]);\n";
        }
    }

    // Si se proporcionó la ubicación del usuario, añadir marcador y círculo
    if($isCercania){
        echo "var usuarioLat = ".json_encode($lat_temp)."; var usuarioLng = ".json_encode($lng_temp).";\n";
        echo "var usuarioMarker = L.marker([usuarioLat, usuarioLng], {icon: getIcon('blue')}).addTo(map).bindPopup('Tu ubicaci\u00f3n').openPopup();\n";
        echo "bounds.extend([usuarioLat, usuarioLng]);\n";
        echo "L.circle([{$lat_temp},{$lng_temp}], {radius: " . ($radio*1000) . ", color:'#3b82f6', fill:false}).addTo(map);\n";
    }

    echo "if(bounds.isValid()){ map.fitBounds(bounds.pad(0.15)); }\n";
    ?>

    function resaltarCard(id){
        let el = document.getElementById("item"+id);
        if(!el) return;
        document.querySelectorAll('.item').forEach(e => e.classList.remove('resaltar'));
        el.classList.add("resaltar");
        el.scrollIntoView({behavior: "smooth", block: "center"});
    }

    // Buscador: busca por nombre o teléfono y resalta la primera coincidencia
    function buscar(){
        const q = document.getElementById('searchInput').value.trim().toLowerCase();
        if(!q) return showToast('Ingresa nombre o teléfono');
        // buscar entre elementos .item
        const items = Array.from(document.querySelectorAll('.item'));
        let found = null;
        for(const it of items){
            const text = it.innerText.toLowerCase();
            if(text.indexOf(q) !== -1){ found = it; break; }
        }
        if(found){
            document.querySelectorAll('.item').forEach(e => e.classList.remove('resaltar'));
            found.classList.add('resaltar');
            found.scrollIntoView({behavior:'smooth', block:'center'});
            showToast('Encontrado');
            // abrir popup en mapa si marcador existe en puntos
            const id = found.id.replace('item','');
            try{ var match = puntos.find(p=>p.id==id); if(match){ match.marker.openPopup(); map.setView([match.lat, match.lng], 15); } }catch(e){}
        } else {
            showToast('No encontrado');
        }
    }

    function limpiarBusqueda(){ document.getElementById('searchInput').value=''; document.querySelectorAll('.item').forEach(e => e.classList.remove('resaltar')); }

    function usarGPS(btn){
        btn.innerHTML = "📡 Buscando...";
        navigator.geolocation.getCurrentPosition(function(pos){
            document.getElementById('lat').value = pos.coords.latitude;
            document.getElementById('lng').value = pos.coords.longitude;
            btn.closest('form').submit();
        }, function(){ showToast("Activa el GPS"); btn.innerHTML="📍 MODO RUTA"; });
    }

    function verImagen(src){ document.getElementById("modal").style.display="flex"; document.getElementById("imgGrande").src=src; }
    function copiarQR(t){ navigator.clipboard.writeText(t).then(()=> showToast("QR copiado")); }

    var currentQR = '';
    function mostrarQR(t){
        currentQR = t;
        // Si el valor es una imagen URL (empieza por http) mostrar imagen; si es texto generar QR via API simple (chart.googleapis)
        var img = document.getElementById('qrImg');
        if(/^https?:\/\//i.test(t)){
            img.src = t;
        } else {
            // Generar QR usando Google Chart API (texto en URL)
            var url = 'https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=' + encodeURIComponent(t);
            img.src = url;
        }
        document.getElementById('qrModal').style.display = 'flex';
    }

    // Street view removed — functions cleaned up

    function copiarTexto(t){
        if(!t) return;
        if(navigator.clipboard && navigator.clipboard.writeText){
            navigator.clipboard.writeText(t).then(()=> showToast("Texto copiado"), ()=> fallbackCopy(t));
        } else {
            fallbackCopy(t);
        }
    }

    function fallbackCopy(text){
        var ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        try{ document.execCommand('copy'); showToast('Texto copiado'); }catch(e){ showToast('No se pudo copiar'); }
        ta.remove();
    }

    function showToast(msg, timeout=1800){
        var t = document.getElementById('toast');
        t.innerText = msg;
        t.style.display = 'block';
        clearTimeout(t._to);
        t._to = setTimeout(()=> t.style.display='none', timeout);
    }

    function validarNumero(n){ 
        navigator.clipboard.writeText(n);
        document.getElementById("modalConsulta").style.display="flex"; 
        document.getElementById("frameConsulta").src="https://numeros-elpezgordo.d-vlant.com/"; 
    }

    // Cerrar modales con ESC
    document.addEventListener('keydown', function(e){ if(e.key==='Escape'){ document.getElementById('modal').style.display='none'; document.getElementById('modalConsulta').style.display='none'; } });

    // Calcular punto más cercano si hay puntos y ubicación del usuario
    function haversine(lat1, lon1, lat2, lon2){
        var R = 6371000; // metros
        var toRad = Math.PI/180;
        var dLat = (lat2-lat1)*toRad;
        var dLon = (lon2-lon1)*toRad;
        var a = Math.sin(dLat/2)*Math.sin(dLat/2) + Math.cos(lat1*toRad)*Math.cos(lat2*toRad)*Math.sin(dLon/2)*Math.sin(dLon/2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    if(typeof puntos !== 'undefined' && puntos.length && typeof usuarioLat !== 'undefined'){
        var minDist = null; var nearest = null;
        puntos.forEach(function(p){
            var d = haversine(usuarioLat, usuarioLng, parseFloat(p.lat), parseFloat(p.lng));
            if(minDist === null || d < minDist){ minDist = d; nearest = p; }
        });
        if(nearest){
            try{ nearest.marker.setIcon(getIcon('orange')); nearest.marker.openPopup(); }catch(e){}
            resaltarCard(nearest.id);
            showToast('Más cercano: '+nearest.cliente+' ('+Math.round(minDist)+' m)');
        }
    }
</script>
</body>
</html>