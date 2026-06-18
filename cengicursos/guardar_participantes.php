<?php

require_once("revisar_permisos.php");
cengi_require_admin();

require_once("conexion.php");
require_once("validador_archivos.php");

$db = conectar();

$error = 0;
$resultado = false;

if (empty($_FILES['archivo']['type'])) {

    $error = 1;

} else {

    if (!esCsv($_FILES['archivo']['type'])) {
        $error = 2;
    }
}

if ($error === 0) {

    $ingenio = (int)$_POST['ingenio'];

    $archivotmp = $_FILES['archivo']['tmp_name'];

    $lineas = file($archivotmp);

    $i = 0;

    try {

        foreach ($lineas as $linea) {

            if ($i++ === 0) {
                continue;
            }

            $datos = str_getcsv($linea);

            if (count($datos) < 5) {
                continue;
            }

            $cui = trim($datos[0]);
            $nombre = trim($datos[1]);
            $puesto = trim($datos[2]);
            $area = trim($datos[3]);
            $estado = trim($datos[4]);

            $stmt = $db->prepare("
                INSERT INTO participantes
                (
                    ingenio_id,
                    cui_participantes,
                    nombre_participantes,
                    puesto_participantes,
                    area_participantes,
                    estado_participantes
                )
                VALUES
                (
                    ?, ?, ?, ?, ?, ?
                )
            ");

            $stmt->execute([
                $ingenio,
                $cui,
                $nombre,
                $puesto,
                $area,
                $estado
            ]);
        }

        $resultado = true;

    } catch (PDOException $e) {

        $resultado = false;
        $errorTexto = $e->getMessage();

    }
}

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
						
						<h3>
ERROR AL GUARDAR:
<?php echo htmlspecialchars($errorTexto ?? mensajeError($error)); ?>
</h3>
					<?php } ?>
					
					<a href="index.php" class="btn btn-success">Regresar</a>
					
				</div>
			</div>
		</div>
	</body>
</html>
