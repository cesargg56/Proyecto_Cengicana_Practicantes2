<?php
	include("conexion.php");
	$mysqli = conectar();
	$ingenio="No se encontró el ingenio";
	if (!empty($_GET['id'])) {
		$id=(int)addslashes($_GET['id']);

		$sql_nombre="SELECT nombre_ingenios FROM ingenios WHERE ingenios.id=$id";
		$result_ingenio =mysqli_query($mysqli, $sql_nombre)or die ('error en la seleccion de base de datos.');
		$fila_nombre=$result_ingenio->fetch_array(MYSQLI_ASSOC);
		$ingenio=$fila_nombre['nombre_ingenios'];
		$sql = "DELETE FROM ingenios WHERE id=$id";
		$resultado = mysqli_query($mysqli, $sql) or $error=mysqli_error($mysqli);
		if(strstr($error, 'parent row: a foreign')<>FALSE) $error="El ingenio tiene cursos asociados";
		mysqli_close($mysqli);
	}
	else
	{
		$resultado=false;
		$error="Debe indicar el id";
	} 
		
?>
 
<html lang="es">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css">
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<meta charset="utf-8">
</head>
	<body>
		<?php include ('menu.php'); ?>
		<div class="container">
			<div class="row">
				<div class="row alert alert-info" style="text-align:center">
					<?php if($resultado) { ?>
						<h3>REGISTRO <strong><?php echo strtoupper($ingenio);?></strong> ELIMINADO</h3>
						
						<?php } else { ?>
						<h3>ERROR AL ELIMINAR: <?php echo strtoupper($error); ?></h3>
					<?php } ?>
					
					<a href="ver_ingenios.php" class="btn btn-success">Regresar</a>
				</div>
			</div>
		</div>
	</body>
</html>