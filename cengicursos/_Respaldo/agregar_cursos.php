<?php
//require c'conexion.php
require_once "conexion.php";
$mysqli = conectar();
require_once "menu.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta name="viewport" content="width=device-widht, initial-scale=1">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-theme.css" rel="stylesheet">
	<link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet">
	<script src="js/bootstrap-datetimepicker.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<meta charset="utf-8">
</head>
<body>
	<?php menu_render();?>
	<div class="container">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h3 class="panel-title"> Agregar Cursos</h3>
			</div>
			<div class="panel-body">
		<form  method="POST" action="guardar_cursos.php" autocomplete="off">
			<div class="form-group">
					<div class="row">
					<div class="col-sm-2">
					<label for="categoria" class="col-sm-2 control-label">Categoría</label>
					</div>
					<div class="col-sm-4">
						<?php
//Consulta obtener todos las categorías
$sqling     = "SELECT id, descripcion_categorias_cursos FROM cengi_cursos.categorias_cursos";
$categorias = mysqli_query($mysqli, $sqling) or die('error en la seleccion de base de datos.');
?>
					<select class="form-control" id="categorias_cursos" name="categorias_cursos" required>
					<?php while ($categoria = $categorias->fetch_array(MYSQLI_ASSOC)) {?>
						<option value="<?php echo $categoria['id']; ?>"><?php echo $categoria['descripcion_categorias_cursos']; ?></option>
					<?php }?>
					</select>
					</div>
				<div class="col-sm-1"></div>
				</div>
			</div>
			<div class="form-group">
				<div class="row">
					<div class="col-sm-2">
						<label for="ingenio" class="col-sm-2 control-label">Ingenio</label>
					</div>
					<div class="col-sm-4">
						<?php
//Consulta obtener todos los ingenios
$sqling   = "SELECT id, nombre_ingenios FROM cengi_cursos.ingenios";
$ingenios = mysqli_query($mysqli, $sqling) or die('error en la seleccion de base de datos.');
?>
						<select class="form-control" id="ingenio" name="ingenio" required>
						<?php while ($ingenio = $ingenios->fetch_array(MYSQLI_ASSOC)) {?>
							<option value="<?php echo $ingenio['id']; ?>"><?php echo $ingenio['nombre_ingenios']; ?></option>
						<?php }?>
						</select>
					</div>
					<div class="col-sm-1"></div>
					</div>
				</div>
					<div class="form-group">
					<div class="row">
					<div class="col-sm-2">
					<label for="nombre_cursos" class="col-sm-2 control-label">Nombre:</label>
					</div>
					<div class="col-sm-4">
						<input type="text" name="nombre_cursos"  class="form-control" required placeholder="Nombre del Curso">
					</div>
				<div class="col-sm-1"></div>
				</div>
			</div>
			<div class="form-group">
				<div class="row">
					<div class="col-sm-2">
					<label for="jornada_cursos" class="col-sm-2 control-label">Jornada</label>
				</div>
					<div class="col-sm-4">
						<select class="form-control" id="jornada_cursos" name="jornada_cursos">
							<option value="Matutina">Matutina</option>
							<option value="Vespertina">Vespertina</option>
							<option value="Todo Completo">Todo Completo</option>
						</select>
					</div>
					<div class="col-sm-1"></div>
			</div>
		</div>
			<div class="form-group">
					<div class="row">
					<div class="col-sm-2">
					<label for="dias" class="col-sm-2 control-label">Días:</label>
					</div>
					<div class="col-sm-4">
						<input type="text" name="dias"  class="form-control" required placeholder="Días a ejecutarse">
					</div>
				<div class="col-sm-1"></div>
				</div>
			</div>
			<div class="form-group">
					<div class="row">
					<div class="col-sm-2">
					<label for="horario" class="col-sm-2 control-label">Horario:</label>
					</div>
					<div class="col-sm-4">
						<input type="text" name="horario"  class="form-control" required placeholder="Formato 24 horas">
					</div>

					<div class="col-sm-1"></div>
				</div>
			</div>
			<div class="form-group">
					<div class="row">
					<div class="col-sm-2">
					<label for="inicio" class="col-sm-2 control-label">Inicia:</label>
					</div>
					<div class="col-sm-4">
						<input type="date" name="inicio"  class="form-control" required placeholder="fecha inicio">
					</div>

			<div class="col-sm-1"></div>
				</div>
			</div>
			<div class="form-group">
					<div class="row">
					<div class="col-sm-2">
					<label for="inicio" class="col-sm-2 control-label">Finaliza:</label>
					</div>
					<div class="col-sm-4">
						<input type="date" name="inicio"  class="form-control" required placeholder="fecha inicio">
					</div>





					<div class="col-sm-1"></div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="submit" class="btn btn-success">Guardar</button>
					<a href="index.php" class="btn btn-danger">Cancelar</a>
				</div>
			</div>
		</form>
	</div>
</body>
</html>