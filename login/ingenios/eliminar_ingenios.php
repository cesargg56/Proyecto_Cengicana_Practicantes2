<?php
session_start();
require_once("../config/conexion.php");

// 🔒 SOLO SUPERADMIN
if (!isset($_SESSION['es_superadmin']) || !$_SESSION['es_superadmin']) {
    die("Acceso restringido");
}

$conn = Conexion::conectar();

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID inválido");
}

// 🔍 OBTENER ROL
$stmt = $conn->prepare("SELECT * FROM ingenios WHERE id = ?");
$stmt->execute([$id]);
$rol = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rol) {
    die("ingenio no encontrado");
}

// 🔒 PROTEGER SUPERADMIN
if ($rol['nombre_ingenio'] == 'Superadmin') {
    die("No se puede eliminar el ingenio Superadmin");
}

// 🔒 VALIDAR SI TIENE USUARIOS
$stmtUsers = $conn->prepare("SELECT id FROM usuarios WHERE ingenio_id = ?");
$stmtUsers->execute([$id]);

if ($stmtUsers->rowCount() > 0) {
    die("No se puede eliminar este ingenio porque tiene usuarios asignados");
}


// 🔥 ELIMINAR
$stmt = $conn->prepare("DELETE FROM Ingenios WHERE id = ?");
$stmt->execute([$id]);

header("Location: ingenios.php");
exit;