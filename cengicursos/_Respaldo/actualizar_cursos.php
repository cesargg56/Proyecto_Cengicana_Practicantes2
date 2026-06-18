<?php
	require_once("conexion.php");
	$mysqli=conectar();
	if (!empty($_POST['id']))
	{
		$id=$_POST['id'];
		$descripcion = $_POST['categorias'];
		$ingenio = $_POST ['ingenio'];
		$curso = $_POST['nombre_cursos'];
		$jornada = $_POST['jornada_cursos'];
		$horario = $_POST['horario'];
		$dias = $_POST['dias'];
		

	$sql ="UPDATE cursos SET categoria_curso_id='$descripcion', ingenio_id='$ingenio', nombre_cursos='$curso', jornada_cursos='$jornada', dias='$dias', horario='$horario' where id=$id";
		
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
				<a href="index.php" class="btn btn-success">Regresar</a>
			</div>
		</div>
	</div>
</body>
</html>