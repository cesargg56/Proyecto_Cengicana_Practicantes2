<?php
	$errores[1]="No se cargó el archivo";
	$errores[2]="El tipo de archivo cargado es incorrecto";
	
	function mensajeError($error){
		return $errores($error);
	}
?>