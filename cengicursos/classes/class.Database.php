<?php
require_once __DIR__ . '/../conexion.php';

function AntiHack($aTexto)
{
    $texto = trim((string) $aTexto);
    $texto = htmlentities($texto);
    $texto = addslashes($texto);
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

class Database
{
    private $_connection;
    private static $_instancia;

    public static function getInstancia()
    {
        if (!isset(self::$_instancia)) {
            self::$_instancia = new self();
        }

        return self::$_instancia;
    }

    public function __construct()
    {
        $this->_connection = conectar();
    }

    private function __clone()
    {
    }

    public function getConnection()
    {
        return $this->_connection;
    }

    private function es_string($sql)
    {
        if (!is_string($sql)) {
            trigger_error('class.Database: el SQL enviado no es un string');
            return false;
        }

        return true;
    }

   public function escape($valor)
{
    return str_replace("'", "''", (string)$valor);
}

    public function get_Row($sql)
    {
        if (!$this->es_string($sql)) {
            return [];
        }

        $resultado = $this->_connection->query($sql);
        if (!$resultado) {
            return [];
        }

        $row = $resultado->fetch(PDO::FETCH_ASSOC);
        return $row ?: [];
    }

    public function get_Cursor($sql)
    {
        if (!$this->es_string($sql)) {
            return false;
        }

        return $this->_connection->query($sql);
    }

    public function get_json_rows($sql)
    {
        if (!$this->es_string($sql)) {
            return json_encode(['data' => []]);
        }

        $resultado = $this->_connection->query($sql);
        if (!$resultado) {
            return 'class.Database: error ' . $this->_connection->errorInfo()[2];
        }

        $registros = [];
        while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $registros[] = $row;
        }

        return json_encode(['data' => $registros]);
    }

    public function get_json_row($sql)
    {
        if (!$this->es_string($sql)) {
            return '{}';
        }

        $resultado = $this->_connection->query($sql);
        if (!$resultado) {
            return 'class.Database: error ' . $this->_connection->errorInfo()[2];
        }

        $row = $resultado->fetch(PDO::FETCH_ASSOC);
        return $row ? json_encode($row) : '{}';
    }

    public function get_valor_query($sql, $columna)
    {
        if (!$this->es_string($sql) || !is_string($columna)) {
            return null;
        }

        $resultado = $this->_connection->query($sql);
        if (!$resultado) {
            return 'class.Database: error ' . $this->_connection->errorInfo()[2];
        }

        $row = $resultado->fetch(PDO::FETCH_ASSOC);
        return $row[$columna] ?? null;
    }

    public function ejecutar_idu($sql)
    {
        if (!$this->es_string($sql)) {
            return false;
        }

        $resultado = $this->_connection->query($sql);
        if (!$resultado) {
            return 'class.Database: error ' . $this->_connection->errorInfo()[2];
        }

        return $resultado;
    }

    public function crypt($aEncryptar, $digito = 7)
    {
        $set_salt = './1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $salt = sprintf('$2a$%02d$', $digito);

        for ($i = 0; $i < 22; $i++) {
            $salt .= $set_salt[mt_rand(0, strlen($set_salt) - 1)];
        }

        return crypt($aEncryptar, $salt);
    }

    public function uncrypt($Evaluar, $Contra)
    {
        return crypt($Evaluar, $Contra) === $Contra;
    }
}
?>
