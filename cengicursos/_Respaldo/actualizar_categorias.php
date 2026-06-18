<?php
	require_once("conexion.php");
	$mysqli=conectar();
	if (!empty($_POST['id']))
	{
		$id=$_POST['id'];
		$nombre = $_POST['nombre'];

		$sql ="UPDATE categorias_cursos SET descripcion_categorias_cursos='$nombre' where id =$id";
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
				<h3>Registro Modificado</h3>
				<?php } else { ?>
				<h3>Error al Modificar - <?php echo $error; ?></h3>
				<?php } ?>
				<a href="ver_categorias.php" class="btn btn-success">Regresar</a>
			</div>
		</div>
	</div>
</body>
</html>