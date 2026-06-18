<?php

require_once __DIR__ . '/../conexion.php';
$conexion = new Conexion();
$conn = $conexion->conectar();

// GUARDAR ANÁLISIS DE FÓSFORO
function guardarFosforo($abs_blanco, $absorbancia, $ppm_sol, $ppm_p) {

    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO agua_fosforo
        (abs_blanco, absorbancia, ppm_sol, ppm_p)
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
            "mensaje" => "Error al guardar el registro."
        ];
    }
}

// GUARDAR PUNTO DE CURVA
function guardarCurvaFosforo($punto_curva, $absorbancia) {

    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO curva_fosforo_ag
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
        "INSERT INTO agua_fosforo_curva
        (id_fosforo, id_curva_fosforo)
        VALUES (?, ?)"
    );

    return $stmt->execute([$id_fosforo, $id_curva]);
}

function obtenerCurvaFosforo($id_fosforo) {

    global $conn;

    $sql = "
        SELECT
            cag.punto_curva,
            cag.absorbancia
        FROM curva_fosforo_ag cag

        INNER JOIN agua_fosforo_curva agc
            ON cag.id_curva = agc.id_curva_fosforo

        WHERE agc.id_fosforo = ?
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
            abs_blanco,
            absorbancia,
            ppm_sol,
            ppm_p
        FROM agua_fosforo

        ORDER BY id DESC
    ";

    $resultado = $conn->query($sql);

    return $resultado ? $resultado->fetchAll(PDO::FETCH_ASSOC) : [];
}
?>
