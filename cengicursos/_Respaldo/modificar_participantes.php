<?php
	include("conexion.php");
	$mysqli = conectar();
	if (!empty($_GET['id'])) {
		$id= $_GET['id'];

	$sql ="SELECT
	  p.id idparticipante,
	  p.ingenio_id,
	  p.cui_participantes,
	  p.nombre_participantes,
	  p.puesto_participantes,
	  p.area_participantes,
	  p.estado_participantes
	 FROM cengi_cursos.participantes p
	 INNER JOIN cengi_cursos.ingenios i ON (p.id=$id)";

//echo "sql=".$sql;	
	$resultado=mysqli_query($mysqli,$sql) or die ("Error en la selección de datos");

	$row=$resultado->fetch_array(MYSQLI_ASSOC);
	}
	else
	{
		$resultado=false;
		$error="Debe indicar el id";
	} 
?>
<html lang="es">
<?php include('head.php'); ?>
<body>
	<?php include('menu.php'); ?>
	<div class="container">
		<div class="row">
				<h3 style="text-align: center">Modificar Registro Participantes</h3>
			</div>
		<form  method="POST" action="actualizar_participantes.php" autocomplete="off">	
			
			<input type="hidden" name="id" id="id" value="<?php echo $row['idparticipante']; ?>">

			<div class="form-group">
				<label for="ingenio" class="col-sm-2 control-label">Ingenio</label>
				<div class="col-sm-10">
				<?php
				//Consulta obtener todos los ingenios
				$sqling ="SELECT id, nombre_ingenios FROM cengi_cursos.ingenios";
						$ingenios =mysqli_query($mysqli, $sqling)or die ('error en la seleccion de base de datos.');
				?>
					<select class="form-control" id="ingenio" name="ingenio">
					<?php while ($ingenio = $ingenios->fetch_array(MYSQLI_ASSOC)) {?>
						<option value="<?php echo $ingenio['id']; ?>" <?php if($ingenio['id']==$row['ingenio_id']) echo 'selected';?>><?php echo $ingenio['nombre_ingenios']; ?></option>
					<?php } ?>
					</select>
				</div>
			</div>

			<div class="form-group">
					<label for="cui_participantes" class="col-sm-2 control-label">CUI:</label>
					<div class="col-sm-10">
						<input type="text" name= "cui_participantes"  class="form-control" value="<?php echo $row['cui_participantes']; ?>"> 
					</div>
			</div>

			


			<div class="form-group">
				<label for="email" class="col-sm-2 control-label">Nombre Participante</label>
				<div class="col-sm-10">
					<input type="nombre_participantes" class="form-control" id="nombre_participantes" name="nombre_participantes" value="<?php echo $row['nombre_participantes']; ?>" placeholder="nombre participantes" required>
				</div>
			</div>

			

			<div class="form-group">
					<label for="puesto_participantes" class="col-sm-2 control-label">Puesto</label>
					<div class="col-sm-10">
						<input type="puesto_participantes" class="form-control" id="puesto_participantes" name="puesto_participantes" value="<?php echo $row['puesto_participantes']; ?>" placeholder="puesto" required>
					</div>
			</div>

			<div class="form-group">
				<label for="area_participantes" class="col-sm-2 control-label">Área</label>
				<div class="col-sm-10">
					<input type="area_participantes" class="form-control" id="area_participantes" name="area_participantes" value=" <?php echo $row['area_participantes'];?>" placeholder="Área" required>
				</div>
			</div>

			<div class="form-group">
					<label for="estado_participantes" class="col-sm-2 control-label">Estado</label>
					<div class="col-sm-10">
						<select class="form-control" id="estado_participantes" name="estado_participantes">
							<option value="1" <?php if($row['estado_participantes']=='1') echo 'selected'; ?>>Activo</option>
							<option value="0" <?php if($row['estado_participantes']=='0') echo 'selected'; ?>>Inactivo</option>
						</select>
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