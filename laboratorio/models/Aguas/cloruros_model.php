<?php
require_once __DIR__ . '/../conexion.php';

function guardarCloruros($ml_muestra, $ml_agno3_blanco, $ml_agno3_muestra, $normalidad_agno3, 
$cloruros_mgl){
    $conn = (new Conexion())->conectar();

    $stmt = $conn->prepare(
        "INSERT INTO agua_cloruros
            (ml_muestra, ml_agno3_blanco, ml_agno3_muestra, normalidad_agno3, cloruros_mgl)
         VALUES (?, ?, ?, ?, ?)"
    );

    if ($stmt->execute([$ml_muestra, $ml_agno3_blanco, $ml_agno3_muestra, $normalidad_agno3, $cloruros_mgl])) {
        return ["exito" => true, "mensaje" => "Cloruros guardados correctamente.", "id" => (int) $conn->lastInsertId()];
    } else {
        return ["exito" => false, "mensaje" => "Error al guardar."];
    }
}
?>
