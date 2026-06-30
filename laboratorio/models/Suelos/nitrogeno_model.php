<?php
require_once __DIR__ . '/../conexion.php';

function guardarNitrogeno($peso, $ml_blanco, $ml_muestra, $porcentaje_nitro, $normalidad, $x_nitrogeno, $control, array $metadata = [])
{
    $conn = (new Conexion())->conectar();

    $stmt = $conn->prepare(
        "INSERT INTO suelo_nitrogeno
        (peso, hcl_blanco, hcl_muestra, porcentaje_n, normalidad, x_nitrogeno, control)
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    $ok = $stmt->execute([
        $peso,
        $ml_blanco,
        $ml_muestra,
        $porcentaje_nitro,
        $normalidad,
        $x_nitrogeno,
        $control,
    ]);
    $id = $ok ? (int) $conn->lastInsertId() : false;

    if ($id !== false) {
        return ['exito' => true, 'mensaje' => 'Porcentaje de nitrogeno guardado correctamente.', 'id' => $id];
    }

    return ['exito' => false, 'mensaje' => 'Error al guardar.'];
}
?>
