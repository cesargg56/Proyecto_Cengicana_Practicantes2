<?php

require_once __DIR__ . '/../conexion.php';

$conexion = new Conexion();
$conn = $conexion->conectar();

function guardarConductividadElectrica(array $data = []): array
{
    return [
        'exito' => false,
        'mensaje' => 'Modelo pendiente para Conductividad Electrica.',
        'data' => $data,
    ];
}
