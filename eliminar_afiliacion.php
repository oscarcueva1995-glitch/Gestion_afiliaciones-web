<?php
include("conexion.php");

if(isset($_GET['id'])){
    $id = $_GET['id'];

    $sql = "DELETE FROM afiliaciones WHERE id_afiliacion = $id";

    if($conn->query($sql)){
        header("Location: listar.php");
    } else {
        echo "Error al eliminar";
    }
}
?>