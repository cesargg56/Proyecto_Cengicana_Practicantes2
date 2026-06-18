<?php
	error_reporting(E_ALL);
	require_once("class.Database.php");
	require_once("conexion.php");
	$mysqli=conectar();
	
/**
 * Clase para manejo de los participantes
 *
 * @author Monica Galiego
 * @version 1.0
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('Este archivo fue generado para PHP 5 o superior');
}


//implementación de la clase areas

class participantes{
	
	//constructor
	public function __construct()
	{
	}
	
	public function consultar_todos(){
		$db = Database::getInstancia();
		$mysqli = $db->getConnection();
		$sql="SELECT p.id idparticipante,
				  i.nombre_ingenios,
				  p.cui_participantes,
				  p.nombre_participantes,
				  p.puesto_participantes,
				  p.area_participantes
				FROM cengi_cursos.participantes p
				INNER JOIN cengi_cursos.ingenios i ON (p.ingenio_id=i.id) ORDER BY nombre_participantes";
		$resultado =$db->ejecutar_idu($sql)or die ('error en la seleccion de base de datos.');
		return $resultado;
	}
	//consulta las áreas existentes en la DB
	public function consultar_visibles(){
		$db = Database::getInstancia();
		$mysqli = $db->getConnection();
		$sql="SELECT p.id idparticipante,
				  i.nombre_ingenios,
				  p.cui_participantes,
				  p.nombre_participantes,
				  p.puesto_participantes,
				  p.area_participantes
				FROM cengi_cursos.participantes p
				INNER JOIN cengi_cursos.ingenios i ON (p.ingenio_id=i.id) WHERE estado_participantes=1 ORDER BY nombre_participantes";
		$result=$db->ejecutar_idu($sql);
		return $result;
	}

	//consulta las áreas existentes en la DB
	public function getParticipantesByNombre($nombre){
		$db = Database::getInstancia();
		$mysqli = $db->getConnection();
	
		$sql="SELECT p.id idparticipante,
				  i.nombre_ingenios,
				  p.cui_participantes,
				  p.nombre_participantes,
				  p.puesto_participantes,
				  p.area_participantes
				FROM cengi_cursos.participantes p
				INNER JOIN cengi_cursos.ingenios i ON (p.ingenio_id=i.id) WHERE p.nombre_participantes LIKE '%$nombre%' ORDER BY nombre_participantes";
		$result=$db->ejecutar_idu($sql);
		//print_r($result);
		return $result;
	}
	//Insertar una nueva área en la base de datos
/*	public function agregar($apellido,$nombre,$direccion,$telefono,$movil,$correo,$responsable){
		$apellido=AntiHack($apellido);
		$nombre=AntiHack($nombre);
		$direccion=AntiHack($direccion);
		$telefono=AntiHack($telefono);
		$movil=AntiHack($movil);
		$correo=AntiHack($correo);
		$responsable=AntiHack($responsable);
		// Carné
		$anio=date('Y');
		$carne="AFC".$anio;
		$query_carne="UPDATE tbl_alumnos SET carne = concat(carne,LAST_INSERT_ID(id_alumno)) where id_alumno=LAST_INSERT_ID();";
		$estado=1;	//almacena la variable saneada de visible
		
		$conector=new DBManager;
		if($conector->conectar()==true){
			if($correo!=""){
				$existe=mysql_query("SELECT * FROM tbl_alumnos WHERE correo='$correo'");
				if (mysql_num_rows ($existe) != 0)
				{
					echo "El correo ya está registrado.";
					return false;
				}
				else {
						$query="INSERT INTO tbl_alumnos (carne,apellido,nombre,direccion,telefono,movil,correo,responsable,estado) VALUES ('$carne','$apellido','$nombre','$direccion','$telefono','$movil','$correo','$responsable',$estado);";
						$result=mysql_query($query);
						if (!$result)
							return false;
						else $result=mysql_query($query_carne);
			
						if (!$result) return false;
						else return true;
					}
				}
			else {
				$query="INSERT INTO tbl_alumnos (carne,apellido,nombre,direccion,telefono,movil,correo,responsable,estado) VALUES ('$carne','$apellido','$nombre','$direccion','$telefono','$movil','$correo','$responsable',$estado);";
						$result=mysql_query($query);
						if (!$result)
							return false;
						else $result=mysql_query($query_carne);
			
						if (!$result) return false;
						else {
							echo 'Le recomendamos ingresar un correo electrónico';
							return true;
						}
			}
			}
	}

	//Funcion para actualizar
	public function actualizar($id, $codigo, $desc, $visible){
		$id=AntiHack($id); //almacena la variable saneada de codigo de área
		$codig=AntiHack($codigo); //almacena la variable saneada de codigo de área
		$descrip=AntiHack($desc);	//almacena la variable saneada de descripción
		$versino=AntiHack($visible);	//almacena la variable saneada de visible
		$conector=new DBManager;
		if($conector->conectar()==true){
			$query="UPDATE tbl_grl_areas SET codigo='$codig', descripcion='$desc', visible='$versino' WHERE id_area=".$id.";";
			$result=mysql_query($query);
			if (!$result)
				return false;
			else
				return true;
		}
		}
	
	//Función para eliminar un área
	public function eliminar($id){
		$id=AntiHack($id);
		$conector=new DBManager;
		if($conector->conectar()==true){
			$qry_delete="DELETE FROM tbl_grl_areas WHERE tbl_grl_areas.id_area = ".$id." LIMIT 1;";
			$eliminar=mysql_query($qry_delete);
			if(!$eliminar)
				return false;
			else return true;
		}
	}*/
}
?>
