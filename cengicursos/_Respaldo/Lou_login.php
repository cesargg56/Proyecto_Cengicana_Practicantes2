<?php

/*
 * Lou_Auth_login file 
 * @author António Lourenço (Antlou) <aftlourenco@gmail.com>
 * version 1.0
 */
	require_once ("../classes/auth/Lou_login.class.php");
	
if(!empty($_POST)){
	$mail = $_POST['mail'];
	$senha = $_POST['senha'];

	$endereco = new Lou_Auth_User($mail, $senha);
	if($endereco->isAuthUser()){
		header("Location: index.php");
	};
	
 }else{
 ?>
	<h1>CONTROL DE ACESSO / LOGIN</h1>
		<form action="#" method="POST">
			<label>Endereco electronico / Email</label>
			<input type="text" name="mail" maxlength="50">
			<br>
			<label>Senha / Password</label>
			<input type="text" name="senha" maxlength="50">
			<br>
			<input type="submit" name="registo" value="CONFIRMO / OK">	
		</form>
<?php
 }
?>