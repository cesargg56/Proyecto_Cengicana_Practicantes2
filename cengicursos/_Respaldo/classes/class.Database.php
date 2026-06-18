<?php
// ======================================================
// Clase: class.Database.php
// Funcion: Se encarga del manejo con la base de datos
// Descripcion: Tiene varias funciones muy útiles para
//                 el manejo de registros.
//
// ======================================================
function AntiHack($aTexto)
{
    // Definimos la cofificación en el HTTP header:
    $texto = trim($aTexto);
    $texto = htmlentities($texto);
    $texto = addslashes($texto);
    // Definimos la codificación empleada al convertir caracteres:
    $texto = htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');

    return $texto;
}

class Database
{
    private $_connection;
    private $_host = "mysql";
    private $_user = "root";
    private $_pass = "u}bp*H}rWD4-}Q4%";
    private $_db   = "cengi_cursos";

    // Almacenar una unica instancia
    private static $_instancia;
    // ================================================
    // Metodo para obtener instancia de base de datos
    // ================================================
    public static function getInstancia()
    {
        if (!isset(self::$_instancia)) {
            self::$_instancia = new self;
        }

        return self::$_instancia;
    }

    // ================================================
    // Constructor de la clase Base de datos
    // ================================================
    public function __construct()
    {
        $this->_connection = new mysqli($this->_host, $this->_user, $this->_pass, $this->_db);
        // Manejar error en base de datos
        if (mysqli_connect_error()) {
            trigger_error('Falla en la conexión de base de datos' . mysqli_connect_error(), E_USER_ERROR);
        }
    }

    // Metodo vacio __close para evitar duplicacion
    private function __close()
    {}

    // Metodo para obtener la conexion a la base de datos
    public function getConnection()
    {
        return $this->_connection;
    }

    // Metodo que revisa el String SQL
    private function es_string($sql)
    {
        if (!is_string($sql)) {
            trigger_error('class.Database.inc: $SQL enviado no es un string: ' . $sql);

            return false;
        }

        return true;
    }

    // ==================================================
    //     Funcion que ejecuta el SQL y retorna un ROW
    //         Esta funcion esta pensada para SQLs,
    //         que retornen unicamente UNA sola línea
    // ==================================================
    public function get_Row($sql)
    {

        if (!self::es_string($sql)) {
            exit();
        }

        $db        = Database::getInstancia();
        $mysqli    = $db->getConnection();
        $resultado = $mysqli->query($sql);
        if ($row = $resultado->fetch_assoc()) {
            return $row;
        } else {
            return [];
        }
    }

    // ==================================================
    //     Funcion que ejecuta el SQL y retorna un CURSOR
    //         Esta funcion esta pensada para SQLs,
    //         que retornen multiples lineas (1 o varias)
    // ==================================================
    public function get_Cursor($sql)
    {
        if (!self::es_string($sql)) {
            exit();
        }

        $db        = Database::getInstancia();
        $mysqli    = $db->getConnection();
        $resultado = $mysqli->query($sql);

        return $resultado; // Este resultado se puede usar así:  while ($row = $resultado->fetch_assoc()){...}
    }

    // ==================================================
    //     Funcion que ejecuta el SQL y retorna un jSon
    //     data: [{...}] con N cantidad de registros
    // ==================================================
    public function get_json_rows($sql)
    {
        if (!self::es_string($sql)) {
            exit();
        }

        $db        = Database::getInstancia();
        $mysqli    = $db->getConnection();
        $resultado = $mysqli->query($sql);
        // Si hay un error en el SQL, este es el error de MySQL
        if (!$resultado) {
            return "class.Database.class: error " . $mysqli->error;
        }

        $i = 0;
        while ($row = $resultado->fetch_assoc()) {
            $registros[$i] = $row;
            $i++;
        };

        return json_encode(['data' => $registros]);
    }

    // ==================================================
    //     Funcion que ejecuta el SQL y retorna un jSon
    //     de una sola linea. Ideal para imprimir un
    //     Query que solo retorne una linea
    // ==================================================
    public function get_json_row($sql)
    {
        if (!self::es_string($sql)) {
            exit();
        }

        $db        = Database::getInstancia();
        $mysqli    = $db->getConnection();
        $resultado = $mysqli->query($sql);
        // Si hay un error en el SQL, este es el error de MySQL
        if (!$resultado) {
            return "class.Database.class: error " . $mysqli->error;
        }
        if (!$row = $resultado->fetch_assoc()) {
            return "{}";
        }

        return json_encode($row);
    }

    // ====================================================================
    //     Funcion que ejecuta el SQL y retorna un valor
    //     Ideal para count(*), Sum, cosas que retornen una fila y una columna
    // ====================================================================
    public function get_valor_query($sql, $columna)
    {
        if (!self::es_string($sql, $columna)) {
            exit();
        }

        $db        = Database::getInstancia();
        $mysqli    = $db->getConnection();
        $resultado = $mysqli->query($sql);
        // Si hay un error en el SQL, este es el error de MySQL
        if (!$resultado) {
            return "class.Database.class: error " . $mysqli->error;
        }
        $Valor = null;
        //Trae el primer valor del arreglo
        if ($row = $resultado->fetch_assoc()) {
            // $Valor = array_values($row)[0];
            $Valor = $row[$columna];
        }

        return $Valor;
    }

    // ====================================================================
    //     Funcion que ejecuta el SQL de inserción, actualización y eliminación
    // ====================================================================
    public function ejecutar_idu($sql)
    {
        if (!self::es_string($sql)) {
            exit();
        }

        $db     = Database::getInstancia();
        $mysqli = $db->getConnection();
        if (!$resultado = $mysqli->query($sql)) {
            return "class.Database.class: error " . $mysqli->error;
        } else {
            return $resultado;
        }

        return $resultado;
    }

    // ====================================================================
    //     Funciones para encryptar y desencryptar data:
    //         crypt_blowfish_bydinvaders
    // ====================================================================
    public function crypt($aEncryptar, $digito = 7)
    {
        $set_salt = './1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $salt     = sprintf('$2a$%02d$', $digito);
        for ($i = 0; $i < 22; $i++) {
            $salt .= $set_salt[mt_rand(0, 22)];
        }

        return crypt($aEncryptar, $salt);
    }

    public function uncrypt($Evaluar, $Contra)
    {
        if (crypt($Evaluar, $Contra) == $Contra) {
            return true;
        } else {
            return false;
        }

    }
}

/*
$row = Database::get_Row("SELECT * FROM direccion where direccion_id = 2");
echo (int)$row['direccion_id']; $Direccion = new Direccion($row);
echo $Direccion->ImprimirDireccion();
echo var_export($Direccion);
$Cursor = Database::get_Cursor("SELECT * FROM direccion");
echo var_export($Cursor);
$jSon = Database::get_json_rows("SELECT * FROM direccion");
echo $jSon;
$jSon = Database::get_json_row("SELECT * FROM direccion where direccion_id = 1");
echo $jSon;
$Valor = Database::get_valor_query("SELECT ciudad_n2ombre from direccion where direccion_id= 2","ciudad_nombre");
if (is_null($Valor)){ echo "Nulo"; }else{ echo "Data: $Valor"; }
$hecho = Database::ejecutar_idu("UPDATE direccion set codigo_postal = 123 where direccion_id = 1");
echo $hecho;
if ( strlen($hecho) == 0 ){  echo "Nulo";  }else{  echo "Data: ". $hecho; }
 */
