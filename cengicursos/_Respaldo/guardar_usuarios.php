<?php
	include("conexion.php");
	$mysqli = conectar();

	$nombre = $_POST['nombre'];
	$email = $_POST['email'];
	$ingenio = $_POST['ingenio'];
	$rol = $_POST['rol'];
	$password = md5($_POST['password']);
	
	$sql = "INSERT INTO users (nombre, email, ingenio_id, rol, password) VALUES ('$nombre', '$email', '$ingenio', '$rol', '$password')";
	//$resultado = $mysqli->query($sql);

//echo $sql;
$resultado = mysqli_query($mysqli, $sql) or die ('Error al insertar los registros en base de datos'.mysql_error());
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
				<div class="row alert alert-success" style="text-align:center">
					<?php if($resultado) { ?>
						<h3>REGISTRO GUARDADO</h3>
						<?php } else { ?>
						<h3>ERROR AL GUARDAR</h3>
					<?php } ?>
					
					<a href="index.php" class="btn btn-success">Regresar</a>
					
				</div>
			</div>
		</div>
	</body>
</html>