<?php

require_once __DIR__ . '/includes/user_module_helper.php';

lab_require_permission('laboratorio.usuarios.gestionar');

$conn = lab_users_connection();
$module = lab_laboratory_module($conn);
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    lab_forbidden('El usuario solicitado no es válido.');
}

$usuario = lab_fetch_laboratory_user($conn, (int) $module['id'], $id);
if ($usuario === null) {
    lab_forbidden('El usuario solicitado no pertenece al módulo Laboratorio.');
}

$currentUser = lab_current_user();
if ((int) ($currentUser['id'] ?? 0) === $id) {
    lab_forbidden('No puede eliminar su propio usuario desde este módulo.');
}

if ((int) ($usuario['es_superadmin'] ?? 0) === 1) {
    lab_forbidden('No se puede eliminar un usuario superadmin desde este módulo.');
}

$stmt = $conn->prepare('DELETE FROM ' . lab_users_table('usuarios') . ' WHERE id = ?');
$stmt->execute([$id]);

lab_user_module_redirect_to_list();
