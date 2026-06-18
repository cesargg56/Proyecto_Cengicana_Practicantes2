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
	<meta content="text/html;" http-equiv="content-type" charset="utf-8">
</head>
<body>
	<?php include_once("menu.php"); ?>
	<div class="container" id="cargar_participantes">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h3 class="panel-title">Carga de Participantes</h3>
			</div>
			<div class="panel-body">	
				<form  method="POST" action="carga_participantes.php" enctype="multipart/form-data" autocomplete="off" accept-charset="UTF-8">	
				<div class="form-group">
					<label for="nombre" class="col-sm-2 control-label">Ingenio_</label>
					<?php
						//Consulta obtener todos los ingenios
						$sqling ="SELECT id, nombre_ingenios FROM cengi_cursos.ingenios";
						$ingenios =mysqli_query($mysqli, $sqling)or die ('error en la seleccion de base de datos.');
					?>
					<select class="form-control" id="ingenio" name="ingenio" required>
					<?php while ($ingenio = $ingenios->fetch_array(MYSQLI_ASSOC)) {?>
						<option value="<?php echo $ingenio['id']; ?>"><?php echo $ingenio['nombre_ingenios']; ?></option>
					<?php } ?>
					</select>
				</div>

				
			<div class="form-group">
					<label for="users" class="col-sm-2 control-label">Delegado</label>
					<?php
						//Consulta obtener todos los ingenios
						$sqling ="SELECT id, nombre FROM cengi_cursos.users";
						$ingenios =mysqli_query($mysqli, $sqling)or die ('error en la seleccion de base de datos.');
					?>
					<select class="form-control" id="user" name="user" required>
					<?php while ($user = $users->fetch_array(MYSQLI_ASSOC)) {?>
						<option value="<?php echo $user['id']; ?>"><?php echo $user['nombre']; ?></option>
					<?php } ?>
					</select>
				</div>

				<div class="form-group">
					<label for="nombre" class="col-sm-2 control-label">Curso</label>
					<?php
						//Consulta obtener todos los ingenios
						$sqling ="SELECT id, nombre_cursos FROM cengi_cursos.cursos";
						$cursos=mysqli_query($mysqli, $sqling)or die ('error en la seleccion de base de datos.');
					?>
					<select class="form-control" id="curso" name="curso" required>							<?php while ($curso = $cursos->fetch_array(MYSQLI_ASSOC)) {?>
							<option value="<?php echo $curso['id']; ?>"><?php echo $curso['nombre_cursos']; ?></option>
					<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label for="nombre" class="col-sm-2 control-label">Archivo</label>
					<input id="archivo" accept=".csv" class="form-control" name="archivo" type="file" /> 
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-success">Guardar</button>
					<a href="index.php" class="btn btn-danger">Cancelar</a>
				</div>
			</form>
		</div>
	</div>
</div>
</body>
</html>