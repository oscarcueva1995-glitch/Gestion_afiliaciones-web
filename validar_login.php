<?php
// 1. Iniciar sesión y limpiar errores previos
session_start();
ob_start(); 

// 2. Incluir conexión (Asegúrate que el archivo se llame Conexion.php con C mayúscula)
include("Conexion.php"); 

// 3. Recibir datos del formulario
$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (empty($correo) || empty($password)) {
    $_SESSION['error'] = "Por favor, completa todos los campos.";
    header("Location: login.php");
    exit();
}

try {
    // 4. Consulta preparada para evitar inyección SQL
    $stmt = $conn->prepare("SELECT nombre, password FROM usuarios WHERE correo = ? LIMIT 1");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        
        // 5. Verificar contraseña (acepta texto plano o hash)
        $valido = false;
        if (strpos($usuario['password'], '$2y$') === 0) {
            $valido = password_verify($password, $usuario['password']);
        } else {
            $valido = ($password === $usuario['password']);
        }

        if ($valido) {
            $_SESSION['usuario'] = $usuario['nombre'];
            header("Location: index.php");
            exit();
        }
    }

    // 6. Si falla, regresar al login con mensaje
    $_SESSION['error'] = "Correo o contraseña incorrectos.";
    header("Location: login.php");
    exit();

} catch (Exception $e) {
    // Evita mostrar el error real al usuario para que no de Error 500
    $_SESSION['error'] = "Error de conexión al servidor.";
    header("Location: login.php");
    exit();
}
?>