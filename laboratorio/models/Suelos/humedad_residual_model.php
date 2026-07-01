<?php

require_once __DIR__ . '/../conexion.php';

function guardarHumedadResidualSuelo(array $data, array $metadata = []): array
{
    $conn = (new Conexion())->conectar();

    $stmt = $conn->prepare("
  INSERT INTO suelo_humedad_residual
(
    id_formulario,
    no_lab,
    control,
    peso_caja,
    peso_muestra_humeda,
    peso_caja_muestra_humeda,
    peso_caja_muestra_seca,
    humedad_residual
)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $ok = $stmt->execute([
        $metadata['id_formulario'] ?? null,
        $metadata['no_lab'] ?? null,
    $data['Control'] ?? 0,

        $data['PesoCaja'] ?? 0,
        $data['PesoMuestraHumedo'] ?? 0,
        $data['PesoCajaMHumeda'] ?? 0,
        $data['PesoCajaMseca'] ?? 0,
        $data['PorHGrav'] ?? 0,
    ]);

    $id = $ok ? (int)$conn->lastInsertId() : false;

    if ($id !== false) {
        return [
            'exito' => true,
            'mensaje' => 'Humedad residual guardada correctamente.',
            'id' => $id
        ];
    }

    return [
        'exito' => false,
        'mensaje' => 'Error al guardar humedad residual.'
    ];
}