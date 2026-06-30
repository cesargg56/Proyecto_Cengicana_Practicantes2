<?php

require_once __DIR__ . '/../conexion.php';

function guardarHumedadSuelo(array $data, array $metadata = []): array
{
    $conn = (new Conexion())->conectar();

    $stmt = $conn->prepare(
        "INSERT INTO suelo_humedad
        (humedad)
        VALUES (?)"
    );

    $ok = $stmt->execute([$data['humedad'] ?? 0]);
    $id = $ok ? (int) $conn->lastInsertId() : false;

    if ($id !== false) {
        return ['exito' => true, 'mensaje' => 'Humedad guardada correctamente.', 'id' => $id];
    }

    return ['exito' => false, 'mensaje' => 'Error al guardar humedad.'];
}
