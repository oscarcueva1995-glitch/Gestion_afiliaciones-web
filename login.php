<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login | Gestión Pro</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <style>
        /* Optimizamos el fondo para que no se vea lento en celulares */
        .bg-custom {
            background-image: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)), 
                              url('https://image2url.com/r2/default/images/1773729806523-a3b4ca2d-3c51-4e45-ba7c-4e28078c5025.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed; /* Evita saltos al hacer scroll */
        }
        
        /* Glassmorphism para que combine con tus otros paneles */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Eliminar el resaltado azul al tocar botones en Android */
        * { -webkit-tap-highlight-color: transparent; }
    </style>
</head>

<body class="bg-custom min-h-screen flex items-center justify-center p-4">

    <div class="glass-card w-full max-w-md p-8 rounded-2xl shadow-2xl transition-all">
        
        <div class="text-center mb-8">
            <div class="bg-sky-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg shadow-sky-500/30">
                <i class="ph ph-user text-white text-3xl"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Bienvenido</h2>
            <p class="text-slate-500 text-sm">Ingresa tus credenciales para continuar</p>
        </div>

        <form action="validar_login.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1 ml-1">Correo Electrónico</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <i class="ph ph-envelope"></i>
                    </span>
                    <input type="email" name="correo" placeholder="ejemplo@correo.com" 
                           class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition-all text-slate-700" 
                           required>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1 ml-1">Contraseña</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <i class="ph ph-lock"></i>
                    </span>
                    <input type="password" name="password" placeholder="••••••••" 
                           class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition-all text-slate-700" 
                           required>
                </div>
            </div>

            <button type="submit" 
                    class="w-full bg-sky-500 hover:bg-sky-600 active:scale-[0.98] text-white font-bold py-3.5 rounded-xl shadow-lg shadow-sky-500/30 transition-all flex items-center justify-center gap-2 mt-6">
                Ingresar al Sistema
                <i class="ph-bold ph-arrow-right"></i>
            </button>
        </form>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="mt-6 p-3 bg-red-50 border border-red-100 rounded-xl flex items-center gap-2 text-red-600 text-sm animate-pulse">
                <i class="ph-bold ph-warning-circle"></i>
                <?php 
                    echo htmlspecialchars($_SESSION['error']); 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <div class="mt-8 text-center">
            <p class="text-slate-400 text-xs italic">
                &copy; 2026 Inversiones Crissomar - Panel de Control
            </p>
        </div>
    </div>

</body>
</html>