<?php
	include("conexion.php");
	require_once("validador_archivos.php");
	$mysqli = conectar();
//obtener el archivo csv

	$error=0;
	if(empty($_FILES['archivo']['type']))
	{
		$error=1;
	}
	else{
		if(esCsv($_FILES['archivo']['type']))
		{
			$tipo = $_FILES['archivo']['type'];
		}
		else {
			$error=2;
		}
	} 


	//if($tipo<>'')
 	$tamanio = $_FILES['archivo']['size'];
 	$archivotmp = $_FILES['archivo']['tmp_name'];

 // cargamos el archivo
 	$lineas = file($archivotmp);
 	//var_dump( $_FILES );

 	//inicializamos variable a 0, esto nos ayudará a indicarle que no lea la primera línea
	$i=0;
 
//Recorremos el bucle para leer línea por línea
	foreach ($lineas as $linea_num => $linea)
	{ 
   //abrimos bucle
   /*si es diferente a 0 significa que no se encuentra en la primera línea 
   (con los títulos de las columnas) y por lo tanto puede leerla*/
   	if($i != 0)
   	{ 
       //abrimos condición, solo entrará en la condición a partir de la segunda pasada del bucle.
       /* La funcion explode nos ayuda a delimitar los campos, por lo tanto irá 
       leyendo hasta que encuentre un ; */
       $datos = explode(",",$linea);
 
       //Almacenamos los datos que vamos leyendo en una variable
       //usamos la función utf8_encode para leer correctamente los caracteres especiales
	
	$ingenio= $_POST['ingenio'];
	$cui	= $_POST[$datos[0]];
	$nombre = $_POST[$datos[1]];
	$puesto = $_POST[$datos[2]];
	$area   = $_POST[$datos[3]];
	$estado = $_POST[$datos[4]];
	
	$sql = ("INSERT INTO participantes (ingenio_id, cui_participantes, nombre_participantes, puesto_participantes, area_participantes, estado_participantes) VALUES ('$ingenio', '$cui', '$nombre', '$puesto', '$area', '$estado')");
	
	//$resultado = $mysqli->query($sql);
		}

			/*Cuando pase la primera pasada se incrementará nuestro valor y a la siguiente pasada ya 
   			entraremos en la condición, de esta manera conseguimos que no lea la primera línea.*/
   		$i++;
   //cerramos bucle
		}
	echo $sql;

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
				<?php if($error>0){ ?>
				<div class="row alert alert-danger" >
					<?php echo ("<strong>Error: </strong>".mensajeError($error)); ?>
				</div>
				<?php } ?>
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