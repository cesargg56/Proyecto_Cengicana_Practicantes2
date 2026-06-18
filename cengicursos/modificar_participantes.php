<?php
require_once("revisar_permisos.php");
require_once("conexion.php");

cengi_require_editar_participantes("participantes.php");

$db = conectar();

if (!empty($_GET['id'])) {

    $id = (int)$_GET['id'];

    $sql = "
        SELECT
            p.id AS idparticipante,
            p.ingenio_id,
            p.cui_participantes,
            p.nombre_participantes,
            p.puesto_participantes,
            p.area_participantes,
            p.estado_participantes
        FROM participantes p
        WHERE p.id = ?
    ";

    if (!cengi_ve_todo_por_rol_o_ingenio()) {
        $sql .= " AND p.ingenio_id = " . (int) cengi_ingenio_id_actual();
    }

    $stmt = $db->prepare($sql);

    $stmt->execute([$id]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        die("Participante no encontrado");
    }

} else {

    $resultado = false;
    $error = "Debe indicar el id";

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
				
				$ingenios = $db->query("
    SELECT id, nombre_ingenios
    FROM ingenios
    ORDER BY nombre_ingenios
");
				?>
					<select class="form-control" id="ingenio" name="ingenio">
					<?php while ($ingenio = $ingenios->fetch(PDO::FETCH_ASSOC)) { ?>
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
					<input type="area_participantes" class="form-control" id="area_participantes" name="area_participantes" value=" <?php echo htmlspecialchars($row['area_participantes']) ;?>" placeholder="Área" required>
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
