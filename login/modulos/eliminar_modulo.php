<?php
session_start();
require_once("../config/conexion.php");

$conn = Conexion::conectar();

$id = $_GET['id'];

// 🔒 VALIDAR SI TIENE USUARIOS
$stmtCheck = $conn->prepare("SELECT * FROM usuario_modulo WHERE modulo_id=?");
$stmtCheck->execute([$id]);

if ($stmtCheck->rowCount() > 0) {
    die("No se puede eliminar este módulo porque tiene usuarios asignados");
}

// 🔥 ELIMINAR
$stmt = $conn->prepare("DELETE FROM modulos WHERE id=?");
$stmt->execute([$id]);

header("Location: modulos.php");
exit;