<?php
require_once __DIR__ . '/../conexion.php';

function guardarPMP($peso_caja, $peso_caja_mhumeda, $peso_caja_mseca, $psh, $pss, $porcentaje_pmp, $no_caja, $control, array $metadata = [])
{
    $conn = (new Conexion())->conectar();

    $stmt = $conn->prepare(
        "INSERT INTO suelo_pmp
        (peso_caja, peso_caja_mhumeda, peso_caja_mseca, psh, pss, porcentaje_pmp, no_caja, control)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $ok = $stmt->execute([
        $peso_caja,
        $peso_caja_mhumeda,
        $peso_caja_mseca,
        $psh,
        $pss,
        $porcentaje_pmp,
        $no_caja,
        $control,
    ]);
    $id = $ok ? (int) $conn->lastInsertId() : false;

    if ($id !== false) {
        return ['exito' => true, 'mensaje' => 'Punto de Marchitez Permanente guardado correctamente.', 'id' => $id];
    }

    return ['exito' => false, 'mensaje' => 'Error al guardar.'];
}
?>
