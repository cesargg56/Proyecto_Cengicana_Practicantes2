<?php

require_once("revisar_permisos.php");
cengi_require_admin();

require_once("conexion.php");

$db = conectar();

if (!empty($_POST['id']))
{
    $id = (int)$_POST['id'];
    $nombre = trim($_POST['nombre']);

    try {

        $sql = "
            UPDATE categorias_cursos
            SET descripcion_categorias_cursos = ?
            WHERE id = ?
        ";

        $stmt = $db->prepare($sql);
        $resultado = $stmt->execute([$nombre, $id]);

    } catch (PDOException $e) {

        $resultado = false;
        $error = $e->getMessage();

    }

}
else
{
    $resultado = false;
    $error = "Debe indicar el id";
}

?>