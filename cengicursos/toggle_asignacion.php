<?php
require_once "revisar_permisos.php";
require_once "conexion.php";

cengi_require_admin('ver_cursos.php');

$db = conectar();
$id = (int) ($_GET['id'] ?? 0);
$cursoId = (int) ($_GET['curso_id'] ?? 0);
$estado = (int) ($_GET['estado'] ?? 0);
$nuevoEstado = $estado === 1 ? 0 : 1;

if ($id > 0) {
    $stmt = $db->prepare("
        UPDATE asignaciones
        SET
            estado_asignaciones = ?,
            actualizado = NOW()
        WHERE id = ?
    ");
    $stmt->execute([
        $nuevoEstado,
        $id,
    ]);
}

header("Location: ver_participante_curso.php?id=" . $cursoId);
exit;
?>
