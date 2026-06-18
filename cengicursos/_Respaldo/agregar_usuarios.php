<?php
require_once "conexion.php";
require_once "menu.php";
$mysqli = conectar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta name="viewport" content="width=device-widht, initial-scale=1">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-theme.css" rel="stylesheet">
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</head>
<body>
	<?php menu_render();?>
	<div class="container">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h3 class="panel-title"> Nuevo Registro de Usuarios</h3>
			</div>

			<div class="panel-body">
				<form  method="POST" action="guardar_usuarios.php" autocomplete="off">
			<div class="form-group">
					<div class="row">
					<div class="col-sm-2">
					<label for="nombre" class="col-sm-2 control-label">Nombre:</label>
					</div>
					<div class="col-sm-6">
						<input type="text" name= "nombre"  class="form-control" required placeholder="Nombre">
					</div>
					<div class="col-sm-1"></div>
					</div>
			</div>
			<div class="form-group">
					<div class="row">
					<div class="col-sm-2">
					<label for="email" class="col-sm-2 control-label">Email:</label>
					</div>
					<div class="col-sm-6">
						<input type="email" class="form-control" id="email"  name="email" placeholder="Email" required>
					</div>
					<div class="col-sm-1"></div>
				</div>
			</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-2">
					<label for="dpi" class="col-sm-2 control-label">Ingenio:</label>
					</div>
					<div class="col-sm-6">
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
					<label for="rol" class="col-sm-2 control-label">Rol:</label>
						</div>
					<div class="col-sm-6">
						<select class="form-control" id="rol" name="rol">
							<option value="Administrador">Administrador</option>
							<option value="Delegado">Delegado</option>
						</select>
					</div>
				<div class="col-sm-1"></div>
				</div>
				</div>

				<div class="form-group">
					<div class="row">
						<div class="col-sm-2">
						<label for="password" class="col-sm-2 control-label">Password:</label>
						</div>
						<div class="col-sm-6">
						<input type="password" class="form-control" id="password"  name="password" placeholder="Password" required>
						</div>
						<div class="col-sm-1"></div>
					</div>
				</div>

			<div class="form-group">
				<div class="col-sm-offset-2">
					<button type="submit" class="btn btn-success">Guardar</button>
					<a href="index.php" class="btn btn-danger">Cancelar</a>
				</div>
			</div>
		</form>
	</div>
</body>
</html>