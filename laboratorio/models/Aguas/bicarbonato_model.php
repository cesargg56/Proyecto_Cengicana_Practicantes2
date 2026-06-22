<?php
require_once __DIR__ . '/../conexion.php';

function guardarBicarbonato($ml_acl, $ml_carbonatos, $normalidad_h2oso4, $volumen_muestra, 
$bicarbonatos_mgl){
    $conn = (new Conexion())->conectar();

    $stmt = $conn->prepare(
        "INSERT INTO agua_bicarbonatos
            (ml_hcl, ml_carbonatos, normalidad_h2so4, volumen_muestra, bicarbonatos_mgl)
         VALUES (?, ?, ?, ?, ?)"
    );

    if ($stmt->execute([$ml_acl, $ml_carbonatos, $normalidad_h2oso4, $volumen_muestra, $bicarbonatos_mgl])) {
        return ["exito" => true, "mensaje" => "Cloruros guardados correctamente.", "id" => (int) $conn->lastInsertId()];
    } else {
        return ["exito" => false, "mensaje" => "Error al guardar."];
    }
}
?>
