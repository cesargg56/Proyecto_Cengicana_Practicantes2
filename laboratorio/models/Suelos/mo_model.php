<?php

require_once __DIR__ . '/../conexion.php';

function guardarMo(array $data, array $metadata = []): array
{
    $conn = (new Conexion())->conectar();

    $stmt = $conn->prepare(
        "INSERT INTO MO_Porcentaje
        (peso_muestra, sulfato_ferroso_consumido, porcentaje_carbono_organico, porcentaje_materia_organica, m1_dicromato, m2_dicromato, val_solucion_ferroso, normalidad_sulfato_ferroso, ml_util_sulfato_ferroso1N, dicromato_potasio, dicromato_consumido, blanco_sulfato_ferroso)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $ok = $stmt->execute([
        $data['peso_muestra'] ?? 0,
        $data['sulfato_ferroso_consumido'] ?? 0,
        $data['porcentaje_carbono_organico'] ?? 0,
        $data['porcentaje_materia_organica'] ?? 0,
        $data['m1_dicromato'] ?? 0,
        $data['m2_dicromato'] ?? 0,
        $data['val_solucion_ferroso'] ?? 0,
        $data['normalidad_sulfato_ferroso'] ?? 0,
        $data['ml_util_sulfato_ferroso1N'] ?? 0,
        $data['dicromato_potasio'] ?? 0,
        $data['dicromato_consumido'] ?? 0,
        $data['blanco_sulfato_ferroso'] ?? 0,
    ]);
    $id = $ok ? (int) $conn->lastInsertId() : false;

    if ($id !== false) {
        return ['exito' => true, 'mensaje' => 'Materia organica guardada correctamente.', 'id' => $id];
    }

    return ['exito' => false, 'mensaje' => 'Error al guardar materia organica.'];
}
