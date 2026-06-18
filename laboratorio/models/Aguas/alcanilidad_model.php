<?php
require_once __DIR__ . '/../conexion.php';

function guardarAlcanilidad($ml_h2oso4, $normalidad_h2oso4, $vol_muestra, $alcanilidad_mgl){
    $conn = (new Conexion())->conectar();

    $stmt = $conn->prepare(
        "INSERT INTO agua_alcalinidad
            (ml_h2so4, normalidad_h2so4, vol_muestra, alcalinidad_mgl)
         VALUES (?, ?, ?, ?)"
    );

    if ($stmt->execute([$ml_h2oso4, $normalidad_h2oso4, $vol_muestra, $alcanilidad_mgl])) {
        return ["exito" => true, "mensaje" => "Alcalinidad guardada correctamente."];
    } else {
        return ["exito" => false, "mensaje" => "Error al guardar."];
    }
}
?>
