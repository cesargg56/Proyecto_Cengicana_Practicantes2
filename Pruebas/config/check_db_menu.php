<?php
require_once "conexion.php";
$conn = Conexion::conectarUsuariosMenu();
$tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "Tablas en MENU: " . implode(", ", $tables) . "\n";
