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
            UPDATE ingenios
            SET
                nombre_ingenios = ?,
                actualizado = NOW()
            WHERE id = ?
        ";

        $stmt = $db->prepare($sql);

        $resultado = $stmt->execute([
            $nombre,
            $id
        ]);

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
				<a href="ver_ingenios.php" class="btn btn-success">Regresar</a>
			</div>
		</div>
	</div>
</body>
</html>
