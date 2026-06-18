<?php

require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__, 2) . '../../login/config/conexion.php';

const LAB_USERS_SCHEMA = 'usuarios_menu';

function lab_users_connection(): PDO
{
    return Conexion::conectar();
}

function lab_users_e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function lab_users_table(string $table): string
{
    static $allowed = [
        'modulos' => true,
        'usuario_modulo' => true,
        'usuarios' => true,
        'roles' => true,
        'ingenios' => true,
    ];

    if (!isset($allowed[$table])) {
        throw new InvalidArgumentException('Tabla no permitida: ' . $table);
    }

    return sprintf('`%s`.`%s`', LAB_USERS_SCHEMA, $table);
}

function lab_users_has_column(PDO $conn, string $table, string $column): bool
{
    static $cache = [];

    $cacheKey = $table . '.' . $column;
    if (array_key_exists($cacheKey, $cache)) {
        return $cache[$cacheKey];
    }

    $stmt = $conn->prepare(
        'SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = ?
          AND TABLE_NAME = ?
          AND COLUMN_NAME = ?
        LIMIT 1'
    );
    $stmt->execute([LAB_USERS_SCHEMA, $table, $column]);

    $cache[$cacheKey] = (bool) $stmt->fetchColumn();

    return $cache[$cacheKey];
}

function lab_laboratory_module(PDO $conn): array
{
    static $module = null;

    if ($module !== null) {
        return $module;
    }

    $stmt = $conn->prepare(
        'SELECT id, nombre
        FROM ' . lab_users_table('modulos') . '
        WHERE LOWER(nombre) = LOWER(?)
        LIMIT 1'
    );
    $stmt->execute(['Laboratorio']);

    $module = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$module) {
        throw new RuntimeException('No se encontro el modulo Laboratorio en la tabla modulos.');
    }

    return $module;
}

function lab_laboratory_module_id(PDO $conn): int
{
    $module = lab_laboratory_module($conn);

    return (int) $module['id'];
}

function lab_fetch_laboratory_users(PDO $conn, int $moduleId): array
{
    $statusSelect = lab_users_has_column($conn, 'usuarios', 'estado')
        ? 'COALESCE(u.estado, 1)'
        : '1';

    $stmt = $conn->prepare(
        'SELECT DISTINCT
            u.id,
            u.nombre,
            u.correo,
            u.rol_id,
            u.ingenio_id,
            ' . $statusSelect . ' AS estado,
            u.es_superadmin,
            COALESCE(r.nombre_rol, \'Sin rol\') AS nombre_rol,
            COALESCE(i.nombre_ingenio, \'Sin ingenio\') AS ingenio
        FROM ' . lab_users_table('usuarios') . ' u
        INNER JOIN ' . lab_users_table('usuario_modulo') . ' um ON um.usuario_id = u.id
        LEFT JOIN ' . lab_users_table('roles') . ' r ON r.id = u.rol_id
        LEFT JOIN ' . lab_users_table('ingenios') . ' i ON i.id = u.ingenio_id
        WHERE um.modulo_id = ?
        ORDER BY u.nombre ASC, u.id ASC'
    );
    $stmt->execute([$moduleId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function lab_fetch_laboratory_user(PDO $conn, int $moduleId, int $userId): ?array
{
    $stmt = $conn->prepare(
        'SELECT DISTINCT
            u.id,
            u.nombre,
            u.correo,
            u.rol_id,
            u.ingenio_id,
            u.es_superadmin,
            COALESCE(r.nombre_rol, \'Sin rol\') AS nombre_rol
        FROM ' . lab_users_table('usuarios') . ' u
        INNER JOIN ' . lab_users_table('usuario_modulo') . ' um ON um.usuario_id = u.id
        LEFT JOIN ' . lab_users_table('roles') . ' r ON r.id = u.rol_id
        WHERE um.modulo_id = ? AND u.id = ?
        LIMIT 1'
    );
    $stmt->execute([$moduleId, $userId]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user ?: null;
}

function lab_fetch_roles_for_user_module(PDO $conn): array
{
    $stmt = $conn->query(
        'SELECT id, nombre_rol
        FROM ' . lab_users_table('roles') . '
        WHERE id != 1
        ORDER BY nombre_rol ASC'
    );

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function lab_fetch_ingenios_for_user_module(PDO $conn): array
{
    $stmt = $conn->query(
        'SELECT id, nombre_ingenio
        FROM ' . lab_users_table('ingenios') . '
        ORDER BY nombre_ingenio ASC'
    );

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function lab_user_module_back_url(): string
{
    return 'index.php';
}

function lab_user_module_list_url(): string
{
    return 'usuarios.php';
}

function lab_user_module_redirect_to_list(): void
{
    header('Location: ' . lab_user_module_list_url());
    exit;
}
