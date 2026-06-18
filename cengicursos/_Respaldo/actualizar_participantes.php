<?php
	require_once("conexion.php");
	$mysqli=conectar();
	if (!empty($_POST['id']))
	{
	$id= $_POST['id'];
	$ingenio = $_POST['ingenio'];
	$cui = $_POST['cui_participantes'];
	$nombre = $_POST['nombre_participantes'];
	$puesto = $_POST['puesto_participantes'];
	$area = $_POST['area_participantes'];
	$estado = $_POST['estado_participantes'];
	//$actualizado = SELECT NOW();
	
	$sql ="UPDATE participantes SET ingenio_id='$ingenio', cui_participantes='$cui', nombre_participantes='$nombre', puesto_participantes='$puesto', area_participantes='$area', estado_participantes='$estado' where id='$id'";
		//echo "sql=".$sql;
		$resultado =mysqli_query($mysqli, $sql) or die ('error en la seleccion de base de datos.');
	}
else
	{
		$resultado=false;
		$error="Debe indicar el id";
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
				<h3>Registro Actualizado</h3>
				<?php } else { ?>
				<h3>Error al Modificar - <?php echo $error; ?></h3>
				<?php } ?>
				<a href="index.php" class="btn btn-success">Regresar</a>
			</div>
		</div>
	</div>
</body>
</html>