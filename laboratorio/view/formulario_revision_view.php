<?php
require_once __DIR__ . '/../includes/auth.php';

function eRevision($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function fechaRevision($fecha)
{
    if (!$fecha) {
        return '-';
    }

    $timestamp = strtotime($fecha);
    return $timestamp ? date('d/m/Y', $timestamp) : $fecha;
}

function inputTypeRevision($value)
{
    if (is_numeric($value)) {
        return 'number';
    }

    return 'text';
}

function labelRevision($value)
{
    $value = str_replace('_', ' ', (string) $value);
    return ucwords($value);
}

function columnaValorRevision(array $fila, array $nombres, $default = '')
{
    foreach ($nombres as $nombre) {
        if (array_key_exists($nombre, $fila) && $fila[$nombre] !== null && $fila[$nombre] !== '') {
            return $fila[$nombre];
        }
    }

    return $default;
}

function columnasAnalisisRevision(array $tabla): array
{
    $pk = $tabla['primary_key'] ?? null;
    $ocultas = [
        $pk,
        'id_formulario',
        'id_encabezado',
        'numero_laboratorio',
        'numero_muestra',
        'no_lab',
        'lote',
        'codigo_lote',
    ];

    $columnas = [];
    foreach ($tabla['columnas'] as $columna) {
        $nombre = (string) ($columna['Field'] ?? '');
        if ($nombre === '' || in_array($nombre, $ocultas, true)) {
            continue;
        }
        $columnas[] = $nombre;
    }

    return $columnas;
}

function revisionValorPrimerCoincidente(array $fila, array $nombres, $default = '-'): string
{
    foreach ($nombres as $nombre) {
        if (array_key_exists($nombre, $fila) && $fila[$nombre] !== null && $fila[$nombre] !== '') {
            return (string) $fila[$nombre];
        }
    }

    return (string) $default;
}

function revisionNumeroLaboratorio(array $formulario): string
{
    foreach (($formulario['tablas'] ?? []) as $tabla) {
        foreach (($tabla['filas'] ?? []) as $fila) {
            $valor = revisionValorPrimerCoincidente($fila, ['numero_laboratorio', 'no_lab', 'numero_muestra'], '');
            if ($valor !== '') {
                return $valor;
            }
        }
    }

    return '-';
}

function revisionPrincipalCampoOrden(string $nombre): int
{
    $nombre = strtolower($nombre);
    $prioridades = [
        'resultado' => 0,
        'resultado_final' => 0,
        'valor_final' => 0,
        'promedio' => 1,
        'media' => 1,
        'ppm' => 2,
        'ph' => 2,
        'brix' => 2,
        'pol' => 2,
        'porcentaje' => 2,
        'conductividad' => 2,
        'ce' => 2,
        'densidad' => 2,
        'cloruros' => 2,
        'calcio' => 2,
        'magnesio' => 2,
        'sodio' => 2,
        'potasio' => 2,
        'fosforo' => 2,
        'nitrógeno' => 2,
        'nitrogeno' => 2,
        'boro' => 2,
        'ras' => 2,
        'tds' => 2,
        'salinidad' => 2,
    ];

    foreach ($prioridades as $patron => $peso) {
        if (strpos($nombre, $patron) !== false) {
            return $peso;
        }
    }

    return 9;
}

function revisionResultadoPrincipal(array $formulario): string
{
    $candidatos = [];

    foreach (($formulario['tablas'] ?? []) as $tabla) {
        $columnas = columnasAnalisisRevision($tabla);

        foreach (($tabla['filas'] ?? []) as $fila) {
            foreach ($columnas as $columna) {
                $valor = $fila[$columna] ?? null;
                if ($valor === null || $valor === '') {
                    continue;
                }

                $candidatos[] = [
                    'peso' => revisionPrincipalCampoOrden((string) $columna),
                    'columna' => (string) $columna,
                    'valor' => is_scalar($valor) ? trim((string) $valor) : '',
                ];
            }
        }
    }

    if (!$candidatos) {
        return '-';
    }

    usort($candidatos, static function (array $a, array $b): int {
        if ($a['peso'] === $b['peso']) {
            return strcmp($a['columna'], $b['columna']);
        }

        return $a['peso'] <=> $b['peso'];
    });

    foreach ($candidatos as $candidato) {
        if ($candidato['valor'] !== '') {
            return labelRevision($candidato['columna']) . ': ' . $candidato['valor'];
        }
    }

    return '-';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revision de formulario</title>
    <link rel="stylesheet" href="../styles/formularios.css">
</head>
<body>
<div class="page-wrap">
    <a href="../controllers/consolidacion_controller.php" class="back-link">Volver a consolidacion</a>

    <h2>Revision de formulario <?= eRevision($resumenRango['codigo_lote'] ?? '-') ?></h2>

    <?php if ($mensajeRevision): ?>
        <div class="alerta exito"><?= eRevision($mensajeRevision) ?></div>
    <?php endif; ?>
    <?php if ($errorRevision): ?>
        <div class="alerta error"><?= eRevision($errorRevision) ?></div>
    <?php endif; ?>

    <?php if (!$puedeEditarRevision && !$puedeGuardarErrores): ?>
        <div class="alerta">Solo puede ver esta revision. Para editar, aprobar o marcar errores se necesitan permisos adicionales.</div>
    <?php endif; ?>

    <?php if (empty($formulariosRevision)): ?>
        <div class="alerta">Este rango aun no tiene formularios ingresados.</div>
    <?php else: ?>
        <div class="review-summary">
            <span>Tipo <?= eRevision($resumenRango['tipo_muestra'] ?? '-') ?></span>
            <span>Ingreso <?= eRevision(fechaRevision($resumenRango['fecha_ingreso'] ?? null)) ?></span>
            <span>Rango <?= eRevision($resumenRango['inicio'] ?? '-') ?> - <?= eRevision($resumenRango['fin'] ?? '-') ?></span>
            <span><?= count($formulariosRevision) ?> formulario(s)</span>
        </div>

        <form method="POST">
            <input type="hidden" name="id_rango" value="<?= (int) $idRango ?>">
            <div class="revision-workbench">
                <div class="table-shell revision-table-shell">
                    <table class="consolidacion-table revision-table">
                        <thead>
                            <tr>
                                <th>Número de laboratorio</th>
                                <th>Resultado calculado principal</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($formulariosRevision as $formulario): ?>
                                <?php
                                    $idFormulario = (int) $formulario['id_formulario'];
                                    $numeroLaboratorio = revisionNumeroLaboratorio($formulario);
                                    $resultadoPrincipal = revisionResultadoPrincipal($formulario);
                                    $estadoFormulario = $formulario['estado_nombre'] ?: 'Revisar';
                                    $panelId = 'revision-panel-' . $idFormulario;
                                ?>
                                <tr class="revision-summary-row" data-revision-row="<?= $idFormulario ?>">
                                    <td>
                                        <div class="revision-lab-cell">
                                            <strong><?= eRevision($numeroLaboratorio) ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="revision-result-cell">
                                            <?= eRevision($resultadoPrincipal) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="revision-pill"><?= eRevision($estadoFormulario) ?></span>
                                    </td>
                                    <td>
                                        <button
                                            type="button"
                                            class="btn-submit secondary revision-toggle"
                                            data-revision-toggle
                                            aria-expanded="false"
                                            aria-controls="<?= eRevision($panelId) ?>">
                                            Revisar
                                        </button>
                                    </td>
                                </tr>
                                <tr class="revision-detail-row" id="<?= eRevision($panelId) ?>" hidden>
                                    <td colspan="4">
                                        <div class="revision-panel">
                                            <div class="revision-panel-head">
                                                <div class="revision-panel-copy">
                                                    <span class="revision-panel-kicker">Formulario #<?= $idFormulario ?></span>
                                                    <h3><?= eRevision($formulario['analisis_nombre'] ?: 'Análisis') ?></h3>
                                                    <p><?= eRevision($numeroLaboratorio) ?></p>
                                                </div>
                                                <div class="revision-version-list">
                                                    <strong>Versiones guardadas</strong>
                                                    <?php foreach ($formulario['versiones'] as $version): ?>
                                                        <span>
                                                            v<?= (int) $version['version_numero'] ?>
                                                            <?= eRevision(labelRevision($version['tipo_version'])) ?>
                                                            <?= eRevision(fechaRevision($version['fecha'] ?? null)) ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>

                                            <div class="form-footer revision-footer">
                                                <div class="footer-grid">
                                                    <div class="field">
                                                        <label>Fecha analisis</label>
                                                        <input
                                                            type="date"
                                                            name="formulario[<?= $idFormulario ?>][fecha]"
                                                            value="<?= eRevision($formulario['fecha'] ?? '') ?>"
                                                            <?= $puedeEditarRevision ? '' : 'disabled' ?>>
                                                    </div>
                                                    <div class="field">
                                                        <label>Analista</label>
                                                        <input
                                                            type="text"
                                                            name="formulario[<?= $idFormulario ?>][analista]"
                                                            value="<?= eRevision($formulario['analista'] ?? '') ?>"
                                                            placeholder="Nombre del analista"
                                                            <?= $puedeEditarRevision ? '' : 'disabled' ?>>
                                                    </div>
                                                    <div class="field full">
                                                        <label>Observaciones de revision</label>
                                                        <textarea name="comentario_revision[<?= $idFormulario ?>]" placeholder="Opcional..." <?= ($puedeEditarRevision || $puedeGuardarErrores) ? '' : 'disabled' ?>></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="revision-detail-block">
                                                <div class="section-title">Datos capturados y resultados</div>
                                                <?php if (empty($formulario['tablas'])): ?>
                                                    <div class="alerta">No se encontraron datos detallados enlazados a este formulario.</div>
                                                <?php else: ?>
                                                    <?php foreach ($formulario['tablas'] as $tabla): ?>
                                                        <?php
                                                            $pk = $tabla['primary_key'] ?? null;
                                                            $editables = labFormularioColumnasEditables($tabla);
                                                            $columnasAnalisis = columnasAnalisisRevision($tabla);
                                                        ?>
                                                        <div class="revision-dataset">
                                                            <div class="lab-table-toolbar">
                                                                <div class="section-title"><?= eRevision(labelRevision($tabla['tabla'] ?? 'Datos')) ?></div>
                                                            </div>
                                                            <div class="table-wrap lab-entry-table-wrap revision-detail-table-wrap">
                                                                <table class="lab-entry-table revision-entry-table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>Lote</th>
                                                                            <th>Numero de laboratorio</th>
                                                                            <?php foreach ($columnasAnalisis as $columna): ?>
                                                                                <th><?= eRevision(labelRevision($columna)) ?></th>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach ($tabla['filas'] as $index => $fila): ?>
                                                                            <?php $idFila = $pk !== null ? ($fila[$pk] ?? $index) : $index; ?>
                                                                            <tr>
                                                                                <td><?= $index + 1 ?></td>
                                                                                <td><?= eRevision(columnaValorRevision($fila, ['lote', 'codigo_lote'], $resumenRango['codigo_lote'] ?? '-')) ?></td>
                                                                                <td><?= eRevision(columnaValorRevision($fila, ['no_lab', 'numero_laboratorio', 'numero_muestra'], '-')) ?></td>
                                                                                <?php foreach ($columnasAnalisis as $columna): ?>
                                                                                    <?php $valor = $fila[$columna] ?? ''; ?>
                                                                                    <td>
                                                                                        <?php if ($pk !== null && $puedeEditarRevision && in_array($columna, $editables, true)): ?>
                                                                                            <input
                                                                                                type="<?= inputTypeRevision($valor) ?>"
                                                                                                name="datos[<?= $idFormulario ?>][<?= eRevision($tabla['tabla']) ?>][<?= eRevision($idFila) ?>][<?= eRevision($columna) ?>]"
                                                                                                value="<?= eRevision($valor) ?>"
                                                                                                step="any">
                                                                                        <?php else: ?>
                                                                                            <?= eRevision($valor) ?>
                                                                                        <?php endif; ?>
                                                                                    </td>
                                                                                <?php endforeach; ?>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>

                                            <?php if ($puedeGuardarErrores || $puedeGuardarCorreccion || $puedeAprobarRevision): ?>
                                                <div class="revision-panel-actions">
                                                    <?php if ($puedeGuardarErrores): ?>
                                                        <button class="btn-submit secondary" type="submit" name="accion" value="marcar_error">Guardar con errores</button>
                                                    <?php endif; ?>
                                                    <?php if ($puedeGuardarCorreccion): ?>
                                                        <button class="btn-submit secondary" type="submit" name="accion" value="guardar">Guardar correccion</button>
                                                    <?php endif; ?>
                                                    <?php if ($puedeAprobarRevision): ?>
                                                        <button class="btn-submit" type="submit" name="accion" value="aprobar">Aprobar formulario</button>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>
<script>
(function () {
    const toggleButtons = Array.from(document.querySelectorAll('[data-revision-toggle]'));
    const panelRows = Array.from(document.querySelectorAll('.revision-detail-row'));

    function closeAll() {
        toggleButtons.forEach((button) => {
            button.setAttribute('aria-expanded', 'false');
            button.textContent = 'Revisar';
        });
        panelRows.forEach((panel) => {
            panel.hidden = true;
        });
    }

    toggleButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const panelId = button.getAttribute('aria-controls');
            const panel = panelId ? document.getElementById(panelId) : null;
            const isOpen = button.getAttribute('aria-expanded') === 'true';

            closeAll();

            if (!isOpen && panel) {
                panel.hidden = false;
                button.setAttribute('aria-expanded', 'true');
                button.textContent = 'Cerrar';
            }
        });
    });
})();
</script>
</body>
</html>
