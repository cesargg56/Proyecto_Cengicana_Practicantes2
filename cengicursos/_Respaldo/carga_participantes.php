<?php
	session_start();
	require_once("revisar_permisos.php");
	require_once("classes/class.Database.php");
	require_once("validador_archivos.php");
	$db = DataBase::getInstancia();
	$mysqli = $db->getConnection();
	error_reporting(E_ALL);
  	ini_set('display_errors','On');
  	$usuarioactivo=$_SESSION['UActivo'];
/***
 * Si el participante ya está asignado al curso actual, debe rechazarse la actualización de información * del participante
 * Si el participante ya existe en el sistema pero es asignado a un curso nuevo, se debe actualizar la * información del participante
 **/

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Cargando...</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css">
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</head>
<body>
	<?php include_once("menu.php"); ?>
	<div class="container">
<?php
	if( !ini_get('safe_mode') ){
            set_time_limit(100000);
        }
    $linea=1;
	$error=false;
	
	function file_get_contents_utf8($fn) {
		$content = file_get_contents($fn);
		 return mb_convert_encoding($content, 'UTF-8', 
          mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true)); 
//      	return($content);
	}

  function limpiar($aTexto){
		
		return($temp);
	}
	
	function quitarTildes($aTexto) {
		$temp = trim($aTexto);
	  	$temp = str_replace("Á","A",$temp);
	  	$temp = str_replace("É","E",$temp);
	  	$temp = str_replace("Í","I",$temp);
	  	$temp = str_replace("Ó","O",$temp);
	  	$temp = str_replace("Ú","U",$temp);
		return($temp);
	}
	
	function error($aTexto) {
		global $linea;
		global $error;
		if($error==false) echo("<h3>Hubo errores al procesar el archivo.</h3>");
		echo('
			<div class="alert alert-danger" role="alert">
  				<p><strong>Error</strong> en l&iacute;nea ' . $linea .  ': ' . $aTexto . '</p>
			</div>');
		$error=true;
		//exit;
	}

	function alerta($aTexto) {
		global $linea;
		global $alerta;
		if($alerta==false) echo("<h3>Hubo alertas al procesar el archivo.</h3>");
		echo('<div class="alert alert-warning" role="alert">
				<p><strong>Atención</strong> en l&iacute;nea ' . $linea .  ': ' . $aTexto . '</p>
			</div>');
		$alerta=true;
		//exit;
	}
	$colCui=0;
	$colNombre=1;
	$colPuesto=2;
	$colArea=3;
	
	$ingenioID=(int)AntiHack($_POST['ingenio']);
	$usersID=(int)AntiHack($_POST['nombre']);
	$cursoID=(int)AntiHack($_POST['curso']);
		
	if(trim($_FILES['archivo']['tmp_name'])=="")
	{
		$linea=0;
		error("No se recibió ningún archivo");
		exit();
	}

	$truncar_as="TRUNCATE TABLE asignaciones_import";
	$truncar_par="TRUNCATE TABLE participantes_import";
	$db->ejecutar_idu($truncar_as);
	$db->ejecutar_idu($truncar_par);

	$csvfile = file_get_contents_utf8($_FILES['archivo']['tmp_name']);
	$data = str_getcsv($csvfile, "\n"); //parse the rows
	//print_r($data);
	foreach ($data as $key) {
		$datos = str_getcsv($key);
		//Se salta el encabezado, y lineas vacias
		if($linea==1) { echo("Omitiendo línea de títulos<br>"); $linea++; continue;}
		if($datos[$colCui]=='') {
			error("El CUI de la línea <strong>$linea</strong> debe tener un valor.");
		}
		echo (".");
		
		$sqlGetParticipante="SELECT id usuario_id FROM cengi_cursos.participantes WHERE cui_participantes='" . addslashes($datos[$colCui])."'";
		$participanteID= $db->get_valor_query($sqlGetParticipante,'usuario_id');
		if($participanteID==""){
			$sqlInsertParticipante="INSERT INTO cengi_cursos.participantes
						            (ingenio_id,
						             cui_participantes,
						             nombre_participantes,
						             puesto_participantes,
						             area_participantes,
						             creado)
									VALUES ($ingenioID,
									        '$datos[$colCui]',
									        '".$datos[$colNombre]."',
									        '".$datos[$colPuesto]."',
									        '".$datos[$colArea]."',
									        now())";
			$db->ejecutar_idu($sqlInsertParticipante);
			$sqlGetParticipante="SELECT LAST_INSERT_ID() usuario_id";
			$participanteID= $db->get_valor_query($sqlGetParticipante,'usuario_id');
			alerta("El participante ".$datos[$colNombre]." con CUI: <strong>".$datos[$colCui]."</strong> no existía en el sistema, se agregó automáticamente.");
			//echo "part=$participanteID";
		}
		//$echo "participanteid=$participanteID";
		$sqlAsigna="INSERT INTO cengi_cursos.asignaciones_import
					(participantes_id,
		             usuarios_id,
		             cursos_id,
		             creado)
					VALUES ($participanteID,
					        $usuarioactivo,
					        $cursoID,
					        now())";
		$db->ejecutar_idu($sqlAsigna);
	}
	//Fin del foreach
	
	if($error==false) {
		echo('<div class="alert alert-success" role="alert">
					<p><span class="glyphicon glyphicon-ok"></span>
					<strong>Excelente!</strong> El archivo fu&eacute; comprobado correctamente. </p>
			  </div>');
			
		if ($db->ejecutar_idu("INSERT INTO asignaciones SELECT * FROM asignaciones_import")===false)
			error("Error al copiar a la tabla final: ");		
	}
?>
</div>
</body>
</html>