<?php
//require c'conexion.php
require_once "conexion.php";
require_once "menu.php";
$mysqli = conectar();
//include menu
//include('menu.php');
$where = "";
if (!empty($_POST)) {
    $valor = $_POST['campo'];
    if (!empty($valor)) {
        $where = "WHERE u.nombre LIKE '%$valor%' ";
    }
}
$sql = "SELECT
  u.id idusuario,
  u.nombre,
  u.email,
  i.id idingenio,
  i.nombre_ingenios,
  u.rol
FROM cengi_cursos.users u
INNER JOIN cengi_cursos.ingenios i ON (u.ingenio_id=i.id) $where";

$resultado = mysqli_query($mysqli, $sql) or die('error en la seleccion de base de datos.');
?>

<html lang="es">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css">
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<meta charset="utf-8">
</head>

<body>
	<?php menu_render();?>
	<div class="container">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h3 class="panel-title"> Usuarios Registrados</h3>
			</div>
		<div class="panel-body">
			<a href="agregar_usuarios.php" class="btn btn-success">Nuevo Registro</a>
			<form action="<?php $_SERVER['PHP_SELF'];?>" method="POST">
				<strong>Nombre: </strong><input type="text" class="" name="campo" id="campo">
				<input type="submit" name="enviar" id="enviar" value="Buscar" class="btn btn-info">
			</form>
		<br>
			<table class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th>ID</th>
						<th>Nombre</th>
						<th>Email</th>
						<th>Ingenio</th>
						<th>Rol</th>
						<!--<th>Password</th>-->
						<th>Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php while ($row = $resultado->fetch_array(MYSQLI_ASSOC)) {?>
					<tr>
						<td><?php echo $row['idusuario']; ?></td>
						<td><?php echo $row['nombre']; ?></td>
						<td><?php echo $row['email']; ?></td>
						<td><?php echo $row['nombre_ingenios']; ?></td>
						<td><?php echo $row['rol']; ?></td>
						<!--<td><?php echo $row['password']; ?></td>-->
						<td><a href="modificar_usuarios.php?id=<?php echo $row['idusuario']; ?>"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;<a href="#" data-href="eliminar_usuarios.php?idusuarios=<?php echo $row['idusuario']; ?>" data-toggle="modal" data-target="#confirm-delete"><span class="glyphicon glyphicon-trash"></span></a></td>
					</tr>
					<?php }?>
				</tbody>
			</table>
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