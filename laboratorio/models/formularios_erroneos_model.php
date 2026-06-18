<?php

require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/../includes/formulario_revision_helper.php';

function obtenerFormulariosErroneos(): array
{
    labFormularioEnsureSchema();
    $pdo = Conexion::conectar();
    $stmt = $pdo->query("
        SELECT v.id_version,
               v.id_formulario,
               v.version_numero,
               v.tipo_version,
               v.usuario,
               v.fecha,
               v.comentario,
               f.id_rango,
               f.fecha AS fecha_actual,
               f.analista,
               ef.nombre AS estado_actual,
               ta.nombre AS analisis_nombre,
               lr.inicio,
               lr.fin,
               l.codigo_lote,
               s.id_solicitud,
               s.fecha_ingreso,
               tm.nombre AS tipo_muestra
          FROM formulario_version v
          INNER JOIN formulario f ON f.id_formulario = v.id_formulario
          LEFT JOIN estado_formulario ef ON ef.id_estado = f.id_estado
          LEFT JOIN tipo_analisis ta ON ta.id_tipo = f.id_tipo_analisis
          LEFT JOIN lote_rango lr ON lr.id_rango = f.id_rango
          LEFT JOIN lote l ON l.id_lote = lr.id_lote
          LEFT JOIN solicitud s ON s.id_lote = l.id_lote
          LEFT JOIN tipo_muestra tm ON tm.id_tipo = s.id_tipo
         WHERE v.tipo_version = 'con_errores'
         ORDER BY v.fecha DESC, v.id_version DESC
    ");

    return $stmt ? ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: []) : [];
}

function obtenerFormularioErroneoDetalle(int $idVersion): ?array
{
    labFormularioEnsureSchema();
    $pdo = Conexion::conectar();
    $stmt = $pdo->prepare("
        SELECT v.id_version,
               v.id_formulario,
               v.version_numero,
               v.tipo_version,
               v.datos_json,
               v.usuario,
               v.fecha,
               v.comentario,
               f.id_rango,
               ef.nombre AS estado_actual,
               ta.nombre AS analisis_nombre,
               lr.inicio,
               lr.fin,
               l.codigo_lote,
               s.id_solicitud,
               s.fecha_ingreso,
               tm.nombre AS tipo_muestra
          FROM formulario_version v
          INNER JOIN formulario f ON f.id_formulario = v.id_formulario
          LEFT JOIN estado_formulario ef ON ef.id_estado = f.id_estado
          LEFT JOIN tipo_analisis ta ON ta.id_tipo = f.id_tipo_analisis
          LEFT JOIN lote_rango lr ON lr.id_rango = f.id_rango
          LEFT JOIN lote l ON l.id_lote = lr.id_lote
          LEFT JOIN solicitud s ON s.id_lote = l.id_lote
          LEFT JOIN tipo_muestra tm ON tm.id_tipo = s.id_tipo
         WHERE v.id_version = ?
           AND v.tipo_version = 'con_errores'
         LIMIT 1
    ");
    $stmt->execute([$idVersion]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return null;
    }

    $datos = json_decode((string) ($row['datos_json'] ?? ''), true);
    $row['datos'] = is_array($datos) ? $datos : [];

    return $row;
}

?>
