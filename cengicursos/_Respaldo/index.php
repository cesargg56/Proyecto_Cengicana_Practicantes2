<?php
require_once "conexion.php";
$mysqli = conectar();
require_once "menu.php";
?>

<html lang="es">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css">
	<link rel="stylesheet" type="text/css" href="css/proyecto.css">
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

</head>

<body>
	<?php menu_render();?>
	<div class="logo">
		<img src="css/images/logo.png">
	</div>
</body>
</html>