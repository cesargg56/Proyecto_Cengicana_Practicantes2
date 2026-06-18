<?php

require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/../includes/formulario_revision_helper.php';

function obtenerResumenRevisionRango(int $idRango): ?array
{
    $pdo = Conexion::conectar();
    $stmt = $pdo->prepare("
        SELECT lr.id_rango,
               lr.inicio,
               lr.fin,
               l.codigo_lote,
               s.id_solicitud,
               s.fecha_ingreso,
               s.numero_muestras,
               tm.nombre AS tipo_muestra
          FROM lote_rango lr
          LEFT JOIN lote l ON l.id_lote = lr.id_lote
          LEFT JOIN solicitud s ON s.id_lote = l.id_lote
          LEFT JOIN tipo_muestra tm ON tm.id_tipo = s.id_tipo
         WHERE lr.id_rango = ?
         LIMIT 1
    ");
    $stmt->execute([$idRango]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

function obtenerFormulariosRevisionRango(int $idRango): array
{
    labFormularioEnsureSchema();
    $pdo = Conexion::conectar();
    $stmt = $pdo->prepare("
        SELECT f.*,
               ef.nombre AS estado_nombre,
               ta.nombre AS analisis_nombre
          FROM formulario f
          LEFT JOIN estado_formulario ef ON ef.id_estado = f.id_estado
          LEFT JOIN tipo_analisis ta ON ta.id_tipo = f.id_tipo_analisis
         WHERE f.id_rango = ?
         ORDER BY f.id_formulario ASC
    ");
    $stmt->execute([$idRango]);
    $formularios = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    foreach ($formularios as &$formulario) {
        $idFormulario = (int) $formulario['id_formulario'];
        labFormularioEnsureVersionInicial($idFormulario, (string) ($formulario['analista'] ?? 'Sistema'));
        $datos = labFormularioDatosActuales($idFormulario);
        $formulario['tablas'] = $datos['tablas'];
        $formulario['versiones'] = labFormularioVersiones($idFormulario);
    }
    unset($formulario);

    return $formularios;
}

function guardarRevisionFormularios(array $formulariosBase, array $datosTablas, string $usuario, string $comentario = ''): void
{
    labFormularioEnsureSchema();
    $pdo = Conexion::conectar();
    $useTransaction = !$pdo->inTransaction();

    if ($useTransaction) {
        $pdo->beginTransaction();
    }

    try {
        foreach ($formulariosBase as $idFormulario => $datosBase) {
            $idFormulario = (int) $idFormulario;
            if ($idFormulario <= 0) {
                continue;
            }

            labFormularioEnsureVersionInicial($idFormulario, $usuario);
            labFormularioGuardarVersionConErrores($idFormulario, $usuario, $comentario);
            labFormularioActualizarBase($idFormulario, is_array($datosBase) ? $datosBase : []);
            labFormularioActualizarDatos($idFormulario, $datosTablas[$idFormulario] ?? []);
            labFormularioGuardarVersion($idFormulario, 'corregida', $usuario, $comentario ?: 'Version corregida desde revision.');
            labFormularioRegistrarHistorial($idFormulario, 'Formulario corregido', 'Revisar', 'Revisar', $usuario, $comentario);
        }

        if ($useTransaction && $pdo->inTransaction()) {
            $pdo->commit();
        }
    } catch (Throwable $e) {
        if ($useTransaction && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

function marcarFormulariosRangoConErrores(int $idRango, string $usuario, string $comentario = ''): void
{
    labFormularioEnsureSchema();
    $pdo = Conexion::conectar();
    $useTransaction = !$pdo->inTransaction();

    if ($useTransaction) {
        $pdo->beginTransaction();
    }

    try {
        $stmt = $pdo->prepare("
            SELECT id_formulario
              FROM formulario
             WHERE id_rango = ?
             ORDER BY id_formulario ASC
        ");
        $stmt->execute([$idRango]);
        $formularios = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        foreach ($formularios as $formulario) {
            $idFormulario = (int) $formulario['id_formulario'];
            labFormularioEnsureVersionInicial($idFormulario, $usuario);
            labFormularioGuardarVersionConErrores($idFormulario, $usuario, $comentario);
            labFormularioRegistrarHistorial($idFormulario, 'Formulario marcado con errores', 'Revisar', 'Revisar', $usuario, $comentario);
        }

        if ($useTransaction && $pdo->inTransaction()) {
            $pdo->commit();
        }
    } catch (Throwable $e) {
        if ($useTransaction && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

function aprobarFormulariosRango(int $idRango, string $usuario, string $comentario = ''): void
{
    $formularios = obtenerFormulariosRevisionRango($idRango);

    foreach ($formularios as $formulario) {
        $idFormulario = (int) $formulario['id_formulario'];
        labFormularioEnsureVersionInicial($idFormulario, $usuario);
        labFormularioAprobar($idFormulario, $usuario, $comentario);
    }
}

?>
