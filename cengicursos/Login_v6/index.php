<?php

/*
 * Lou_Auth_login file 
 * @author António Lourenço (Antlou) <aftlourenco@gmail.com>
 * version 1.0
 */
	session_start();
	require_once ("../classes/auth/Lou_login.class.php");
if(!empty($_POST)){
	$mail = $_POST['mail'];
	$senha = $_POST['senha'];

	$endereco = new Lou_Auth_User($mail, $senha);
	if($endereco->isAuthUser()){
		$elRol = $_SESSION["CMenus"];
		//echo $elRol;
		if(strcmp($elRol, 'Administrador')=== 0){
			header("Location: ../index.php");
		} 
		else {
			header("Location: ../participantes.php");
		}
	}
	else {
		header("Location: index.php?error=1");
	}
	var_dump($endereco->isAuthUser());
 }else{
 ?>
<!DOCTYPE html>
<html lang="es">
<head>
	<title>Login V6</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100 p-t-85 p-b-20">
				<form class="login100-form validate-form" action="#" method="POST">
					<span class="login100-form-title p-b-70">
						CengiCursos
					</span>
					<?php
					if(isset($_GET['error'])){
									$error=$_GET['error'];
									switch ($error) {
										case 1:echo('<div class="alert alert-danger" ole="alert"><p><strong>Error</strong>Usuario o Contraseña Incorrectos</p></div>');
											break;
										case 2:echo('<div class="alert alert-danger" ole="alert"><p><strong>Error</strong>No cuenta con los permisos necesarios</p></div>');
											break;
										case 3:echo('<div class="alert alert-danger" ole="alert"><p><strong>Error</strong> No ha iniciado sesión en el sistema</p></div>');
											break;
									}
								}
					?>
					<span class="login100-form-avatar">
						<img src="../css/images/logo.png" alt="AVATAR">
					</span>

					<div class="wrap-input100 validate-input m-t-85 m-b-35" data-validate = "Enter username">
						<input class="input100" type="text" name="mail">
						<span class="focus-input100" data-placeholder="Username"></span>
					</div>

					<div class="wrap-input100 validate-input m-b-50" data-validate="Enter password">
						<input class="input100" type="password" name="senha">
						<span class="focus-input100" data-placeholder="Password"></span>
					</div>

					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Login
						</button>
					</div>

					<ul class="login-more p-t-190">
						<li class="m-b-8">
						</li>
					</ul>
				</form>
			</div>
		</div>
	</div>
	

	<div id="dropDownSelect1"></div>
	
<!--===============================================================================================-->
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>

</body>
</html>
<?php
 }
?>