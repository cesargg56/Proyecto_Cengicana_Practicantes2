<?php

require_once("revisar_permisos.php");
cengi_require_admin();

require_once("conexion.php");

$db = conectar();
$resultado = false;
$error = '';

function obtener_post_actualizacion($clave)
{
    $valor = $_POST[$clave] ?? null;

    if (!is_string($valor)) {
        throw new InvalidArgumentException("Falta el campo obligatorio: {$clave}.");
    }

    $valor = trim($valor);

    if ($valor === '') {
        throw new InvalidArgumentException("El campo {$clave} no puede ir vacio.");
    }

    return $valor;
}

if (!empty($_POST['id']))
{
    $id = (int)$_POST['id'];

    try {
        $descripcion = (int)obtener_post_actualizacion('categorias');
        $ingenio = (int)obtener_post_actualizacion('ingenio');
        $tipo = obtener_post_actualizacion('tipo');
        $curso = obtener_post_actualizacion('nombre_cursos');
        $jornada = obtener_post_actualizacion('jornada_cursos');
        $horario = obtener_post_actualizacion('horario');
        $dias = obtener_post_actualizacion('dias');
        $inicio = obtener_post_actualizacion('inicio');
        $fin = obtener_post_actualizacion('fin');

        if ($descripcion <= 0 || $ingenio <= 0) {
            throw new InvalidArgumentException('Debe seleccionar una categoria y un ingenio validos.');
        }

        if (strtotime($inicio) === false || strtotime($fin) === false) {
            throw new InvalidArgumentException('Las fechas de inicio y fin no son validas.');
        }

        if ($inicio > $fin) {
            throw new InvalidArgumentException('La fecha de inicio no puede ser mayor a la fecha final.');
        }

        $sql = "
            UPDATE cursos
            SET
                categoria_curso_id = ?,
                ingenio_id = ?,
                tipo = ?,
                nombre_cursos = ?,
                jornada_cursos = ?,
                dias = ?,
                horario = ?,
                inicio = ?,
                fin = ?
            WHERE id = ?
        ";

        $stmt = $db->prepare($sql);

        $resultado = $stmt->execute([
            $descripcion,
            $ingenio,
            $tipo,
            $curso,
            $jornada,
            $dias,
            $horario,
            $inicio,
            $fin,
            $id
        ]);

    } catch (Throwable $e) {

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

<html lang="es">

<?php include('head.php'); ?>

<body>

	<?php include('menu.php'); ?>

	<div class="container">

		<div class="row">

			<div class="row" style="text-align: center;">

				<?php if($resultado) { ?>

					<h3>Registro Modificado</h3>

				<?php } else { ?>

					<h3>Error al Modificar - <?php echo $error; ?></h3>

				<?php } ?>

				<a href="index.php" class="btn btn-success">
					Regresar
				</a>

			</div>

		</div>

	</div>

</body>
</html>
