<?php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

class Conexion {

    public static function conectar() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $db   = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];

        return new PDO(
            "mysql:host=$host;port=$port;dbname=$db;charset=utf8",
            $user,
            $pass
        );
    }

    // 👇 ESTE ES EL QUE TE FALTA
    public static function conectarUsuariosMenu() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $host = $_ENV['DB_MENU_HOST'];
        $port = $_ENV['DB_MENU_PORT'];
        $db   = $_ENV['DB_MENU_NAME'];
        $user = $_ENV['DB_MENU_USER'];
        $pass = $_ENV['DB_MENU_PASS'];

        return new PDO(
            "mysql:host=$host;port=$port;dbname=$db;charset=utf8",
            $user,
            $pass
        );
    }
}