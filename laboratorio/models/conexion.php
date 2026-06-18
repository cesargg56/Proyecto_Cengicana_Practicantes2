<?php
class Conexion {
    private static $pdo = null;

    public static function conectar() {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        try {
           $conn = new PDO(
    "mysql:host=" . getenv('DB_HOST') .
    ";port=" . getenv('DB_PORT') .
    ";dbname=" . getenv('DB_NAME') .
    ";charset=utf8mb4",
    getenv('DB_USER'),
    getenv('DB_PASSWORD'),
    [
        PDO::ATTR_TIMEOUT => 10,
    ]
);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            self::$pdo = $conn;

            return self::$pdo;
        } catch (PDOException $e) {
            throw new RuntimeException("Error de conexión: " . $e->getMessage());
        }
    }
}

$conexion = Conexion::conectar();
?>
