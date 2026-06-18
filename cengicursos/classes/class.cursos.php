<?php
error_reporting(E_ALL);
require_once __DIR__ . '/class.Database.php';

if (0 > version_compare(PHP_VERSION, '5')) {
    die('Este archivo fue generado para PHP 5 o superior');
}

class cursos
{
    public function __construct()
    {
    }

    private function consulta_base()
    {
        return "
            SELECT
                cu.id AS cursoid,
                ca.descripcion_categorias_cursos,
                i.nombre_ingenios,
                cu.nombre_cursos,
                cu.jornada_cursos,
                cu.dias,
                cu.horario,
                cu.creado,
                cu.actualizado
            FROM cursos cu
            INNER JOIN categorias_cursos ca ON cu.categoria_curso_id = ca.id
            INNER JOIN ingenios i ON cu.ingenio_id = i.id
        ";
    }

    public function consultar_todos()
    {
        $db = Database::getInstancia();
        $sql = $this->consulta_base();
        $sql .= function_exists('cengi_scope_sql_por_nombre_ingenio') ? cengi_scope_sql_por_nombre_ingenio('i', false) : '';
        $sql .= " ORDER BY cu.nombre_cursos";

        return $db->ejecutar_idu($sql);
    }

    public function consultar_visibles()
    {
        $db = Database::getInstancia();
        $sql = $this->consulta_base() . " WHERE ca.estado_categorias_cursos <> 0";
        $sql .= function_exists('cengi_scope_sql_por_nombre_ingenio') ? cengi_scope_sql_por_nombre_ingenio('i', true) : '';
        $sql .= " ORDER BY cu.nombre_cursos";

        return $db->ejecutar_idu($sql);
    }

    public function getCursoByNombre($nombre)
    {
        $db = Database::getInstancia();
        $nombre = $db->escape($nombre);

        $sql = $this->consulta_base() . "
            WHERE ca.estado_categorias_cursos <> 0
              AND cu.nombre_cursos LIKE '%{$nombre}%'
        ";
        $sql .= function_exists('cengi_scope_sql_por_nombre_ingenio') ? cengi_scope_sql_por_nombre_ingenio('i', true) : '';
        $sql .= " ORDER BY cu.nombre_cursos";

        return $db->ejecutar_idu($sql);
    }
}
?>
