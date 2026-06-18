<?php

require_once __DIR__ . '/../conexion.php';
$conexion = new Conexion();
$conn = $conexion->conectar();

// GUARDAR ANÁLISIS DE FÓSFORO
function guardarFosforo($abs_blanco, $absorbancia, $ppm_sol, $ppm_p, $control) {

    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO suelo_fosforo
        (blanco, absorbancia, ppm_solucion, ppm_suelo)
        VALUES (?, ?, ?, ?)"
    );

    if ($stmt->execute([$abs_blanco, $absorbancia, $ppm_sol, $ppm_p])) {

        return [
            "exito" => true,
            "mensaje" => "Fósforo guardado correctamente.",
            "id" => (int) $conn->lastInsertId()
        ];

    } else {

        return [
            "exito" => false,
            "mensaje" => "Error al guardar."
        ];
    }
}

// GUARDAR PUNTO DE CURVA
function guardarCurvaFosforo($punto_curva, $absorbancia) {

    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO curva_fosforo
        (punto_curva, absorbancia)
        VALUES (?, ?)"
    );

    if ($stmt->execute([$punto_curva, $absorbancia])) {

        return (int) $conn->lastInsertId();

    } else {

        return false;
    }
}
// RELACIONAR Fósforo↔ CURVA
function relacionarFosforoCurva($id_fosforo, $id_curva) {

    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO suelo_fosforo_curva
        (id_fosforo, id_curva_fosforo)
        VALUES (?, ?)"
    );

    return $stmt->execute([$id_fosforo, $id_curva]);
}

function obtenerCurvaFosforo($id_fosforo) {

    global $conn;

    $sql = "
        SELECT
            cf.punto_curva,
            cf.absorbancia
        FROM curva_fosforo cf

        INNER JOIN suelo_fosforo_curva fcf
            ON cf.id_curva = fcf.id_curva_fosforo

        WHERE fcf.id_fosforo = ?
    ";

    $stmt = $conn->prepare($sql);

    $stmt->execute([$id_fosforo]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerHistorialFosforo() {

    global $conn;

    $sql = "
        SELECT
            id,
            blanco AS abs_blanco,
            absorbancia,
            ppm_solucion AS ppm_sol,
            ppm_suelo AS ppm_p,
            NULL AS control
        FROM suelo_fosforo

        ORDER BY id ASC
    ";

    $resultado = $conn->query($sql);

    return $resultado->fetchAll(PDO::FETCH_ASSOC);
}
?>
