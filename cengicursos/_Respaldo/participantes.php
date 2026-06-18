<?php
$a = session_id();
if ($a == '') {
    session_start();
}

//require c'conexion.php
require_once "conexion.php";
$mysqli = conectar();

# code...

/*var_dump(isset($_SESSION["CMenus"]));
exit();*/
$menu = "menu.php";
if (isset($_SESSION["CMenus"])) {
    $elRol = $_SESSION["CMenus"];
    if ((strcmp($elRol, 'Administrador') !== 0) && (strcmp($elRol, 'Delegado') !== 0)) {
        header("Location: Login_v6/index.php?error=2");
        exit;
    }

    if (strcmp($elRol, 'Delegado') === 0) {
        $menu = "menu_delegados.php";
    }
} else {
    header("Location: Login_v6/index.php?error=3");
    exit;
}
require_once realpath("classes/class.participantes.php");
require_once realpath("classes/class.cursos.php");
require_once realpath("classes/class.ingenios.php");
require_once realpath("classes/class.users.php");

$mysqli   = new participantes;
$cursos   = new cursos;
$ingenios = new ingenios;
$users    = new users;

if (!empty($_POST['campo'])) {
    $resultado = $mysqli->getParticipantesByNombre($_POST['campo']);
} else {
    $resultado = $mysqli->consultar_visibles();
}

require_once $menu;
?>

<html lang="es">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css">
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/main.js"></script>
	<meta charset="utf-8">
</head>

<body>
	<?php menu_render();?>
	<div class="container" id="cargarParticipantes" style="display: none;">
		<button type="button" id="btnShowGrid" class="btn btn-lg btn-info"><span class="glyphicon glyphicon-list-alt"></span> Ver Todos</button>
		<br><br>
		<div class="panel panel-success">
			<div class="panel-heading">
				<h3 class="panel-title">Carga de Participantes</h3>
			</div>
			<div class="panel-body">
				<form  method="POST" action="carga_participantes.php" enctype="multipart/form-data" autocomplete="off" accept-charset="UTF-8">
				<div class="form-group">
					<label for="nombre" class="col-sm-2 control-label">Ingenio</label>

					<select class="form-control" id="ingenio" name="ingenio" required>
					<?php
$result_ingenios = $ingenios->consultar_visibles();
while ($ingenio = $result_ingenios->fetch_array(MYSQLI_ASSOC)) {?>
						<option value="<?php echo $ingenio['id']; ?>"><?php echo $ingenio['nombre_ingenios']; ?></option>
					<?php }?>
					</select>
				</div>

				<div class="form-group">
					<label for="users" class="col-sm-2 control-label">Delegado</label>
					<select class="form-control" id="users" name="users" required>
					<?php
$result_users = $users->consultar_visibles();
while ($users = $result_users->fetch_array(MYSQLI_ASSOC)) {?>
						<option value="<?php echo $users['id']; ?>"><?php echo $users['nombre']; ?></option>
					<?php }?>
					</select>
				</div>

				<div class="form-group">
					<label for="nombre" class="col-sm-2 control-label">Curso</label>

					<select class="form-control" id="curso" name="curso" required>
						<?php $result_cursos = $cursos->consultar_visibles();
while ($curso = $result_cursos->fetch_array(MYSQLI_ASSOC)) {?>
							<option value="<?php echo $curso['cursoid']; ?>"><?php echo $curso['nombre_cursos']; ?></option>
					<?php }?>
					</select>
				</div>
				<div class="well">
					<p>Archivo separado por comas en formato:</p>
					<table class="table table-bordered">
						<tr>
							<th>CUI</th>
							<th>NOMBRE</th>
							<th>PUESTO</th>
							<th>ÁREA</th>
						</tr>
					</table>
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
	<div class="container" id="gridParticipantes">
		<button type="button" id="btnLoadCSV" class="btn btn-lg btn-success"><span class="glyphicon glyphicon-cloud-upload"></span> Cargar CSV</button>
		<br /><br />
		<div class="panel panel-success">
			<div class="panel-heading">
				<h3 class="panel-title">Participantes Registrados</h3>
			</div>

		<div class="panel-body">
			<div class="row">
				<form action="<?php $_SERVER['PHP_SELF'];?>" method="POST">
				 <div class="col-sm-4">
					<input type="text" placeholder="Nombre" class="form-control" name="campo" id="campo">
				 </div>
				 <div class="col-sm-2">
					<input type="submit" name="enviar" id="enviar" value="Buscar" class="btn btn-success">
				 </div>
				 <div class="col-sm-6"></div>
				</form>
			</div>
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>ID</th>
								<th>Ingenio</th>
								<th>CUI</th>
								<th>Nombre</th>
								<th>Puesto</th>
								<th>Área</th>
								<th>Acciones</th>
							</tr>
						</thead>
						<tbody>
							<?php if ($resultado != false) {
    while ($row = $resultado->fetch_array(MYSQLI_ASSOC)) {?>
							<tr>
								<td><?php echo $row['idparticipante']; ?></td>
								<td><?php echo $row['nombre_ingenios']; ?></td>
								<td><?php echo $row['cui_participantes']; ?></td>
								<td><?php echo $row['nombre_participantes']; ?></td>
								<td><?php echo $row['puesto_participantes']; ?></td>
								<td><?php echo $row['area_participantes']; ?></td>
								<td><a href="modificar_participantes.php?id=<?php echo $row['idparticipante']; ?>" ><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;<a href="#" data-href="eliminar_participante.php?id=<?php echo $row['idparticipante']; ?>" data-toggle="modal" data-target="#confirm-delete"><span class="glyphicon glyphicon-trash"></span></a>
								</td>
							</tr>
							<?php }
} else {
    echo "No se encontraron resultados";
}
?>
						</tbody>
					</table>
			</div>
		</div>
	</div>
	<!-- Modal -->
	<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">

				<div class="model-header">
					<button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Eliminar Registro</h4>
				</div>

				<div class="modal-body">
					¿Desea eliminar este registro?
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					<a class="btn btn-danger btn-ok">Eliminar</a>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$('#confirm-delete').on('show.bs.modal',function(e){
			$(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));

			$('.debug-url').html('Delete URL: <strong> '+ $(this).find('.btn-ok').attr('href')+'</strong>');
		});
	</script>
</body>
</html>