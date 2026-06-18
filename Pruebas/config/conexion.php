<?php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

class Conexion {
    private static $envLoaded = false;
    private static $loginEnv = null;

    private static function cargarEnv() {
        if (self::$envLoaded) {
            return;
        }

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->safeLoad();
        self::$envLoaded = true;
    }

    private static function env($key, $default = null) {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }

    private static function loginEnv($key, $default = null) {
        if (self::$loginEnv === null) {
            self::$loginEnv = [];
            $loginEnvPath = __DIR__ . '/../../login/.env';

            if (is_file($loginEnvPath)) {
                foreach (file($loginEnvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                    $line = trim($line);

                    if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
                        continue;
                    }

                    [$envKey, $envValue] = explode('=', $line, 2);
                    self::$loginEnv[trim($envKey)] = trim($envValue, " \t\n\r\0\x0B\"'");
                }
            }
        }

        return self::$loginEnv[$key] ?? $default;
    }

    public static function conectar() {
        self::cargarEnv();

        $host = self::env('DB_HOST', self::loginEnv('DB_HOST'));
        $port = self::env('DB_PORT', self::loginEnv('DB_PORT', 3306));
        $db   = self::env('DB_NAME', 'Pruebas');
        $user = self::env('DB_USER', self::loginEnv('DB_USER'));
        $pass = self::env('DB_PASS', self::env('DB_PASSWORD', self::loginEnv('DB_PASS', self::loginEnv('DB_PASSWORD', ''))));

        if (!$host || !$db || !$user) {
            die("Error: faltan variables de conexion para la base principal de Pruebas");
        }

        return new PDO(
            "mysql:host=$host;port=$port;dbname=$db;charset=utf8",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }

    public static function conectarUsuariosMenu() {
        self::cargarEnv();

        $host = self::env('DB_MENU_HOST', self::loginEnv('DB_HOST'));
        $port = self::env('DB_MENU_PORT', self::loginEnv('DB_PORT', 3306));
        $db   = self::env('DB_MENU_NAME', self::loginEnv('DB_NAME', 'usuarios_menu'));
        $user = self::env('DB_MENU_USER', self::loginEnv('DB_USER'));
        $pass = self::env('DB_MENU_PASS', self::loginEnv('DB_PASS', self::loginEnv('DB_PASSWORD', '')));

        if (!$host || !$db || !$user) {
            die("Error: faltan variables de conexion para usuarios/menu en Pruebas/.env");
        }

        return new PDO(
            "mysql:host=$host;port=$port;dbname=$db;charset=utf8",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }
}
