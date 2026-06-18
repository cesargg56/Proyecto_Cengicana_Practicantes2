<?php require_once "menu.php";?>
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
				<h3 class="panel-title"> Nuevo Registro de Ingenios</h3>
			</div>

			<div class="panel-body">
		<form  method="POST" action="guardar_ingenios.php" autocomplete="off">
			<div class="form-group">
					<div class="row">
					<div class="col-sm-2">
					<label for="nombre" class="col-sm-2 control-label">Nombre:</label>
					</div>
					<div class="col-sm-4">
						<input type="text" name= "nombre"  class="form-control" required placeholder="Nombre">
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