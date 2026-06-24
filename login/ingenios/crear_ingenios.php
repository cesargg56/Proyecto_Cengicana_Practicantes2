<?php
session_start();
require_once("../config/conexion.php");

$conn = Conexion::conectar();

if ($_POST) {
    $nombre = $_POST['nombre'];

    $stmt = $conn->prepare("INSERT INTO ingenios (nombre_ingenio) VALUES (?)");
    $stmt->execute([$nombre]);

    header("Location: ingenios.php");
    exit;
}
?>

<link rel="stylesheet" href="../assets/usuarios.css">

<a href="ingenios.php" class="btn-volver">← Volver</a>

<form method="POST">
<h2>Crear Igenio</h2>

<input name="nombre" placeholder="Nombre del Ingenio" required>

<button>Crear</button>
</form>