<?php

require_once __DIR__ . '/../conexion.php';

function guardarPhSuelo(array $data, array $metadata = []): array
{
    $conn = (new Conexion())->conectar();

    $stmt = $conn->prepare(
        "INSERT INTO suelo_ph
        (ph, temperatura)
        VALUES (?, ?)"
    );

    $ok = $stmt->execute([
        $data['ph'] ?? 0,
        $data['temperatura'] ?? 0,
    ]);
    $id = $ok ? (int) $conn->lastInsertId() : false;

    if ($id !== false) {
        return ['exito' => true, 'mensaje' => 'pH guardado correctamente.', 'id' => $id];
    }

    return ['exito' => false, 'mensaje' => 'Error al guardar pH.'];
}
