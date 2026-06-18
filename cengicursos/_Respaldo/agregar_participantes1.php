<?php
	require_once("conexion.php");
	$mysqli=conectar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta name="viewport" content="width=device-widht, initial-scale=1">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-theme.css" rel="stylesheet">
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<meta charset="utf-8">
</head>
<body>
	<?php include_once("menu.php"); ?>
	<div class="container">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h3 class="panel-title"> Nuevo Registro de Participantes</h3>
			</div>

			<div class="panel-body">	
		<form  method="POST" action="guardar_participantes.php" autocomplete="off">	
			<div class="form-group">
				<div class="row">
					<div class="col-sm-2">
						<label for="cui" class="col-sm-2 control-label">CUI</label>
					</div>
					<div class="col-sm-4">
						<input type="text" class="form-control" id="cui" name="cui" placeholder="0000-00000-0000" required>
					</div>
					<div class="col-sm-1">
						<label for="nombre" class="col-sm-2 control-label">Nombre</label>
					</div>
					<div class="col-sm-4">
						<input type="text" class="form-control" id="nombre"  name="nombre" placeholder="Nombre Participante" required>
					</div>
					<div class="col-sm-1"></div>
				</div>
			</div>
			<div class="form-group">
				<div class="row">
					<div class="col-sm-2">
						<label for="nombre" class="col-sm-2 control-label">Ingenio</label>
					</div>
					<div class="col-sm-4">
						<?php
						//Consulta obtener todos los ingenios
						$sqling ="SELECT id, nombre_ingenios FROM cengi_cursos.ingenios";
						$ingenios =mysqli_query($mysqli, $sqling)or die ('error en la seleccion de base de datos.');
						?>
						<select class="form-control" id="ingenio" name="ingenio" required>
						<?php while ($ingenio = $ingenios->fetch_array(MYSQLI_ASSOC)) {?>
							<option value="<?php echo $ingenio['id']; ?>"><?php echo utf8_encode($ingenio['nombre_ingenios']); ?></option>
						<?php } ?>
						</select>
					</div>
					<div class="col-sm-1">
						<label for="area" class="col-sm-2 control-label">Área</label>
					</div>
					<div class="col-sm-4">
						<input type="text" class="form-control"  id="area"  name="area" placeholder="Área" required>
					</div>
					<div class="col-sm-1"></div>
				</div>
			</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-2">
							<label for="dpi" class="col-sm-2 control-label">Puesto</label>
						</div>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="puesto"  name="puesto" placeholder="Puesto" required>
						</div>
						<div class="col-sm-1"></div>
					</div>
				</div>
			<div class="form-group">
				<div class="row">
					<div class="col-sm-offset-2 col-sm-10">
						<button type="submit" class="btn btn-success">Guardar</button>
						<a href="index.php" class="btn btn-danger">Cancelar</a>
					</div>
				</div>
			</div>
		</form>
	</div>
</body>
</html>