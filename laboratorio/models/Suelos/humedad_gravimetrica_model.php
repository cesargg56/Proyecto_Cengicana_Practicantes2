<?php

require_once __DIR__ . '/../conexion.php';

function guardarHumedadGravimetrica(array $data, array $metadata = []): array
{
    $conn = (new Conexion())->conectar();

    $stmt = $conn->prepare("
        INSERT INTO suelo_humedad_gravimetrica
        (
            id_formulario,
            no_lab,
            no_caja,
            peso_caja,
            peso_caja_muestra_humeda,
            peso_caja_muestra_seca,
            peso_suelo_humedo,
            peso_suelo_seco,
            h_grav
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $ok = $stmt->execute([
        $metadata['id_formulario'] ?? null,
        $metadata['no_lab'] ?? null,

        $data['NoCaja'] ?? null,
        $data['PesoCaja'] ?? 0,
        $data['PesoCajaMHumeda'] ?? 0,
        $data['PesoCajaMseca'] ?? 0,
        $data['PesoHumedo'] ?? 0,
        $data['PesoSeco'] ?? 0,
        $data['PorHGrav'] ?? 0,
    ]);

    if ($ok) {
        return [
            'exito' => true,
            'mensaje' => 'Humedad gravimétrica guardada correctamente.',
            'id' => (int)$conn->lastInsertId()
        ];
    }

    return [
        'exito' => false,
        'mensaje' => 'Error al guardar humedad gravimétrica.'
    ];
}