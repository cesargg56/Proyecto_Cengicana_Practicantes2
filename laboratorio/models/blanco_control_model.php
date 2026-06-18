<?php

require_once __DIR__ . '/conexion.php';
$conexion = new Conexion();
$conn     = $conexion->conectar();

// ══════════════════════════════════════════════════════
//  BLANCO
// ══════════════════════════════════════════════════════

function listarBlancos() {
    global $conn;
    $res = $conn->query(
        "SELECT id_blanco, id_rango, id_tipo_analisis, codigo, descripcion, valor, activo
           FROM blanco
          ORDER BY id_blanco DESC"
    );
    return $res ? $res->fetchAll(PDO::FETCH_ASSOC) : [];
}

function obtenerBlancoPorId($id) {
    global $conn;
    $stmt = $conn->prepare(
        "SELECT id_blanco, id_rango, id_tipo_analisis, codigo, descripcion, valor, activo
           FROM blanco
          WHERE id_blanco = ?
          LIMIT 1"
    );
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function guardarBlanco($id_rango, $id_tipo_analisis, $codigo, $descripcion, $valor, $activo) {
    global $conn;
    $stmt = $conn->prepare(
        "INSERT INTO blanco (id_rango, id_tipo_analisis, codigo, descripcion, valor, activo)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    return $stmt->execute([$id_rango, $id_tipo_analisis, $codigo, $descripcion, $valor, $activo])
        ? (int) $conn->lastInsertId()
        : false;
}

function actualizarBlanco($id, $id_rango, $id_tipo_analisis, $codigo, $descripcion, $valor, $activo) {
    global $conn;
    $stmt = $conn->prepare(
        "UPDATE blanco
            SET id_rango = ?, id_tipo_analisis = ?, codigo = ?, descripcion = ?, valor = ?, activo = ?
          WHERE id_blanco = ?"
    );
    return $stmt->execute([$id_rango, $id_tipo_analisis, $codigo, $descripcion, $valor, $activo, $id]);
}

function eliminarBlanco($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM blanco WHERE id_blanco = ?");
    return $stmt->execute([$id]);
}

// ══════════════════════════════════════════════════════
//  CONTROL
// ══════════════════════════════════════════════════════

function listarControles() {
    global $conn;
    $res = $conn->query(
        "SELECT id_control, id_rango, id_tipo_analisis, codigo, descripcion, valor, minimo, maximo, activo
           FROM control_laboratorio
          ORDER BY id_control DESC"
    );
    return $res ? $res->fetchAll(PDO::FETCH_ASSOC) : [];
}

function obtenerControlPorId($id) {
    global $conn;
    $stmt = $conn->prepare(
        "SELECT id_control, id_rango, id_tipo_analisis, codigo, descripcion, valor, minimo, maximo, activo
           FROM control_laboratorio
          WHERE id_control = ?
          LIMIT 1"
    );
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function guardarControl($id_rango, $id_tipo_analisis, $codigo, $descripcion, $valor, $minimo, $maximo, $activo) {
    global $conn;
    $stmt = $conn->prepare(
        "INSERT INTO control_laboratorio (id_rango, id_tipo_analisis, codigo, descripcion, valor, minimo, maximo, activo)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    return $stmt->execute([$id_rango, $id_tipo_analisis, $codigo, $descripcion, $valor, $minimo, $maximo, $activo])
        ? (int) $conn->lastInsertId()
        : false;
}

function actualizarControl($id, $id_rango, $id_tipo_analisis, $codigo, $descripcion, $valor, $minimo, $maximo, $activo) {
    global $conn;
    $stmt = $conn->prepare(
        "UPDATE control_laboratorio
            SET id_rango = ?, id_tipo_analisis = ?, codigo = ?, descripcion = ?, valor = ?, minimo = ?, maximo = ?, activo = ?
          WHERE id_control = ?"
    );
    return $stmt->execute([$id_rango, $id_tipo_analisis, $codigo, $descripcion, $valor, $minimo, $maximo, $activo, $id]);
}

function eliminarControl($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM control_laboratorio WHERE id_control = ?");
    return $stmt->execute([$id]);
}
