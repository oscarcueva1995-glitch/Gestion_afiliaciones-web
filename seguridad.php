<?php
session_start();
// Si no existe la sesión de usuario, lo manda al login inmediatamente
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
?>