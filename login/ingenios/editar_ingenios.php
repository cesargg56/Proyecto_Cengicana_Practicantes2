<?php
session_start();
require_once("../config/conexion.php");

$conn = Conexion::conectar();

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM ingenios WHERE id=?");
$stmt->execute([$id]);
$m = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_POST) {
    $nombre = $_POST['nombre_ingenio'];

    $stmt = $conn->prepare("UPDATE ingenios SET nombre_ingenio=? WHERE id=?");
    $stmt->execute([$nombre, $id]);

    header("Location: ingenios.php");
    exit;
}
?>

<link rel="stylesheet" href="../assets/usuarios.css">

<a href="ingenios.php" class="btn-volver">← Volver</a>
<form method="POST">
<h2>Editar Ingenio</h2>

<input name="nombre_ingenio" value="<?= $m['nombre_ingenio'] ?>" required>
<button>Guardar</button>
</form>