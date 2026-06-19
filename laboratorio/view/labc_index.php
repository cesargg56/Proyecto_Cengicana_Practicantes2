<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../conexion.php';

lab_require_module_access();

if (!lab_can_any([
    'laboratorio.labc.ver',
    'laboratorio.formularios_labc.ver',
    'laboratorio.analisis.ver',
    'laboratorio.blanco_control.ver',
    'laboratorio.consolidacion.ver',
])) {
    lab_forbidden('No tiene permisos para acceder al LABC.');
}

function labc_e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function labc_visible_analysis(array $items): array
{
    return array_values(array_filter($items, static function ($item) {
        return lab_can_analysis($item['key']);
    }));
}

function labc_card_icon(string $key): string
{
    static $icons = [
        'usuarios' => 'fa-users-gear',
        'suelos' => 'fa-mountain',
        'aguas' => 'fa-droplet',
        'foliares' => 'fa-leaf',
        'cana' => 'fa-tractor',
        'mieles' => 'fa-mug-hot',
        'suelos.textura' => 'fa-mountain',
        'suelos.humedad' => 'fa-droplet',
        'suelos.humedad_residual' => 'fa-water',
        'suelos.dap' => 'fa-layer-group',
        'suelos.cc' => 'fa-seedling',
        'suelos.pmp' => 'fa-leaf',
        'suelos.ph' => 'fa-flask',
        'suelos.cic' => 'fa-vials',
        'suelos.mo' => 'fa-microscope',
        'suelos.macroscic' => 'fa-flask-vial',
        'suelos.micros' => 'fa-vials',
        'suelos.nitrogeno' => 'fa-leaf',
        'suelos.boro' => 'fa-flask',
        'suelos.azufre' => 'fa-fire-flame-curved',
        'suelos.fosforo' => 'fa-atom',
        'aguas.macros' => 'fa-flask-vial',
        'aguas.ras' => 'fa-water',
        'aguas.boro' => 'fa-flask',
        'aguas.ph' => 'fa-droplet',
        'aguas.salinidad' => 'fa-wind',
        'aguas.dureza' => 'fa-gem',
        'aguas.carbonatos' => 'fa-vial',
        'aguas.micros' => 'fa-flask',
        'aguas.fosforo' => 'fa-atom',
        'aguas.conductividad' => 'fa-bolt',
        'aguas.tds' => 'fa-cubes',
        'aguas.resistividad' => 'fa-wave-square',
        'aguas.cloruros' => 'fa-flask',
        'aguas.alcanilidad' => 'fa-scale-balanced',
        'aguas.bicarbonato' => 'fa-vials',
        'foliares.macros' => 'fa-leaf',
        'foliares.nitrogeno' => 'fa-seedling',
        'foliares.boro' => 'fa-flask',
        'foliares.micros' => 'fa-vials',
        'foliares.fosforo' => 'fa-atom',
        'cana.peso_seco' => 'fa-scale-balanced',
        'cana.fibra' => 'fa-wheat-awn',
        'cana.humedad' => 'fa-droplet',
        'cana.brixpol' => 'fa-chart-line',
        'mieles.brix' => 'fa-chart-column',
        'blancos.control' => 'fa-vial',
        'consolidacion' => 'fa-layer-group',
    ];

    return $icons[$key] ?? 'fa-flask';
}

function labc_prepare_cards(array $items, ?string $highlightKey = null): array
{
    $cards = [];
    foreach ($items as $index => $item) {
        $key = (string) ($item['key'] ?? '');
        $label = (string) ($item['label'] ?? '');
        $cards[] = [
            'key' => $key,
            'label' => $label,
            'href' => (string) ($item['href'] ?? '#'),
            'icon' => (string) ($item['icon'] ?? labc_card_icon($key)),
            'active' => !empty($item['active']) || ($highlightKey !== null && $key === $highlightKey) || ($highlightKey === null && $index === 0),
            'ghost' => !empty($item['ghost']),
            'search' => trim($label . ' ' . ($item['search'] ?? '')),
        ];
    }

    return $cards;
}

function labc_section_href(string $area): string
{
    return '?area=' . rawurlencode($area) . '#section-' . $area;
}

function labc_history_href(string $area): ?string
{
    switch ($area) {
        case 'suelos':
        case 'aguas':
        case 'foliares':
        case 'cana':
            return 'dashboard.php?tipo_reporte=' . rawurlencode($area);
        default:
            return null;
    }
}

function getSampleTypeKey(string $nombre): string
{
    $normalized = strtolower(trim($nombre));
    $map = [
        'suelos' => 'suelos',
        'suelo' => 'suelos',
        'cañas' => 'cana',
        'caña' => 'cana',
        'cana' => 'cana',
        'mieles' => 'mieles',
        'miel' => 'mieles',
        'agua' => 'aguas',
        'aguas' => 'aguas',
        'foliares' => 'foliares',
        'foliar' => 'foliares',
    ];
    return $map[$normalized] ?? $normalized;
}

function get_analysis_info(string $sampleTypeKey, string $dbAnalysisName): ?array
{
    $norm = strtolower(trim($dbAnalysisName));
    $norm = str_replace(
        ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'Ã¡', 'Ã©', 'Ã­', 'Ã³', 'Ãº', 'Ã±', 'â‚ƒ', 'Â', 'º', '%'],
        ['a', 'e', 'i', 'o', 'u', 'n', 'a', 'e', 'i', 'o', 'u', 'n', '3', '', '', ''],
        $norm
    );
    $norm = preg_replace('/\s+/', ' ', $norm);

    $key = null;
    $label = null;
    $filename = null;

    if ($sampleTypeKey === 'suelos') {
        if (strpos($norm, 'textura') !== false) {
            $key = 'suelos.textura';
            $label = 'Textura';
            $filename = 'textura_controller.php';
        } elseif (strpos($norm, 'residual') !== false) {
            $key = 'suelos.humedad_residual';
            $label = 'Humedad residual';
            $filename = 'humedad_residual_controller.php';
        } elseif (strpos($norm, 'humedad') !== false) {
            $key = 'suelos.humedad';
            $label = 'Humedad';
            $filename = 'humedad_controller.php';
        } elseif (strpos($norm, 'dap') !== false || strpos($norm, 'densidad aparente') !== false) {
            $key = 'suelos.dap';
            $label = 'Densidad aparente (DAP)';
            $filename = 'dap_controller.php';
        } elseif (strpos($norm, 'capacidad de campo') !== false || $norm === 'cc') {
            $key = 'suelos.cc';
            $label = 'Capacidad de Campo';
            $filename = 'cc_controller.php';
        } elseif (strpos($norm, 'marchitez') !== false || $norm === 'pmp') {
            $key = 'suelos.pmp';
            $label = 'Punto de Marchitez Permanente';
            $filename = 'pmp_controller.php';
        } elseif ($norm === 'ph') {
            $key = 'suelos.ph';
            $label = 'pH';
            $filename = 'ph_controller.php';
        } elseif (strpos($norm, 'cic') !== false && strpos($norm, 'macronutrientes') !== false) {
            $key = 'suelos.macroscic';
            $label = 'Macronutrientes y CIC';
            $filename = 'macroscic_controller.php';
        } elseif (strpos($norm, 'cic') !== false) {
            $key = 'suelos.cic';
            $label = 'CIC';
            $filename = 'cic_controller.php';
        } elseif (strpos($norm, 'materia organica') !== false || $norm === 'mo') {
            $key = 'suelos.mo';
            $label = '%MO';
            $filename = 'mo_controller.php';
        } elseif (strpos($norm, 'micro') !== false) {
            $key = 'suelos.micros';
            $label = 'Micro Nutrientes (Cu, Zn, Fe, Mn, K)';
            $filename = 'micros_controller.php';
        } elseif (strpos($norm, 'nitrogeno') !== false) {
            $key = 'suelos.nitrogeno';
            $label = 'Nitrógeno';
            $filename = 'nitrogeno_controller.php';
        } elseif (strpos($norm, 'boro') !== false) {
            $key = 'suelos.boro';
            $label = 'Boro';
            $filename = 'boro_controller.php';
        } elseif (strpos($norm, 'azufre') !== false) {
            $key = 'suelos.azufre';
            $label = 'Azufre';
            $filename = 'azufre_controller.php';
        } elseif (strpos($norm, 'fosforo') !== false) {
            $key = 'suelos.fosforo';
            $label = 'Fósforo';
            $filename = 'fosforo_controller.php';
        }
    } elseif ($sampleTypeKey === 'aguas') {
        if (strpos($norm, 'macronutrientes') !== false || $norm === 'macros') {
            $key = 'aguas.macros';
            $label = 'Macronutrientes';
            $filename = 'macros_controller.php';
        } elseif (strpos($norm, 'ras') !== false) {
            $key = 'aguas.ras';
            $label = 'RAS';
            $filename = 'ras_controller.php';
        } elseif (strpos($norm, 'boro') !== false) {
            $key = 'aguas.boro';
            $label = 'Boro';
            $filename = 'boro_controller.php';
        } elseif ($norm === 'ph') {
            $key = 'aguas.ph';
            $label = 'pH';
            $filename = 'ph_controller.php';
        } elseif (strpos($norm, 'salinidad') !== false) {
            $key = 'aguas.salinidad';
            $label = 'Salinidad';
            $filename = 'salinidad_controller.php';
        } elseif (strpos($norm, 'dureza') !== false) {
            $key = 'aguas.dureza';
            $label = 'Dureza';
            $filename = 'dureza_controller.php';
        } elseif (strpos($norm, 'carbonato') !== false && strpos($norm, 'bi') === false) {
            $key = 'aguas.carbonatos';
            $label = 'Carbonatos';
            $filename = 'carbonatos_controller.php';
        } elseif (strpos($norm, 'bicarbonato') !== false) {
            $key = 'aguas.bicarbonato';
            $label = 'Bicarbonatos';
            $filename = 'bicarbonato_controller.php';
        } elseif (strpos($norm, 'micro') !== false) {
            $key = 'aguas.micros';
            $label = 'Micro Nutrientes (Cu, Zn, Fe, Mn)';
            $filename = 'micros_controller.php';
        } elseif (strpos($norm, 'fosforo') !== false) {
            $key = 'aguas.fosforo';
            $label = 'Fósforo';
            $filename = 'fosforo_controller.php';
        } elseif (strpos($norm, 'conductividad') !== false || $norm === 'ce') {
            $key = 'aguas.conductividad';
            $label = 'Conductividad Eléctrica';
            $filename = 'conductividad_controller.php';
        } elseif (strpos($norm, 'solidos totales disueltos') !== false || $norm === 'tds' || $norm === 'std') {
            $key = 'aguas.tds';
            $label = 'TDS';
            $filename = 'tds_controller.php';
        } elseif (strpos($norm, 'resistividad') !== false) {
            $key = 'aguas.resistividad';
            $label = 'Resistividad';
            $filename = 'resistividad_controller.php';
        } elseif (strpos($norm, 'cloruro') !== false) {
            $key = 'aguas.cloruros';
            $label = 'Cloruros';
            $filename = 'cloruros_controller.php';
        } elseif (strpos($norm, 'alcalinidad') !== false) {
            $key = 'aguas.alcanilidad';
            $label = 'Alcalinidad';
            $filename = 'alcanilidad_controller.php';
        }
    } elseif ($sampleTypeKey === 'foliares') {
        if (strpos($norm, 'macronutrientes') !== false || $norm === 'macros') {
            $key = 'foliares.macros';
            $label = 'Macronutrientes';
            $filename = 'macros_controller.php';
        } elseif (strpos($norm, 'nitrogeno') !== false) {
            $key = 'foliares.nitrogeno';
            $label = 'Nitrogeno';
            $filename = 'nitrogeno_controller.php';
        } elseif (strpos($norm, 'boro') !== false) {
            $key = 'foliares.boro';
            $label = 'Boro';
            $filename = 'boro_controller.php';
        } elseif (strpos($norm, 'micro') !== false) {
            $key = 'foliares.micros';
            $label = 'Micro Nutrientes (Cu, Zn, Fe, Mn, K)';
            $filename = 'micros_controller.php';
        } elseif (strpos($norm, 'fosforo') !== false) {
            $key = 'foliares.fosforo';
            $label = 'Fósforo';
            $filename = 'fosforo_controller.php';
        }
    } elseif ($sampleTypeKey === 'cana') {
        if (strpos($norm, 'peso seco') !== false) {
            $key = 'cana.peso_seco';
            $label = 'Peso seco';
            $filename = 'peso_seco_controller.php';
        } elseif (strpos($norm, 'fibra') !== false) {
            $key = 'cana.fibra';
            $label = 'Fibra';
            $filename = 'fibra_controller.php';
        } elseif (strpos($norm, 'humedad') !== false) {
            $key = 'cana.humedad';
            $label = '% de Humedad';
            $filename = 'humedad_controller.php';
        } elseif (strpos($norm, 'brix') !== false || strpos($norm, 'pol') !== false || strpos($norm, 'pureza') !== false) {
            $key = 'cana.brixpol';
            $label = 'Determinación de Brix y Pol';
            $filename = 'brixpol_controller.php';
        }
    } elseif ($sampleTypeKey === 'mieles') {
        if (strpos($norm, 'brix') !== false) {
            $key = 'mieles.brix';
            $label = 'Brix';
            $filename = 'brix_controller.php';
        }
    }

    if ($key !== null) {
        $folder = ucfirst($sampleTypeKey);
        return [
            'key' => $key,
            'label' => $label,
            'href' => "../controllers/{$folder}/{$filename}"
        ];
    }

    // Default fallback: slugify name
    $slug = preg_replace('/[^a-z0-9]+/', '_', strtolower(trim($dbAnalysisName)));
    $slug = trim($slug, '_');
    $folder = ucfirst($sampleTypeKey);
    return [
        'key' => "{$sampleTypeKey}.{$slug}",
        'label' => $dbAnalysisName,
        'href' => "../controllers/{$folder}/{$slug}_controller.php"
    ];
}

// Fetch Sample Types from database
$stmtMuestras = $conexion->query("SELECT id_tipo, nombre FROM tipo_muestra ORDER BY id_tipo");
$dbMuestras = $stmtMuestras->fetchAll(PDO::FETCH_ASSOC);

$sampleTypeKeys = [];
$sampleTypesByKey = [];

foreach ($dbMuestras as $dbMuestra) {
    $key = getSampleTypeKey($dbMuestra['nombre']);
    $sampleTypeKeys[] = $key;
    $sampleTypesByKey[$key] = [
        'id_tipo' => $dbMuestra['id_tipo'],
        'nombre' => $dbMuestra['nombre'],
    ];
}

$activeArea = trim((string) ($_GET['area'] ?? 'aguas'));
if (!in_array($activeArea, $sampleTypeKeys, true)) {
    $activeArea = in_array('aguas', $sampleTypeKeys, true) ? 'aguas' : ($sampleTypeKeys[0] ?? 'aguas');
}

// Fetch Analyses from database
$stmtAnalisis = $conexion->query("SELECT id_tipo, id_tipo_muestra, nombre FROM tipo_analisis ORDER BY id_tipo");
$dbAnalisis = $stmtAnalisis->fetchAll(PDO::FETCH_ASSOC);


$canCreateSolicitud = lab_can('laboratorio.solicitudes.crear');
$canAnalisis = lab_can('laboratorio.analisis.ver');
$canBlancoControl = lab_can('laboratorio.blanco_control.ver');
$canConsolidacion = lab_can('laboratorio.consolidacion.ver');
$canManageUsers = lab_can('laboratorio.usuarios.gestionar');

$analysesBySampleTypeId = [];
foreach ($dbAnalisis as $analisis) {
    $idMuestra = $analisis['id_tipo_muestra'];
    if (!isset($analysesBySampleTypeId[$idMuestra])) {
        $analysesBySampleTypeId[$idMuestra] = [];
    }
    $analysesBySampleTypeId[$idMuestra][] = $analisis;
}

$analysesBySampleTypeKey = [];
foreach ($sampleTypesByKey as $key => $sampleType) {
    $idMuestra = $sampleType['id_tipo'];
    $dbAnalisisList = $analysesBySampleTypeId[$idMuestra] ?? [];
    
    $analysesBySampleTypeKey[$key] = [];
    foreach ($dbAnalisisList as $dbAnalisisItem) {
        $info = get_analysis_info($key, $dbAnalisisItem['nombre']);
        if ($info) {
            $analysesBySampleTypeKey[$key][] = $info;
        }
    }
}

// Suelos group division
$suelosFisicos = [];
$suelosQuimicos = [];
$suelosAnalyses = $analysesBySampleTypeKey['suelos'] ?? [];
foreach ($suelosAnalyses as $analisis) {
    if (in_array($analisis['key'], [
        'suelos.textura',
        'suelos.humedad',
        'suelos.humedad_residual',
        'suelos.dap',
        'suelos.cc',
        'suelos.pmp'
    ], true)) {
        $suelosFisicos[] = $analisis;
    } else {
        $suelosQuimicos[] = $analisis;
    }
}

$suelosFisicos = labc_visible_analysis($suelosFisicos);
$suelosQuimicos = labc_visible_analysis($suelosQuimicos);


$suelosCardsFisicos = labc_prepare_cards($suelosFisicos, 'suelos.textura');
$suelosCardsQuimicos = labc_prepare_cards($suelosQuimicos, 'suelos.ph');

// Other sections:
$aguasAnalyses = labc_visible_analysis($analysesBySampleTypeKey['aguas'] ?? []);
$aguasCards = labc_prepare_cards($aguasAnalyses, 'aguas.ph');

$foliaresAnalyses = labc_visible_analysis($analysesBySampleTypeKey['foliares'] ?? []);
$foliaresCards = labc_prepare_cards($foliaresAnalyses, 'foliares.micros');

$canaAnalyses = labc_visible_analysis($analysesBySampleTypeKey['cana'] ?? []);
$canaCards = labc_prepare_cards($canaAnalyses, 'cana.humedad');

$mielesAnalyses = labc_visible_analysis($analysesBySampleTypeKey['mieles'] ?? []);
$mielesCards = labc_prepare_cards(array_merge($mielesAnalyses, [
    [
        'key' => 'mieles.proximos',
        'href' => '#',
        'label' => 'Próximos Análisis',
        'icon' => 'fa-circle-plus',
        'ghost' => true,
    ],
]), 'mieles.brix');

$canCreateSolicitud = lab_can('laboratorio.solicitudes.crear');
$canBlancoControl = lab_can('laboratorio.blanco_control.ver');
$canConsolidacion = lab_can('laboratorio.consolidacion.ver');
$canManageUsers = lab_can('laboratorio.usuarios.gestionar');

$utilityCards = [];
if ($canManageUsers) {
    $utilityCards[] = [
        'key' => 'usuarios',
        'href' => '../usuarios.php',
        'label' => 'Usuarios del laboratorio',
        'icon' => 'fa-users-gear',
    ];
}
if ($canBlancoControl) {
    $utilityCards[] = [
        'key' => 'blancos.control',
        'href' => '../controllers/blanco_control_controller.php',
        'label' => 'Blancos y Control Generales',
        'icon' => 'fa-vial',
    ];
}
if ($canConsolidacion) {
    $utilityCards[] = [
        'key' => 'consolidacion',
        'href' => '../controllers/consolidacion_controller.php',
        'label' => 'Hoja de consolidación',
        'icon' => 'fa-layer-group',
    ];
}
if ($canAnalisis) {
    $utilityCards[] = [
        'key' => 'analisis.catalogo',
        'href' => '../catalogo_analisis.php',
        'label' => 'Catálogo de análisis',
        'icon' => 'fa-table-list',
    ];
    $utilityCards[] = [
        'key' => 'muestras.catalogo',
        'href' => '../catalogo_muestras.php',
        'label' => 'Catálogo de muestras',
        'icon' => 'fa-vials',
    ];
}
$utilityCards = labc_prepare_cards($utilityCards, 'consolidacion');

$sampleTypeConfigs = [
    'suelos' => [
        'id' => 'suelos',
        'nav_label' => 'Suelos',
        'title' => 'Formularios de Suelos',
        'subtitle' => 'Consulta los análisis físicos y químicos disponibles.',
        'theme' => 'suelos',
        'history_url' => labc_history_href('suelos'),
        'groups' => [
            ['title' => 'Físicos', 'cards' => $suelosCardsFisicos],
            ['title' => 'Químicos', 'cards' => $suelosCardsQuimicos],
        ],
        'count' => count($suelosCardsFisicos) + count($suelosCardsQuimicos),
        'active_card' => 'suelos.ph',
    ],
    'aguas' => [
        'id' => 'aguas',
        'nav_label' => 'Aguas',
        'title' => 'Formularios de Aguas',
        'subtitle' => 'Selecciona el tipo de análisis para tus muestras recibidas.',
        'theme' => 'aguas',
        'history_url' => labc_history_href('aguas'),
        'cards' => $aguasCards,
        'count' => count($aguasCards),
        'active_card' => 'aguas.ph',
    ],
    'foliares' => [
        'id' => 'foliares',
        'nav_label' => 'Foliares',
        'title' => 'Formularios de Foliares',
        'subtitle' => 'Análisis de tejido y diagnóstico foliar.',
        'theme' => 'foliares',
        'history_url' => labc_history_href('foliares'),
        'cards' => $foliaresCards,
        'count' => count($foliaresCards),
        'active_card' => 'foliares.micros',
    ],
    'cana' => [
        'id' => 'cana',
        'nav_label' => 'Caña',
        'title' => 'Formularios de Caña',
        'subtitle' => 'Registros de proceso y calidad industrial.',
        'theme' => 'cana',
        'history_url' => labc_history_href('cana'),
        'cards' => $canaCards,
        'count' => count($canaCards),
        'active_card' => 'cana.humedad',
    ],
    'mieles' => [
        'id' => 'mieles',
        'nav_label' => 'Mieles',
        'title' => 'Formularios de Mieles',
        'subtitle' => 'Análisis disponibles y próximos formularios.',
        'theme' => 'mieles',
        'history_url' => null,
        'cards' => $mielesCards,
        'count' => count($mielesCards),
        'active_card' => 'mieles.brix',
    ],
];

$sections = [];
foreach ($sampleTypeKeys as $key) {
    if (isset($sampleTypeConfigs[$key])) {
        $sections[] = $sampleTypeConfigs[$key];
    } else {
        // Fallback for new sample types added in the database
        $dbName = $sampleTypesByKey[$key]['nombre'];
        $dbAnalyses = labc_visible_analysis($analysesBySampleTypeKey[$key] ?? []);
        $dbCards = labc_prepare_cards($dbAnalyses);
        $sections[] = [
            'id' => $key,
            'nav_label' => ucfirst($dbName),
            'title' => 'Formularios de ' . ucfirst($dbName),
            'subtitle' => 'Seleccione el tipo de análisis a realizar.',
            'theme' => $key,
            'history_url' => labc_history_href($key),
            'cards' => $dbCards,
            'count' => count($dbCards),
            'active_card' => null,
        ];
    }
}

$sectionCounts = [];
foreach ($sections as $section) {
    $sectionCounts[$section['id']] = (int) $section['count'];
}

$visibleTotal = array_sum($sectionCounts);
$activeSection = null;
foreach ($sections as $section) {
    if ($section['id'] === $activeArea) {
        $activeSection = $section;
        break;
    }
}
if ($activeSection === null) {
    $activeSection = $sections[0] ?? null;
}

$sectionsById = [];
foreach ($sections as $section) {
    $sectionsById[$section['id']] = $section;
}

$displayOrder = array_merge(
    [$activeArea],
    array_values(array_diff($sampleTypeKeys, [$activeArea]))
);

$displaySections = [];
foreach ($displayOrder as $sectionId) {
    if (isset($sectionsById[$sectionId])) {
        $displaySections[] = $sectionsById[$sectionId];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formularios de Análisis</title>
    <link rel="stylesheet" href="../styles/base.css">
    <link rel="stylesheet" href="../css/formularios_dashboard.css?v=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="lab-app">
        <aside class="lab-sidebar">
            <div class="lab-brand">
                <div class="lab-brand-mark">LAB</div>
                <div class="lab-brand-copy">
                    <span class="lab-brand-kicker">Gestión LAB</span>
                    <strong>Panel de Control</strong>
                </div>
            </div>

            <nav class="lab-nav" aria-label="Secciones de formularios">
                <?php foreach ($displaySections as $section): ?>
                    <a
                        class="lab-nav-item <?= $activeArea === $section['id'] ? 'active' : '' ?>"
                        href="<?= labc_e(labc_section_href($section['id'])) ?>"
                        aria-current="<?= $activeArea === $section['id'] ? 'page' : 'false' ?>">
                        <span class="lab-nav-icon"><i class="fa-solid <?= labc_e(labc_card_icon($section['id'])) ?>"></i></span>
                        <span class="lab-nav-text">
                            <strong><?= labc_e($section['nav_label']) ?></strong>
                        </span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="sidebar-footer">
                <?php if ($canConsolidacion || $canBlancoControl): ?>
                    <a class="sidebar-download" href="dashboard.php">
                        <i class="fa-solid fa-download"></i>
                        <span>Descargar Reportes</span>
                    </a>
                <?php endif; ?>

                <div class="sidebar-links">
                    <a href="../index.php">
                        <span>Inicio</span>
                    </a>
                    <a href="<?= labc_e(lab_logout_url()) ?>">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Cerrar Sesión</span>
                    </a>
                </div>
            </div>
        </aside>

        <main class="lab-main">
            <header class="lab-topbar">
                <div class="search-shell">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <input
                        type="search"
                        id="formSearch"
                        placeholder="Buscar formularios..."
                        autocomplete="off"
                        aria-label="Buscar formularios">
                </div>

                <div class="top-actions">
                    <?php if ($canCreateSolicitud): ?>
                        <a class="primary-action" href="menu_solicitud.php">
                            <i class="fa-solid fa-plus"></i>
                            <span>Nueva solicitud</span>
                        </a>
                    <?php endif; ?>

                    <a class="text-action" href="dashboard.php">Informes</a>

                    <button type="button" class="icon-action" aria-label="Notificaciones">
                        <i class="fa-regular fa-bell"></i>
                    </button>

                    <button type="button" class="icon-action" aria-label="Configuración">
                        <i class="fa-solid fa-gear"></i>
                    </button>

                    <div class="avatar-chip" aria-hidden="true">LAB</div>
                </div>
            </header>

            <section class="hero-card">
                <div class="hero-headline">
                    <div class="hero-labels">
                        <span class="hero-chip">Panel de formularios</span>
                        <span class="hero-chip alt"><?= labc_e($activeSection['nav_label']) ?> destacado</span>
                    </div>

                    <h1>Formularios de Laboratorio</h1>
                    <p>Seleccione el tipo de análisis a realizar para las muestras recibidas.</p>
                </div>

                <div class="hero-stats">
                    <?php foreach ($sections as $section): ?>
                        <span class="hero-stat">
                            <strong><?= (int) ($section['count'] ?? 0) ?></strong>
                            <small><?= labc_e($section['nav_label']) ?></small>
                        </span>
                    <?php endforeach; ?>
                </div>
            </section>

            <div class="content-stack">
                <?php foreach ($displaySections as $section): ?>
                    <section
                        class="form-section <?= $activeArea === $section['id'] ? 'is-active' : '' ?>"
                        id="section-<?= labc_e($section['id']) ?>"
                        data-section="<?= labc_e($section['id']) ?>"
                        data-label="<?= labc_e($section['nav_label']) ?>">
                        <div class="section-head">
                            <div>
                                <p class="section-kicker">Formularios de <?= labc_e($section['nav_label']) ?></p>
                                <h2><?= labc_e($section['title']) ?></h2>
                                <p class="section-copy"><?= labc_e($section['subtitle']) ?></p>
                            </div>

                            <?php if ($section['history_url'] !== null): ?>
                                <a class="history-btn" href="<?= labc_e($section['history_url']) ?>">
                                    <i class="fa-regular fa-clock"></i>
                                    <span>Ver historial</span>
                                </a>
                            <?php else: ?>
                                <span class="history-btn disabled" aria-disabled="true">
                                    <i class="fa-regular fa-clock"></i>
                                    <span>Ver historial</span>
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($section['groups'])): ?>
                            <?php foreach ($section['groups'] as $group): ?>
                                <div class="group-block">
                                    <div class="group-title"><?= labc_e($group['title']) ?></div>
                                    <div class="card-grid">
                                        <?php foreach ($group['cards'] as $card): ?>
                                            <?php
                                                $cardClasses = ['module-card', 'theme-' . $section['theme']];
                                                if (!empty($card['active'])) {
                                                    $cardClasses[] = 'active';
                                                }
                                                if (!empty($card['ghost'])) {
                                                    $cardClasses[] = 'ghost';
                                                }
                                                $cardClassAttr = implode(' ', $cardClasses);
                                                $search = labc_e($card['search']);
                                            ?>
                                            <?php if (!empty($card['ghost'])): ?>
                                                <div
                                                    class="<?= labc_e($cardClassAttr) ?>"
                                                    data-search="<?= $search ?>">
                                                    <span class="module-card-icon">
                                                        <i class="fa-solid <?= labc_e($card['icon']) ?>"></i>
                                                    </span>
                                                    <span class="module-card-label"><?= labc_e($card['label']) ?></span>
                                                </div>
                                            <?php else: ?>
                                                <a
                                                    class="<?= labc_e($cardClassAttr) ?>"
                                                    href="<?= labc_e($card['href']) ?>"
                                                    data-search="<?= $search ?>"
                                                    aria-current="<?= !empty($card['active']) ? 'page' : 'false' ?>">
                                                    <span class="module-card-icon">
                                                        <i class="fa-solid <?= labc_e($card['icon']) ?>"></i>
                                                    </span>
                                                    <span class="module-card-label"><?= labc_e($card['label']) ?></span>
                                                </a>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="card-grid">
                                <?php foreach ($section['cards'] as $card): ?>
                                    <?php
                                        $cardClasses = ['module-card', 'theme-' . $section['theme']];
                                        if (!empty($card['active'])) {
                                            $cardClasses[] = 'active';
                                        }
                                        if (!empty($card['ghost'])) {
                                            $cardClasses[] = 'ghost';
                                        }
                                        $cardClassAttr = implode(' ', $cardClasses);
                                        $search = labc_e($card['search']);
                                    ?>
                                    <?php if (!empty($card['ghost'])): ?>
                                        <div
                                            class="<?= labc_e($cardClassAttr) ?>"
                                            data-search="<?= $search ?>">
                                            <span class="module-card-icon">
                                                <i class="fa-solid <?= labc_e($card['icon']) ?>"></i>
                                            </span>
                                            <span class="module-card-label"><?= labc_e($card['label']) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <a
                                            class="<?= labc_e($cardClassAttr) ?>"
                                            href="<?= labc_e($card['href']) ?>"
                                            data-search="<?= $search ?>"
                                            aria-current="<?= !empty($card['active']) ? 'page' : 'false' ?>">
                                            <span class="module-card-icon">
                                                <i class="fa-solid <?= labc_e($card['icon']) ?>"></i>
                                            </span>
                                            <span class="module-card-label"><?= labc_e($card['label']) ?></span>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </section>
                <?php endforeach; ?>

                <?php if (!empty($utilityCards)): ?>
                    <section class="form-section utility-section">
                        <div class="section-head">
                            <div>
                                <p class="section-kicker">Herramientas internas</p>
                                <h2>Gestión adicional</h2>
                                <p class="section-copy">Accesos rápidos para consolidación y controles generales.</p>
                            </div>
                        </div>

                        <div class="card-grid compact">
                            <?php foreach ($utilityCards as $card): ?>
                                <a
                                    class="module-card theme-utility"
                                    href="<?= labc_e($card['href']) ?>"
                                    data-search="<?= labc_e($card['search']) ?>">
                                    <span class="module-card-icon">
                                        <i class="fa-solid <?= labc_e($card['icon']) ?>"></i>
                                    </span>
                                    <span class="module-card-label"><?= labc_e($card['label']) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <section class="no-results" id="noResults" hidden>
                    <div class="no-results-card">
                        <div class="no-results-icon">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </div>
                        <h3>No se encontraron formularios</h3>
                        <p>Prueba con otro nombre o limpia la búsqueda para volver a ver todas las opciones.</p>
                    </div>
                </section>
            </div>

            <a class="floating-action" href="menu_solicitud.php" aria-label="Nueva solicitud">
                <i class="fa-solid fa-plus"></i>
            </a>
        </main>
    </div>

    <script>
    (function () {
        const searchInput = document.getElementById('formSearch');
        const sections = Array.from(document.querySelectorAll('.form-section'));
        const noResults = document.getElementById('noResults');

        function filterCards() {
            const query = (searchInput.value || '').trim().toLowerCase();
            let visibleCards = 0;

            sections.forEach((section) => {
                const cards = Array.from(section.querySelectorAll('.module-card'));
                let sectionVisible = false;

                cards.forEach((card) => {
                    const text = (card.dataset.search || card.textContent || '').toLowerCase();
                    const match = !query || text.includes(query);
                    card.hidden = !match;
                    if (match) {
                        sectionVisible = true;
                        visibleCards += 1;
                    }
                });

                section.hidden = !sectionVisible;
            });

            noResults.hidden = visibleCards !== 0;
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterCards);
            filterCards();
        }
    })();
    </script>
</body>
</html>
