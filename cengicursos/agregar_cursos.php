<?php
require_once "revisar_permisos.php";
cengi_require_admin();
//require c'conexion.php
require_once "conexion.php";
$db = conectar();
require_once "menu.php";
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-theme.css" rel="stylesheet">
	<link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet">

	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/bootstrap-datetimepicker.min.js"></script>
</head>

<body>

<?php menu_render(); ?>

<div class="container">

	<div class="panel panel-success">

		<div class="panel-heading">
			<h3 class="panel-title">Agregar Cursos</h3>
		</div>

		<div class="panel-body">

			<form method="POST" action="guardar_cursos.php" autocomplete="off">

				<!-- CATEGORÍA -->
				<div class="form-group">
					<div class="row">

						<div class="col-sm-2">
							<label for="categorias_cursos" class="control-label">
								Categoría
							</label>
						</div>

						<div class="col-sm-4">

							<?php
							$sqling = "SELECT id, descripcion_categorias_cursos
           FROM categorias_cursos";
		   $categorias = $db->query($sqling);

?>

							<select class="form-control"
									id="categorias_cursos"
									name="categorias_cursos"
									required>

<?php while ($categoria = $categorias->fetch(PDO::FETCH_ASSOC)) { ?>
									<option value="<?php echo $categoria['id']; ?>">
										<?php echo $categoria['descripcion_categorias_cursos']; ?>
									</option>

								<?php } ?>

							</select>

						</div>

					</div>
				</div>

				<!-- INGENIO -->
				<div class="form-group">
					<div class="row">

						<div class="col-sm-2">
							<label for="ingenio" class="control-label">
								Ingenio
							</label>
						</div>

						<div class="col-sm-4">

							<?php
							$sqling = "SELECT id, nombre_ingenios
           FROM ingenios";

$ingenios = $db->query($sqling);
							?>

							<select class="form-control"
									id="ingenio"
									name="ingenio"
									required>

<?php while ($ingenio = $ingenios->fetch(PDO::FETCH_ASSOC)) { ?>
									<option value="<?php echo $ingenio['id']; ?>">
										<?php echo $ingenio['nombre_ingenios']; ?>
									</option>

								<?php } ?>

							</select>

						</div>

					</div>
				</div>

				<!-- TIPO -->
				<div class="form-group">
					<div class="row">

						<div class="col-sm-2">
							<label for="tipo" class="control-label">
								Tipo
							</label>
						</div>

						<div class="col-sm-4">
							<select class="form-control"
									id="tipo"
									name="tipo"
									required>

								<option value="Curso">Curso</option>
								<option value="Diplomado">Diplomado</option>
								<option value="Seminario">Seminario</option>

							</select>
						</div>

					</div>
				</div>

				<!-- NOMBRE -->
				<div class="form-group">
					<div class="row">

						<div class="col-sm-2">
							<label for="nombre_cursos" class="control-label">
								Nombre
							</label>
						</div>

						<div class="col-sm-4">
							<input type="text"
								   name="nombre_cursos"
								   class="form-control"
								   required
								   placeholder="Nombre del Curso">
						</div>

					</div>
				</div>

				<!-- JORNADA -->
				<div class="form-group">
					<div class="row">

						<div class="col-sm-2">
							<label for="jornada_cursos" class="control-label">
								Jornada
							</label>
						</div>

						<div class="col-sm-4">

							<select class="form-control"
									id="jornada_cursos"
									name="jornada_cursos">

								<option value="Matutina">Matutina</option>
								<option value="Vespertina">Vespertina</option>
								<option value="Todo Completo">Todo Completo</option>

							</select>

						</div>

					</div>
				</div>

				<!-- DÍAS -->
				<div class="form-group">
					<div class="row">

						<div class="col-sm-2">
							<label for="dias" class="control-label">
								Días
							</label>
						</div>

						<div class="col-sm-4">
							<input type="text"
								   name="dias"
								   class="form-control"
								   required
								   placeholder="Días a ejecutarse">
						</div>

					</div>
				</div>

				<!-- HORARIO -->
				<div class="form-group">
					<div class="row">

						<div class="col-sm-2">
							<label for="horario" class="control-label">
								Horario
							</label>
						</div>

						<div class="col-sm-4">
							<input type="text"
								   name="horario"
								   class="form-control"
								   required
								   placeholder="Formato 24 horas">
						</div>

					</div>
				</div>

				<!-- FECHA INICIO -->
				<div class="form-group">
					<div class="row">

						<div class="col-sm-2">
							<label for="inicio" class="control-label">
								Inicia
							</label>
						</div>

						<div class="col-sm-4">
							<input type="date"
								   name="inicio"
								   class="form-control"
								   required>
						</div>

					</div>
				</div>

				<!-- FECHA FINAL -->
				<div class="form-group">
					<div class="row">

						<div class="col-sm-2">
							<label for="fin" class="control-label">
								Finaliza
							</label>
						</div>

						<div class="col-sm-4">
							<input type="date"
								   name="fin"
								   class="form-control"
								   required>
						</div>

					</div>
				</div>

				<!-- BOTONES -->
				<div class="form-group">

					<div class="col-sm-offset-2 col-sm-10">

						<button type="submit" class="btn btn-success">
							Guardar
						</button>

						<a href="index.php" class="btn btn-danger">
							Cancelar
						</a>

					</div>

				</div>

			</form>

		</div>

	</div>

</div>

</body>
</html>
