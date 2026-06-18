<?php

	session_start();
	if($_REQUEST["act"]=="logout"){
		session_destroy();
		header("Location: login.php");
		exit;
	}
?>