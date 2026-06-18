<?php
	include("conexion.php");
	$mysqli = conectar();
	if (!empty($_GET['idusuarios'])) {
		$id= $_GET['idusuarios'];
		$sql = "DELETE FROM users WHERE id=$id";
		$resultado = mysqli_query($mysqli, $sql) or die ('Error al insertar los registros en base de datos'.mysql_error());
		mysqli_close($mysqli);
	}
	else
	{
		$resultado=false;
		$error="Debe indicar el id";
	} 
		
?>
 
<html lang="es">
	<?php include ('head.php'); ?>	
	<body>
		<?php include ('menu.php'); ?>
		<div class="container">
			<div class="row">
				<div class="row alert alert-info" style="text-align:center">
					<?php if($resultado) { ?>
						<h3>REGISTRO ELIMINADO</h3>
						<?php } else { ?>
						<h3>ERROR AL GUARDAR - <?php echo $error; ?></h3>
					<?php } ?>
					
					<a href="index.php" class="btn btn-success">Regresar</a>
					
				</div>
			</div>
		</div>
	</body>
</html>