<?php
session_start();
require_once("../config/conexion.php");
require_once("../config/permisos_roles.php");

if (empty($_SESSION['es_superadmin']) || (int) $_SESSION['es_superadmin'] !== 1) {
    die("Acceso restringido");
}

$conn = Conexion::conectar();
asegurar_tablas_permisos($conn);

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID invalido");
}

$stmt = $conn->prepare("SELECT * FROM roles WHERE id = ?");
$stmt->execute([$id]);
$rol = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rol) {
    die("Rol no encontrado");
}

if (strtolower($rol['nombre_rol']) === 'superadmin') {
    die("No se puede eliminar el rol Superadmin");
}

$stmtUsers = $conn->prepare("SELECT id FROM usuarios WHERE rol_id = ?");
$stmtUsers->execute([$id]);

if ($stmtUsers->rowCount() > 0) {
    die("No se puede eliminar este rol porque tiene usuarios asignados");
}

try {
    $stmtRM = $conn->prepare("SELECT * FROM rol_modulo WHERE rol_id = ?");
    $stmtRM->execute([$id]);

    if ($stmtRM->rowCount() > 0) {
        die("No se puede eliminar este rol porque tiene modulos asignados");
    }
} catch (Exception $e) {
}

$stmtPermisos = $conn->prepare("DELETE FROM rol_permiso WHERE rol_id = ?");
$stmtPermisos->execute([$id]);

$stmt = $conn->prepare("DELETE FROM roles WHERE id = ?");
$stmt->execute([$id]);

header("Location: roles.php");
exit;
