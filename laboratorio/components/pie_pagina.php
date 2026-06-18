<?php
require_once __DIR__ . '/../includes/formulario_revision_helper.php';

$labFooterLoteEntrada = $_POST['lote'] ?? $_GET['lote'] ?? '';
if (is_array($labFooterLoteEntrada)) {
    $labFooterLoteEntrada = reset($labFooterLoteEntrada) ?: '';
}
$lote_actual = trim((string) $labFooterLoteEntrada);

if (!function_exists('labFooterE')) {
    function labFooterE($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('labFooterLower')) {
    function labFooterLower(string $value): string
    {
        return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
    }
}

if (!function_exists('labFooterContextoAnalisis')) {
    function labFooterContextoAnalisis(): ?array
    {
        global $labAnalysisContexto;

        if (isset($labAnalysisContexto) && is_array($labAnalysisContexto)) {
            return $labAnalysisContexto;
        }

        $script = strtolower(str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '')));

        $mapa = [
            'suelos/cc_controller.php' => [
                'tipos' => ['suelos', 'suelo'],
                'analisis' => ['Capacidad de Campo', 'Capacidad campo'],
                'label' => 'Capacidad de Campo',
            ],
            'suelos/pmp_controller.php' => [
                'tipos' => ['suelos', 'suelo'],
                'analisis' => ['Punto de Marchitez Permanente', 'Marchitez Permanente', 'PMP'],
                'label' => 'Punto de Marchitez Permanente',
            ],
            'suelos/macroscic_controller.php' => [
                'tipos' => ['suelos', 'suelo'],
                'analisis' => ['Macronutrientes y CIC', 'Macronutrientes', 'CIC'],
                'label' => 'Macronutrientes y CIC',
            ],
            'suelos/micros_controller.php' => [
                'tipos' => ['suelos', 'suelo'],
                'analisis' => ['Micro Nutrientes', 'Micronutrientes', 'Cu, Zn, Fe, Mn, K'],
                'label' => 'Micro Nutrientes de Suelos',
            ],
            'suelos/nitrogeno_controller.php' => [
                'tipos' => ['suelos', 'suelo'],
                'analisis' => ['Nitrógeno', 'Nitrogeno', 'Nitrógeno total', 'Nitrogeno total'],
                'label' => 'Nitrógeno de Suelos',
            ],
            'suelos/boro_controller.php' => [
                'tipos' => ['suelos', 'suelo'],
                'analisis' => ['Boro'],
                'label' => 'Boro de Suelos',
            ],
            'suelos/azufre_controller.php' => [
                'tipos' => ['suelos', 'suelo'],
                'analisis' => ['Azufre', 'SO4'],
                'label' => 'Azufre de Suelos',
            ],
            'suelos/fosforo_controller.php' => [
                'tipos' => ['suelos', 'suelo'],
                'analisis' => ['Fósforo', 'Fosforo', 'Fósforo disponible', 'Fosforo disponible'],
                'label' => 'Fósforo de Suelos',
            ],
            'foliares/micros_controller.php' => [
                'tipos' => ['foliares', 'foliar'],
                'analisis' => ['Micro Nutrientes', 'Micronutrientes', 'Cu, Zn, Fe, Mn, K'],
                'label' => 'Micro Nutrientes Foliares',
            ],
            'foliares/fosforo_controller.php' => [
                'tipos' => ['foliares', 'foliar'],
                'analisis' => ['Fósforo', 'Fosforo', 'Fósforo foliar', 'Fosforo foliar'],
                'label' => 'Fósforo Foliar',
            ],
            'aguas/micros_controller.php' => [
                'tipos' => ['agua', 'aguas'],
                'analisis' => ['Micro Nutrientes', 'Micronutrientes', 'Cu, Zn, Fe, Mn'],
                'label' => 'Micro Nutrientes de Aguas',
            ],
            'aguas/fosforo_controller.php' => [
                'tipos' => ['agua', 'aguas'],
                'analisis' => ['Fósforo', 'Fosforo'],
                'label' => 'Fósforo de Aguas',
            ],
            'aguas/conductividad_controller.php' => [
                'tipos' => ['agua', 'aguas'],
                'analisis' => ['Conductividad Eléctrica', 'Conductividad Electrica', 'CE'],
                'label' => 'Conductividad Eléctrica',
            ],
            'aguas/tds_controller.php' => [
                'tipos' => ['agua', 'aguas'],
                'analisis' => ['TDS', 'Sólidos totales disueltos', 'Solidos totales disueltos', 'STD'],
                'label' => 'TDS',
            ],
            'aguas/resistividad_controller.php' => [
                'tipos' => ['agua', 'aguas'],
                'analisis' => ['Resistividad'],
                'label' => 'Resistividad',
            ],
            'aguas/cloruros_controller.php' => [
                'tipos' => ['agua', 'aguas'],
                'analisis' => ['Cloruros'],
                'label' => 'Cloruros',
            ],
            'aguas/alcanilidad_controller.php' => [
                'tipos' => ['agua', 'aguas'],
                'analisis' => ['Alcalinidad', 'Alcanilidad'],
                'label' => 'Alcalinidad',
            ],
            'aguas/bicarbonato_controller.php' => [
                'tipos' => ['agua', 'aguas'],
                'analisis' => ['Bicarbonatos', 'Bicarbonato'],
                'label' => 'Bicarbonatos',
            ],
            'cana/humedad_controller.php' => [
                'tipos' => ['cañas', 'caña', 'canas', 'cana'],
                'analisis' => ['% de Humedad', 'Humedad'],
                'label' => '% de Humedad en Caña',
            ],
            'cana/brixpol_controller.php' => [
                'tipos' => ['cañas', 'caña', 'canas', 'cana'],
                'analisis' => ['Determinación de Brix y Pol', 'Determinacion de Brix y Pol', 'Brix', 'Pol'],
                'label' => 'Brix y Pol',
            ],
        ];

        foreach ($mapa as $needle => $contexto) {
            if (strpos($script, $needle) !== false) {
                return $contexto;
            }
        }

        return null;
    }
}

if (!function_exists('labFooterConexion')) {
    function labFooterConexion(): ?PDO
    {
        global $conn, $conexion;

        if ($conn instanceof PDO) {
            return $conn;
        }

        if ($conexion instanceof PDO) {
            return $conexion;
        }

        if (class_exists('Conexion')) {
            $pdo = Conexion::conectar();
            return $pdo instanceof PDO ? $pdo : null;
        }

        $conexionPath = __DIR__ . '/../models/conexion.php';
        if (file_exists($conexionPath)) {
            require_once $conexionPath;
            if (class_exists('Conexion')) {
                $pdo = Conexion::conectar();
                return $pdo instanceof PDO ? $pdo : null;
            }
        }

        return null;
    }
}

if (!function_exists('labFooterCondiciones')) {
    function labFooterCondiciones(array $contexto, array &$params): array
    {
        $tipoParts = [];
        foreach ($contexto['tipos'] as $tipo) {
            $tipoParts[] = 'LOWER(tm.nombre) = ?';
            $params[] = labFooterLower($tipo);
        }

        $analisisParts = [];
        foreach ($contexto['analisis'] as $analisis) {
            $analisisLower = labFooterLower($analisis);
            $analisisParts[] = 'LOWER(ta.nombre) = ?';
            $params[] = $analisisLower;
            $analisisParts[] = 'LOWER(ta.nombre) LIKE ?';
            $params[] = '%' . $analisisLower . '%';
        }

        return [
            'tipo' => '(' . implode(' OR ', $tipoParts) . ')',
            'analisis' => '(' . implode(' OR ', $analisisParts) . ')',
        ];
    }
}

if (!function_exists('labFooterPendienteAnalisisSql')) {
    function labFooterPendienteAnalisisSql(string $rangoAlias = 'lr', string $analisisAlias = 'ta'): string
    {
        return "NOT EXISTS (
            SELECT 1
              FROM formulario f_done
             WHERE f_done.id_rango = {$rangoAlias}.id_rango
               AND f_done.id_tipo_analisis = {$analisisAlias}.id_tipo
        )";
    }
}

if (!function_exists('labFooterAnalisisYaIngresado')) {
    function labFooterAnalisisYaIngresado(?array $contexto, string $codigoLote): bool
    {
        if (!$contexto || $codigoLote === '') {
            return false;
        }

        try {
            $pdo = labFooterConexion();
            if (!$pdo) {
                return false;
            }

            $params = [$codigoLote];
            $condiciones = labFooterCondiciones($contexto, $params);
            $stmt = $pdo->prepare("
                SELECT 1
                  FROM lote l
                  INNER JOIN solicitud s ON s.id_lote = l.id_lote
                  INNER JOIN tipo_muestra tm ON tm.id_tipo = s.id_tipo
                  INNER JOIN solicitud_analisis sa ON sa.id_solicitud = s.id_solicitud
                  INNER JOIN tipo_analisis ta ON ta.id_tipo = sa.id_tipo_analisis
                  LEFT JOIN lote_rango lr ON lr.id_lote = l.id_lote
                  INNER JOIN formulario f_done
                          ON f_done.id_rango = lr.id_rango
                         AND f_done.id_tipo_analisis = ta.id_tipo
                 WHERE l.codigo_lote = ?
                   AND {$condiciones['tipo']}
                   AND {$condiciones['analisis']}
                 LIMIT 1
            ");
            $stmt->execute($params);

            return (bool) $stmt->fetchColumn();
        } catch (Throwable $e) {
            return false;
        }
    }
}

if (!function_exists('labFooterLotesPorAnalisis')) {
    function labFooterLotesPorAnalisis(?array $contexto): array
    {
        if (!$contexto) {
            return [];
        }

        try {
            $pdo = labFooterConexion();
            if (!$pdo) {
                return [];
            }

            $params = [];
            $condiciones = labFooterCondiciones($contexto, $params);
            $stmt = $pdo->prepare("
                SELECT DISTINCT l.codigo_lote
                  FROM lote l
                  INNER JOIN solicitud s ON s.id_lote = l.id_lote
                  LEFT JOIN lote_rango lr ON lr.id_lote = l.id_lote
                  INNER JOIN tipo_muestra tm ON tm.id_tipo = s.id_tipo
                  INNER JOIN solicitud_analisis sa ON sa.id_solicitud = s.id_solicitud
                  INNER JOIN tipo_analisis ta ON ta.id_tipo = sa.id_tipo_analisis
                 WHERE l.codigo_lote IS NOT NULL
                   AND l.codigo_lote <> ''
                   AND {$condiciones['tipo']}
                   AND {$condiciones['analisis']}
                   AND " . labFooterPendienteAnalisisSql('lr', 'ta') . "
                 ORDER BY l.codigo_lote
            ");
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }
}

if (!function_exists('labFooterTodosLosLotes')) {
    function labFooterTodosLosLotes(): array
    {
        try {
            $pdo = labFooterConexion();
            if (!$pdo) {
                return [];
            }

            $stmt = $pdo->query("
                SELECT DISTINCT codigo_lote
                  FROM lote
                 WHERE codigo_lote IS NOT NULL
                   AND codigo_lote <> ''
                 ORDER BY codigo_lote
            ");

            return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }
}

if (!function_exists('labFooterMuestrasPorLote')) {
    function labFooterMuestrasPorLote(?array $contexto): array
    {
        try {
            $pdo = labFooterConexion();
            if (!$pdo) {
                return [];
            }

            $params = [];
            $joinContexto = '';
            $whereContexto = '';

            if ($contexto) {
                $condiciones = labFooterCondiciones($contexto, $params);
                $joinContexto = "
                  INNER JOIN tipo_muestra tm ON tm.id_tipo = s.id_tipo
                  INNER JOIN solicitud_analisis sa ON sa.id_solicitud = s.id_solicitud
                  INNER JOIN tipo_analisis ta ON ta.id_tipo = sa.id_tipo_analisis
                  LEFT JOIN lote_rango lr ON lr.id_lote = l.id_lote
                ";
                $whereContexto = "
                   AND {$condiciones['tipo']}
                   AND {$condiciones['analisis']}
                ";
            }

            $stmt = $pdo->prepare("
                SELECT DISTINCT l.codigo_lote, m.codigo_lab, m.numero_muestra
                  FROM lote l
                  INNER JOIN solicitud s ON s.id_lote = l.id_lote
                  {$joinContexto}
                  INNER JOIN muestra m ON m.id_solicitud = s.id_solicitud
                 WHERE l.codigo_lote IS NOT NULL
                   AND l.codigo_lote <> ''
                   AND (m.codigo_lab IS NOT NULL OR m.numero_muestra IS NOT NULL)
                   {$whereContexto}
                   " . ($contexto ? 'AND ' . labFooterPendienteAnalisisSql('lr', 'ta') : '') . "
                 ORDER BY l.codigo_lote, m.numero_muestra, m.codigo_lab
            ");
            $stmt->execute($params);

            $muestras = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $lote = trim((string) ($row['codigo_lote'] ?? ''));
                if ($lote === '') {
                    continue;
                }

                $numero = trim((string) ($row['codigo_lab'] ?? ''));
                if ($numero === '' && $row['numero_muestra'] !== null) {
                    $numero = (string) $row['numero_muestra'];
                }
                if ($numero === '') {
                    continue;
                }

                $muestras[$lote] ??= [];
                if (!in_array($numero, $muestras[$lote], true)) {
                    $muestras[$lote][] = $numero;
                }
            }

            return $muestras;
        } catch (Throwable $e) {
            return [];
        }
    }
}

if (!function_exists('labFooterDestinoFormulario')) {
    function labFooterDestinoFormulario(?array $contexto, string $codigoLote): ?array
    {
        if (!$contexto || $codigoLote === '') {
            return null;
        }

        try {
            $pdo = labFooterConexion();
            if (!$pdo) {
                return null;
            }

            $params = [$codigoLote];
            $condiciones = labFooterCondiciones($contexto, $params);
            $stmt = $pdo->prepare("
                SELECT lr.id_rango, ta.id_tipo AS id_tipo_analisis
                  FROM lote l
                  INNER JOIN solicitud s ON s.id_lote = l.id_lote
                  INNER JOIN tipo_muestra tm ON tm.id_tipo = s.id_tipo
                 INNER JOIN solicitud_analisis sa ON sa.id_solicitud = s.id_solicitud
                 INNER JOIN tipo_analisis ta ON ta.id_tipo = sa.id_tipo_analisis
                 LEFT JOIN lote_rango lr ON lr.id_lote = l.id_lote
                 WHERE l.codigo_lote = ?
                   AND {$condiciones['tipo']}
                   AND {$condiciones['analisis']}
                   AND " . labFooterPendienteAnalisisSql('lr', 'ta') . "
                 ORDER BY s.id_solicitud DESC, lr.id_rango DESC
                 LIMIT 1
            ");
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                return $row;
            }

            $fallbackParams = [];
            $fallbackCondiciones = labFooterCondiciones($contexto, $fallbackParams);
            $fallbackParams[] = $codigoLote;
            $stmt = $pdo->prepare("
                SELECT lr.id_rango, ta.id_tipo AS id_tipo_analisis
                  FROM lote l
                  LEFT JOIN lote_rango lr ON lr.id_lote = l.id_lote
                  INNER JOIN tipo_muestra tm ON {$fallbackCondiciones['tipo']}
                  INNER JOIN tipo_analisis ta
                          ON ta.id_tipo_muestra = tm.id_tipo
                         AND {$fallbackCondiciones['analisis']}
                 WHERE l.codigo_lote = ?
                   AND " . labFooterPendienteAnalisisSql('lr', 'ta') . "
                 ORDER BY lr.id_rango DESC
                 LIMIT 1
            ");
            $stmt->execute($fallbackParams);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }
}

if (!function_exists('labFooterGuardarFormularioBase')) {
    function labFooterGuardarFormularioBase(?array $contexto, string $codigoLote, string $fecha, string $analista, string $observaciones): array
    {
        if (!$contexto) {
            return ['ok' => false, 'message' => 'No se pudo identificar el análisis actual.'];
        }

        if ($codigoLote === '' || $fecha === '' || $analista === '') {
            return ['ok' => false, 'message' => 'Complete lote, fecha y analista para guardar el registro.'];
        }

        try {
            $pdo = labFooterConexion();
            if (!$pdo) {
                return ['ok' => false, 'message' => 'No se pudo conectar a la base de datos.'];
            }

            $destino = labFooterDestinoFormulario($contexto, $codigoLote);
            if (!$destino) {
                if (labFooterAnalisisYaIngresado($contexto, $codigoLote)) {
                    return ['ok' => false, 'message' => 'El lote ' . $codigoLote . ' ya tiene este analisis ingresado.'];
                }
                return ['ok' => false, 'message' => 'El lote seleccionado no corresponde a este análisis.'];
            }

            $stmt = $pdo->prepare("
                INSERT INTO formulario (id_estado, id_rango, id_tipo_analisis, fecha, analista)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                labFormularioEstadoRevisarId(),
                $destino['id_rango'] !== null ? (int) $destino['id_rango'] : null,
                (int) $destino['id_tipo_analisis'],
                $fecha,
                $analista,
            ]);

            $idFormulario = (int) $pdo->lastInsertId();
            $comentarios = [];
            if ($observaciones !== '') {
                $comentarios[] = 'Observaciones: ' . $observaciones;
            }
            $comentarioFinal = implode("\n", $comentarios);

            if ($idFormulario > 0 && $comentarioFinal !== '') {
                $historial = $pdo->prepare("
                    INSERT INTO historial_formulario (id_formulario, accion, estado_anterior, estado_nuevo, usuario, fecha, comentario)
                    VALUES (?, ?, ?, ?, ?, NOW(), ?)
                ");
                $historial->execute([
                    $idFormulario,
                    'Registro creado',
                    null,
                    null,
                    $analista,
                    $comentarioFinal,
                ]);
            }

            if ($idFormulario > 0) {
                labFormularioGuardarVersion($idFormulario, 'inicial', $analista, 'Version enviada desde el formulario base.');
            }

            return ['ok' => true, 'message' => 'Registro base del formulario guardado correctamente.'];
        } catch (Throwable $e) {
            return ['ok' => false, 'message' => 'No se pudo guardar el registro base: ' . $e->getMessage()];
        }
    }
}

if (!function_exists('labFooterLotesPosteados')) {
    function labFooterLotesPosteados(string $loteActual): array
    {
        $raw = $_POST['lote'] ?? $loteActual;
        $values = is_array($raw) ? $raw : [$raw];
        $lotes = [];

        foreach ($values as $value) {
            $lote = trim((string) $value);
            if ($lote !== '') {
                $lotes[] = $lote;
            }
        }

        return $lotes;
    }
}

if (!function_exists('labFooterGuardarFormulariosBase')) {
    function labFooterGuardarFormulariosBase(?array $contexto, array $lotes, string $fecha, string $analista, string $observaciones): array
    {
        if (!$lotes) {
            return ['ok' => false, 'message' => 'Seleccione al menos un lote para guardar el registro.'];
        }

        $guardados = 0;
        $errores = [];

        foreach ($lotes as $index => $lote) {
            $resultado = labFooterGuardarFormularioBase($contexto, $lote, $fecha, $analista, $observaciones);
            if (!empty($resultado['ok'])) {
                $guardados++;
                continue;
            }

            $errores[] = 'Fila ' . ($index + 1) . ': ' . ($resultado['message'] ?? 'No se pudo guardar.');
        }

        if ($errores) {
            return [
                'ok' => false,
                'message' => 'Se guardaron ' . $guardados . ' registro(s) base, pero hubo errores. ' . implode(' ', $errores),
            ];
        }

        return [
            'ok' => true,
            'message' => 'Registros base guardados: ' . $guardados . '.',
        ];
    }
}

$labFooterContexto = labFooterContextoAnalisis();
$labFooterLotesContexto = labFooterLotesPorAnalisis($labFooterContexto);
$labFooterLotes = $labFooterLotesContexto ?: labFooterTodosLosLotes();
$labFooterMuestras = labFooterMuestrasPorLote($labFooterContexto);
$fecha_actual_footer = trim((string) ($_POST['fecha'] ?? date('Y-m-d')));
$analista_actual = trim((string) ($_POST['analista'] ?? $_POST['tecnico'] ?? ''));
$observaciones = trim((string) ($_POST['observaciones'] ?? $observaciones ?? ''));
$labFooterGuardado = null;

if ($lote_actual !== '' && !in_array($lote_actual, $labFooterLotes, true)) {
    array_unshift($labFooterLotes, $lote_actual);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && $labFooterContexto && empty($labSkipFooterBaseSave)) {
    $analisisGuardado = !isset($resultado)
        || !is_array($resultado)
        || !array_key_exists('exito', $resultado)
        || (bool) $resultado['exito'];

    if ($analisisGuardado) {
        $labFooterGuardado = labFooterGuardarFormulariosBase(
            $labFooterContexto,
            labFooterLotesPosteados($lote_actual),
            $fecha_actual_footer,
            $analista_actual,
            $observaciones
        );
    }
}
?>
<div class="form-footer">
  <?php if ($labFooterGuardado): ?>
    <div class="alerta <?= $labFooterGuardado['ok'] ? 'exito' : 'error' ?>">
      <?= labFooterE($labFooterGuardado['message']) ?>
    </div>
  <?php endif; ?>

  <div class="footer-grid">
    <div class="field">
      <label>Fecha análisis</label>
      <input type="date" name="fecha" value="<?= labFooterE($fecha_actual_footer) ?>" required>
    </div>
    <div class="field">
      <label>Analista</label>
      <input type="text" name="analista" value="<?= labFooterE($analista_actual) ?>" placeholder="Nombre del analista" required>
    </div>
    <div class="field full">
      <label>Observaciones</label>
      <textarea name="observaciones" placeholder="Opcional..."><?= labFooterE($observaciones) ?></textarea>
    </div>
  </div>

  <script
    type="application/json"
    data-lab-table-config><?= json_encode([
      'lotes' => $labFooterLotes,
      'muestras' => $labFooterMuestras,
      'loteActual' => $lote_actual,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>

  <button type="submit" class="btn-submit">Guardar formularios en base de datos</button>
</div>
<script src="../../js/analisis_tabla.js?v=<?= (int) @filemtime(__DIR__ . '/../js/analisis_tabla.js') ?>" defer></script>
