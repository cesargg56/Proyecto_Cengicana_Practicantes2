<?php
error_reporting(E_ALL);
require_once __DIR__ . '/../conexion.php';

if (0 > version_compare(PHP_VERSION, '5')) {
    die('Este archivo fue generado para PHP 5 o superior');
}

class users
{
    public function __construct()
    {
    }

    public function consultar_visibles()
    {
        $mysqli = conectar_usuarios_menu();
        $sql = "
            SELECT
                DISTINCT
                u.id,
                u.nombre,
                u.correo,
                r.nombre_rol,
                COALESCE(i.nombre_ingenio, 'Sin ingenio') AS nombre_ingenio
            FROM usuarios u
            INNER JOIN roles r ON r.id = u.rol_id
            LEFT JOIN ingenios i ON i.id = u.ingenio_id
            LEFT JOIN usuario_modulo um ON um.usuario_id = u.id
            LEFT JOIN modulos m ON m.id = um.modulo_id
            WHERE LOWER(COALESCE(m.nombre, '')) IN ('cursos', 'cengicursos')
        ";

        if (function_exists('cengi_ve_todo_por_rol_o_ingenio') && !cengi_ve_todo_por_rol_o_ingenio()) {
            $sql .= "
              AND u.ingenio_id = " . (int) cengi_ingenio_id_actual();
        }

        $sql .= "
            ORDER BY u.nombre
        ";

        return $mysqli->query($sql);
    }

    public function getCursoByNombre($nombre)
    {
        $mysqli = conectar_usuarios_menu();
        $nombre = '%' . $mysqli->real_escape_string($nombre) . '%';

        $stmt = $mysqli->prepare("
            SELECT
                DISTINCT
                u.id,
                u.nombre,
                u.correo,
                r.nombre_rol,
                COALESCE(i.nombre_ingenio, 'Sin ingenio') AS nombre_ingenio
            FROM usuarios u
            INNER JOIN roles r ON r.id = u.rol_id
            LEFT JOIN ingenios i ON i.id = u.ingenio_id
            LEFT JOIN usuario_modulo um ON um.usuario_id = u.id
            LEFT JOIN modulos m ON m.id = um.modulo_id
            WHERE u.nombre LIKE ?
              AND LOWER(COALESCE(m.nombre, '')) IN ('cursos', 'cengicursos')
        ");

        if (function_exists('cengi_ve_todo_por_rol_o_ingenio') && !cengi_ve_todo_por_rol_o_ingenio()) {
            $stmt = $mysqli->prepare("
                SELECT
                    DISTINCT
                    u.id,
                    u.nombre,
                    u.correo,
                    r.nombre_rol,
                    COALESCE(i.nombre_ingenio, 'Sin ingenio') AS nombre_ingenio
                FROM usuarios u
                INNER JOIN roles r ON r.id = u.rol_id
                LEFT JOIN ingenios i ON i.id = u.ingenio_id
                LEFT JOIN usuario_modulo um ON um.usuario_id = u.id
                LEFT JOIN modulos m ON m.id = um.modulo_id
                WHERE u.nombre LIKE ?
                  AND LOWER(COALESCE(m.nombre, '')) IN ('cursos', 'cengicursos')
                  AND u.ingenio_id = ?
                ORDER BY u.nombre
            ");
            $ingenioId = (int) cengi_ingenio_id_actual();
            $stmt->bind_param('si', $nombre, $ingenioId);
            $stmt->execute();

            return $stmt->get_result();
        }

        $stmt = $mysqli->prepare("
            SELECT
                DISTINCT
                u.id,
                u.nombre,
                u.correo,
                r.nombre_rol,
                COALESCE(i.nombre_ingenio, 'Sin ingenio') AS nombre_ingenio
            FROM usuarios u
            INNER JOIN roles r ON r.id = u.rol_id
            LEFT JOIN ingenios i ON i.id = u.ingenio_id
            LEFT JOIN usuario_modulo um ON um.usuario_id = u.id
            LEFT JOIN modulos m ON m.id = um.modulo_id
            WHERE u.nombre LIKE ?
              AND LOWER(COALESCE(m.nombre, '')) IN ('cursos', 'cengicursos')
            ORDER BY u.nombre
        ");
        $stmt->bind_param('s', $nombre);
        $stmt->execute();

        return $stmt->get_result();
    }
}
?>
