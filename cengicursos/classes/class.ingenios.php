<?php
error_reporting(E_ALL);
require_once __DIR__ . '/class.Database.php';

if (0 > version_compare(PHP_VERSION, '5')) {
    die('Este archivo fue generado para PHP 5 o superior');
}

class ingenios
{
    public function __construct()
    {
    }

    public function consultar_visibles()
    {
        $db = Database::getInstancia();
        $sql = "SELECT id, nombre_ingenios FROM ingenios";

        if (function_exists('cengi_scope_sql_por_nombre_ingenio') && !cengi_ve_todo_por_rol_o_ingenio()) {
            $sql .= cengi_scope_sql_por_nombre_ingenio('', false);
        }

        $sql .= " ORDER BY nombre_ingenios";
        return $db->ejecutar_idu($sql);
    }

    public function getCursoByNombre($nombre)
    {
        $db = Database::getInstancia();
        $nombre = $db->escape($nombre);

        $sql = "SELECT id, nombre_ingenios FROM ingenios WHERE nombre_ingenios LIKE '%{$nombre}%'";

        if (function_exists('cengi_scope_sql_por_nombre_ingenio') && !cengi_ve_todo_por_rol_o_ingenio()) {
            $sql .= cengi_scope_sql_por_nombre_ingenio('', true);
        }

        $sql .= " ORDER BY nombre_ingenios";
        return $db->ejecutar_idu($sql);
    }
}
?>
