<?php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

class Conexion {

    private static $pdo = null;

    public static function conectar() {

        if (self::$pdo !== null) {
            return self::$pdo;
        }

        try {
            // Cargar .env solo una vez
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();

            $host = $_ENV['DB_HOST'] ?? null;
            $port = $_ENV['DB_PORT'] ?? 3306;
            $db   = $_ENV['DB_NAME'] ?? null;
            $user = $_ENV['DB_USER'] ?? null;
            $pass = $_ENV['DB_PASS'] ?? '';

            if (!$host || !$db || !$user) {
                die("Error: variables de entorno incompletas");
            }

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            self::$pdo = new PDO(
                "mysql:host=$host;port=$port;dbname=$db;charset=utf8",
                $user,
                $pass,
                $options
            );

            return self::$pdo;

        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}