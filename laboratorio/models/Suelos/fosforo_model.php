<?php

require_once __DIR__ . '/../conexion.php';
$conexion = new Conexion();
$conn = $conexion->conectar();

function guardarFosforo($abs_blanco, $absorbancia, $ppm_sol, $ppm_p, $control, array $metadata = [])
{
    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO suelo_fosforo
        (blanco, absorbancia, ppm_solucion, ppm_suelo, control)
        VALUES (?, ?, ?, ?, ?)"
    );

    $ok = $stmt->execute([$abs_blanco, $absorbancia, $ppm_sol, $ppm_p, $control]);
    $id = $ok ? (int) $conn->lastInsertId() : false;

    if ($id !== false) {
        return [
            'exito' => true,
            'mensaje' => 'Fosforo guardado correctamente.',
            'id' => $id,
        ];
    }

    return [
        'exito' => false,
        'mensaje' => 'Error al guardar.',
    ];
}

function guardarCurvaFosforo($punto_curva, $absorbancia)
{
    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO curva_fosforo
        (punto_curva, absorbancia)
        VALUES (?, ?)"
    );

    if ($stmt->execute([$punto_curva, $absorbancia])) {
        return (int) $conn->lastInsertId();
    }

    return false;
}

function relacionarFosforoCurva($id_fosforo, $id_curva)
{
    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO suelo_fosforo_curva
        (id_fosforo, id_curva_fosforo)
        VALUES (?, ?)"
    );

    return $stmt->execute([$id_fosforo, $id_curva]);
}

function obtenerCurvaFosforo($id_fosforo)
{
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

function obtenerHistorialFosforo()
{
    global $conn;

    $sql = "
        SELECT
            id,
            blanco AS abs_blanco,
            absorbancia,
            ppm_solucion AS ppm_sol,
            ppm_suelo AS ppm_p,
            control
        FROM suelo_fosforo
        ORDER BY id ASC
    ";

    $resultado = $conn->query($sql);

    return $resultado->fetchAll(PDO::FETCH_ASSOC);
}
?>
