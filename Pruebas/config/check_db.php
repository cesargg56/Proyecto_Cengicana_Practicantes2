<?php
require_once "conexion.php";
$conn = Conexion::conectar();
$tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "Tablas encontradas: " . implode(", ", $tables) . "\n";
