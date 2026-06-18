<?php

require_once __DIR__ . '/../conexion.php';
$conexion = new Conexion();
$conn = $conexion->conectar();

// GUARDAR ANÁLISIS DE AZUFRE
function guardarAzufre($abs_blanco, $absorbancia, $ppm_so4, $control) {

    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO suelo_azufre
        (abs_blanco, absorbancia, ppm_so4, control)
        VALUES (?, ?, ?, ?)"
    );

    if ($stmt->execute([$abs_blanco, $absorbancia, $ppm_so4, $control])) {

        return [
            "exito" => true,
            "mensaje" => "Azufre guardado correctamente.",
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
function guardarCurvaAzufre($punto_curva, $absorbancia) {

    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO curva_azufre
        (punto_curva, absorbancia)
        VALUES (?, ?)"
    );

    if ($stmt->execute([$punto_curva, $absorbancia])) {

        return (int) $conn->lastInsertId();

    } else {

        return false;
    }
}
// RELACIONAR Azufre ↔ CURVA
function relacionarAzufreCurva($id_azufre, $id_curva) {

    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO suelo_azufre_curva
        (id_azufre, id_curva_azufre)
        VALUES (?, ?)"
    );

    return $stmt->execute([$id_azufre, $id_curva]);
}

function obtenerCurvaAzufre($id_azufre) {

    global $conn;

    $sql = "
        SELECT
            ca.punto_curva,
            ca.absorbancia
        FROM curva_azufre ca

        INNER JOIN suelo_azufre_curva ac
            ON ca.id_curva = ac.id_curva_azufre

        WHERE ac.id_azufre = ?
    ";

    $stmt = $conn->prepare($sql);

    $stmt->execute([$id_azufre]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerHistorialAzufre() {

    global $conn;

    $sql = "
        SELECT
            id,
            abs_blanco,
            absorbancia,
            ppm_so4,
            control
        FROM suelo_azufre

        ORDER BY id DESC
    ";

    $resultado = $conn->query($sql);

    return $resultado->fetchAll(PDO::FETCH_ASSOC);
}
?>
