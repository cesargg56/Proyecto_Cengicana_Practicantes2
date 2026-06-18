<?php
session_start();
require_once("../config/conexion.php");

$conn = Conexion::conectar();

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM modulos WHERE id=?");
$stmt->execute([$id]);
$m = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_POST) {
    $nombre = $_POST['nombre'];

    $stmt = $conn->prepare("UPDATE modulos SET nombre=? WHERE id=?");
    $stmt->execute([$nombre, $id]);

    header("Location: modulos.php");
    exit;
}
?>

<link rel="stylesheet" href="../assets/usuarios.css">

<a href="modulos.php" class="btn-volver">← Volver</a>

<form method="POST">
<h2>Editar Módulo</h2>

<input name="nombre" value="<?= $m['nombre'] ?>" required>

<button>Guardar</button>
</form>