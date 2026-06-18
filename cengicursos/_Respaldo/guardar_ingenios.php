<?php
	include("conexion.php");
	$mysqli = conectar();

	$nombre = $_POST['nombre'];
	
	
	$sql = "INSERT INTO ingenios(nombre_ingenios, creado) VALUES ('$nombre',now())";
	//$resultado = $mysqli->query($sql);

//echo $sql;
$resultado = mysqli_query($mysqli, $sql) or die ('Error al insertar los registros en base de datos'.mysqli_error($mysqli));
	mysqli_close($mysqli);
?>
 
<html lang="es">
	<head>
		
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/bootstrap-theme.css" rel="stylesheet">
		<script src="js/jquery-3.1.1.min.js"></script>
		<script src="js/bootstrap.min.js"></script>	
	</head>
	
	<body>
		<div class="container">
			<div class="row">
				<div class="row alert alert-info" style="text-align:center">
					<?php if($resultado) { ?>
						<h3>REGISTRO GUARDADO</h3>
						<?php } else { ?>
						<h3>ERROR AL GUARDAR</h3>
					<?php } ?>
					
					<a href="ver_ingenios.php" class="btn btn-primary">Regresar</a>
					
				</div>
			</div>
		</div>
	</body>
</html>