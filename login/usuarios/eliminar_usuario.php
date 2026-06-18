<?php
session_start();
require_once("../config/conexion.php");

$conn = Conexion::conectar();

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID inválido");
}

// 🔒 VERIFICAR SI ES SUPERADMIN
$stmt = $conn->prepare("SELECT es_superadmin FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Usuario no encontrado");
}

// 🚫 BLOQUEAR ELIMINACIÓN
if ($user['es_superadmin'] == 1) {
    die("No se puede eliminar un Superadmin");
}

// 🔥 ELIMINAR RELACIÓN DE MÓDULOS (para evitar errores de FK)
$stmtDelMod = $conn->prepare("DELETE FROM usuario_modulo WHERE usuario_id = ?");
$stmtDelMod->execute([$id]);

// 🔥 ELIMINAR USUARIO
$stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->execute([$id]);

header("Location: usuarios.php");
exit;