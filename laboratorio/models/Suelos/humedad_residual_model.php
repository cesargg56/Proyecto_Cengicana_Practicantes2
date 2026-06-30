<?php

require_once __DIR__ . '/../conexion.php';

function guardarHumedadResidualSuelo(array $data, array $metadata = []): array
{
    $conn = (new Conexion())->conectar();

    $stmt = $conn->prepare(
        "INSERT INTO laboratorio_humedad
        (NoCaja, PesoCaja, PesoCajaMHumeda, PesoCajaMseca, PesoSeco, PesoHumedo, PorHGrav)
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    $ok = $stmt->execute([
        $data['NoCaja'] ?? 0,
        $data['PesoCaja'] ?? 0,
        $data['PesoCajaMHumeda'] ?? 0,
        $data['PesoCajaMseca'] ?? 0,
        $data['PesoSeco'] ?? 0,
        $data['PesoHumedo'] ?? 0,
        $data['PorHGrav'] ?? 0,
    ]);
    $id = $ok ? (int) $conn->lastInsertId() : false;

    if ($id !== false) {
        return ['exito' => true, 'mensaje' => 'Humedad gravimetrica guardada correctamente.', 'id' => $id];
    }

    return ['exito' => false, 'mensaje' => 'Error al guardar humedad gravimetrica.'];
}
