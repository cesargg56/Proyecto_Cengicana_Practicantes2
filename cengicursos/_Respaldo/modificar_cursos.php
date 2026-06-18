<?php
	include("conexion.php");
	$mysqli = conectar();
	if (!empty($_GET['id'])) {
		$id= $_GET['id'];

	$sql ="SELECT
	  c.id idcurso,
	  c.categoria_curso_id,
	  ca.descripcion_categorias_cursos,
	  c.ingenio_id,
	  i.nombre_ingenios,  
	  c.nombre_cursos,
	  c.jornada_cursos,
	  c.horario,
	  c.dias,
	  c.inicio,
	  c.fin
	 FROM cengi_cursos.cursos c
	INNER JOIN cengi_cursos.categorias_cursos ca ON (c.categoria_curso_id=ca.id)
	INNER JOIN cengi_cursos.ingenios i on (c.ingenio_id=i.id)";

	$resultado=mysqli_query($mysqli,$sql) or die ("Error en la selección de datos");

	$row=$resultado->fetch_array(MYSQLI_ASSOC);
	}
	else
	{
		$resultado=false;
		$error="Debe indicar el id";
	} 
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
	
	<?php include_once("menu.php"); ?>
	<div class="container">
		<div class="panel panel-success">
			<div class="panel-heading">
		<h3 style="text-align: center">Modificar Registro de Cursos</h3>
		</div>

		<form  method="POST" action="actualizar_cursos.php" autocomplete="off">	
				<div class="form-group">
				<div class="row">
				<div class="col-sm-2">
				<label for="categorias" class="col-sm-2 control-label">Categoria</label>
				</div>
				<div class="col-sm-4">
				<?php
				//Consulta obtener todos los ingenios
				$sqling ="SELECT id, descripcion_categorias_cursos FROM cengi_cursos.categorias_cursos";
						$categorias =mysqli_query($mysqli, $sqling)or die ('error en la seleccion de base de datos.');
				?>
					<select class="form-control" id="categorias" name="categorias">
					<?php while ($categoria = $categorias->fetch_array(MYSQLI_ASSOC)) {?>
						<option value="<?php echo $categoria['id']; ?>" <?php if($categoria['id']==$row['categoria_curso_id']) echo 'selected'; ?>><?php echo $categoria['descripcion_categorias_cursos']; ?></option>
					<?php } ?>
					</select>
				</div>
				<div class="col-sm-1"></div>
			</div>
			</div>

			<input type="hidden" name="id" id="id" value="<?php echo $row['idcurso']; ?>">

			<div class="form-group">
				<div class="row">
				<div class="col-sm-2">
				<label for="ingenio" class="col-sm-2 control-label">Ingenio</label>
				</div>
				<div class="col-sm-4">
				<?php
				//Consulta obtener todos los ingenios
				$sqling ="SELECT id, nombre_ingenios FROM cengi_cursos.ingenios";
						$ingenios =mysqli_query($mysqli, $sqling)or die ('error en la seleccion de base de datos.');
				?>
					<select class="form-control" id="ingenio" name="ingenio">
					<?php while ($ingenio = $ingenios->fetch_array(MYSQLI_ASSOC)) {?>
						<option value="<?php echo $ingenio['id']; ?>" <?php if($ingenio['id']==$row['ingenio_id']) echo 'selected'; ?>><?php echo $ingenio['nombre_ingenios']; ?></option>
					<?php } ?>
					</select>
				</div>
				<div class="col-sm-1"></div>
			</div>
			</div>

			
			<div class="form-group">
					<div class="row">
						<div class="col-sm-2">
					<label for="nombre_cursos" class="col-sm-2 control-label">Curso:</label>
					</div>
					<div class="col-sm-6">
						<input type="text" name= "nombre_cursos"  class="form-control" value="<?php echo $row['nombre_cursos']; ?>"> 
					</div>
					<div class="col-sm-1"></div>
					</div>
			</div>

			
			<div class="form-group">
				<div class="row">
				<div class="col-sm-2">
				<label for="jornada_cursos" class="col-sm-2 control-label">Jornada</label>
				</div>
				<div class="col-sm-6">
					<input type="jornada_cursos" class="form-control" id="jornada_cursos" name="jornada_cursos" value="<?php echo $row['jornada_cursos']; ?>" placeholder="Jornada" required>
				</div>
				<div class="col-sm-1"></div>	
			</div>
		</div>

			<div class="form-group">
				<div class="row">
				<div class="col-sm-2">
				<label for="horario" class="col-sm-2 control-label">Horario</label>
				</div>
				<div class="col-sm-6">
					<input type="horario" class="form-control" id="horario" name="horario" value="<?php echo $row['horario']; ?>" placeholder="Horario" required>
				</div>
				<div class="col-sm-1"></div>
			</div>
			</div>

			<div class="form-group">
				<div class="row">
					<div class="col-sm-2">
				<label for="dias" class="col-sm-2 control-label">Dias</label>
				</div>
				<div class="col-sm-6">
					<input type="dias" class="form-control" id="dias" name="dias" value="<?php echo $row['dias']; ?>" placeholder="dias" required>
				</div>
				</div class="col-sm-1">
			</div>
			

			<div class="form-group">
				<div class="row">
					<div class="col-sm-2">
				<label for="inicio" class="col-sm-2 control-label">Inicio:</label>
				</div>
				<div class="col-sm-6">
					<input type="date" class="form-control" id="inicio" name="inicio" value="<?php echo $row['inicio']; ?>" placeholder="fecha inicio" required>
				</div>
				</div class="col-sm-1">
			</div>
			
			
			<div class="form-group">
				<div class="row">
					<div class="col-sm-2">
				<label for="inicio" class="col-sm-2 control-label">Finaliza:</label>
				</div>
				<div class="col-sm-6">
					<input type="date" class="form-control" id="fin" name="fin" value="<?php echo $row['fin']; ?>" placeholder="fecha finalización" required>
				</div>
				</div class="col-sm-1">
			</div>
			</div>
			

			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<a href="index.php" class="btn btn-default">Regresar</a>
					<button type="submit" class="btn btn-success">Guardar</button>
				</div>
			</div>
		</form>
	</div>
</body>
</html>