<?php
session_start();
require_once("../config/conexion.php");

$conn = Conexion::conectar();

if ($_POST) {
    $nombre = $_POST['nombre'];

    $stmt = $conn->prepare("INSERT INTO modulos (nombre) VALUES (?)");
    $stmt->execute([$nombre]);

    header("Location: modulos.php");
    exit;
}
?>

<link rel="stylesheet" href="../assets/usuarios.css">

<a href="modulos.php" class="btn-volver">← Volver</a>

<form method="POST">
<h2>Crear Módulo</h2>

<input name="nombre" placeholder="Nombre del módulo" required>

<button>Crear</button>
</form>