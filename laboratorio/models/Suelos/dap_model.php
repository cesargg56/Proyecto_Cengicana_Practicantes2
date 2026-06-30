<?php

require_once __DIR__ . '/../conexion.php';

function guardarDapSuelo(array $data, array $metadata = []): array
{
    $conn = (new Conexion())->conectar();

    $stmt = $conn->prepare(
        "INSERT INTO suelo_dap
        (peso_caja, peso_muestra_seca, volumen_final, peso_suelo_seco, densidad)
        VALUES (?, ?, ?, ?, ?)"
    );

    $ok = $stmt->execute([
        $data['peso_caja'] ?? 0,
        $data['peso_muestra_seca'] ?? 0,
        $data['volumen_final'] ?? 0,
        $data['peso_suelo_seco'] ?? 0,
        $data['densidad'] ?? 0,
    ]);
    $id = $ok ? (int) $conn->lastInsertId() : false;

    if ($id !== false) {
        return ['exito' => true, 'mensaje' => 'DAP guardado correctamente.', 'id' => $id];
    }

    return ['exito' => false, 'mensaje' => 'Error al guardar DAP.'];
}
