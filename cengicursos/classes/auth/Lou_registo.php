<?php

/*
 * Lou_Auth_registo file 
 * @author Antonio Lourenco (Antlou) <aftlourenco@gmail.com>
 * version 1.0
 * 19/09/2014
 */
 
if(!empty($_POST)){

	$email = $_POST['mail'];
	$senha = md5($_POST['senha']); 
	
define("HOST", "localhost"); 	// the host you want to connect
define("USER", "root"); 		// the username database
define("PASSWORD", "mysql"); 	// the user's password database 
define("DATABASE", "test"); 	// the name of the database
define("PORT",3306);			// port to connect to the database

// if you are connected via TCP/IP rather than a Unix socket, remember to add the port number as a parameter.
$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE, PORT);
$mysqli->set_charset('utf8');
// Check connection
	if (mysqli_connect_errno()){
		echo "Failed to connect to MySQL: " . mysqli_connect_error(); exit();
	}else{
		echo 'Performed db connection<br>';
	}
	
	//criar tabela users
	$sql_table = 'CREATE TABLE IF NOT EXISTS users (
	id INT(5) NOT NULL AUTO_INCREMENT,
	password VARCHAR(128) NOT NULL,
	email VARCHAR(255) NOT NULL, 
	PRIMARY KEY(id),
	UNIQUE KEY(email) 
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';
	
	// Executar query
	if (mysqli_query($mysqli,$sql_table)){
		echo "Table users created successfully<br>";
		// utilizar declarações preparadas
		$sql_insert = $mysqli->prepare("INSERT INTO users (id, password, email) VALUES (?, ?, ?)");  
		$i = null;  
		$sql_insert->bind_param('iss',$i,$senha,$email); 
		// Executar a query preparada.
		$sql_insert->execute();
		if($sql_insert->affected_rows===1){
			echo 'Utilizador registado / User registed<br>';

			echo 'Vai ser redirecionado para a pagina Controlo de Acesso<br>
				Will be redirected to the page of Login';
			header("Refresh: 5, Lou_login.php");
			
		}else{
			echo 'ERRO.<br>';
		}
	$sql_insert->close();
	}else{		
		echo 'Error creating table: ' . mysqli_error($mysqli);
	} 
$mysqli->close();
}else{
?>
	<h1>REGISTO de UTILIZADOR / SIGN UP</h1>
		<form action="#" method="POST">
		
			<label>Endereco electronico / Email</label>
			<input type="text" name="mail" maxlength="50" >
			<br>
			<label>Senha / Password</label>
			<input type="text" name="senha" maxlength="50" >
			<br>
			<input type="submit" name="registo" value="CONFIRMO / OK">
				
		</form>
		<a href="Lou_login.php">Controlo de Acesso / Login</a>
<?php
}
?>
			

			
			
