<?php

require_once __DIR__ . '/../conexion.php';

function guardarCic(array $data, array $metadata = []): array
{
    $conn = (new Conexion())->conectar();

    $stmt = $conn->prepare(
        "INSERT INTO suelo_macros
        (cic_blanco, cic_muestra, cic_meq, control)
        VALUES (?, ?, ?, ?)"
    );

    $ok = $stmt->execute([
        $data['cic_blanco'] ?? 0,
        $data['cic_muestra'] ?? 0,
        $data['cic_meq'] ?? 0,
        $data['control'] ?? 0,
    ]);
    $id = $ok ? (int) $conn->lastInsertId() : false;

    if ($id !== false) {
        return ['exito' => true, 'mensaje' => 'CIC guardado correctamente.', 'id' => $id];
    }

    return ['exito' => false, 'mensaje' => 'Error al guardar CIC.'];
}
