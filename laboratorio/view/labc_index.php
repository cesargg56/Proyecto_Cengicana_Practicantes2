<?php
require_once __DIR__ . '/../includes/auth.php';

lab_require_module_access();

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

$activeArea = trim((string) ($_GET['area'] ?? 'aguas'));
if (!in_array($activeArea, ['suelos', 'aguas', 'foliares', 'cana', 'mieles'], true)) {
    $activeArea = 'aguas';
}

$suelosFisicos = labc_visible_analysis([
    ['key' => 'suelos.textura', 'href' => '../controllers/Suelos/textura_controller.php', 'label' => 'Textura'],
    ['key' => 'suelos.humedad', 'href' => '../controllers/Suelos/humedad_controller.php', 'label' => 'Humedad'],
    ['key' => 'suelos.humedad_residual', 'href' => '../controllers/Suelos/humedad_residual_controller.php', 'label' => 'Humedad residual'],
    ['key' => 'suelos.dap', 'href' => '../controllers/Suelos/dap_controller.php', 'label' => 'Densidad aparente (DAP)'],
    ['key' => 'suelos.cc', 'href' => '../controllers/Suelos/cc_controller.php', 'label' => 'Capacidad de Campo'],
    ['key' => 'suelos.pmp', 'href' => '../controllers/Suelos/pmp_controller.php', 'label' => 'Punto de Marchitez Permanente'],
]);

$suelosQuimicos = labc_visible_analysis([
    ['key' => 'suelos.ph', 'href' => '../controllers/Suelos/ph_controller.php', 'label' => 'pH'],
    ['key' => 'suelos.cic', 'href' => '../controllers/Suelos/cic_controller.php', 'label' => 'CIC'],
    ['key' => 'suelos.mo', 'href' => '../controllers/Suelos/mo_controller.php', 'label' => '%MO'],
    ['key' => 'suelos.macroscic', 'href' => '../controllers/Suelos/macroscic_controller.php', 'label' => 'Macronutrientes y CIC'],
    ['key' => 'suelos.micros', 'href' => '../controllers/Suelos/micros_controller.php', 'label' => 'Micro Nutrientes (Cu, Zn, Fe, Mn, K)'],
    ['key' => 'suelos.nitrogeno', 'href' => '../controllers/Suelos/nitrogeno_controller.php', 'label' => 'Nitrógeno'],
    ['key' => 'suelos.boro', 'href' => '../controllers/Suelos/boro_controller.php', 'label' => 'Boro'],
    ['key' => 'suelos.azufre', 'href' => '../controllers/Suelos/azufre_controller.php', 'label' => 'Azufre'],
    ['key' => 'suelos.fosforo', 'href' => '../controllers/Suelos/fosforo_controller.php', 'label' => 'Fósforo'],
]);

$aguas = labc_visible_analysis([
    ['key' => 'aguas.macros', 'href' => '../controllers/Aguas/macros_controller.php', 'label' => 'Macronutrientes'],
    ['key' => 'aguas.ras', 'href' => '../controllers/Aguas/ras_controller.php', 'label' => 'RAS'],
    ['key' => 'aguas.boro', 'href' => '../controllers/Aguas/boro_controller.php', 'label' => 'Boro'],
    ['key' => 'aguas.ph', 'href' => '../controllers/Aguas/ph_controller.php', 'label' => 'pH'],
    ['key' => 'aguas.salinidad', 'href' => '../controllers/Aguas/salinidad_controller.php', 'label' => 'Salinidad'],
    ['key' => 'aguas.dureza', 'href' => '../controllers/Aguas/dureza_controller.php', 'label' => 'Dureza'],
    ['key' => 'aguas.carbonatos', 'href' => '../controllers/Aguas/carbonatos_controller.php', 'label' => 'Carbonatos'],
    ['key' => 'aguas.micros', 'href' => '../controllers/Aguas/micros_controller.php', 'label' => 'Micro Nutrientes (Cu, Zn, Fe, Mn)'],
    ['key' => 'aguas.fosforo', 'href' => '../controllers/Aguas/fosforo_controller.php', 'label' => 'Fósforo'],
    ['key' => 'aguas.conductividad', 'href' => '../controllers/Aguas/conductividad_controller.php', 'label' => 'Conductividad Eléctrica'],
    ['key' => 'aguas.tds', 'href' => '../controllers/Aguas/tds_controller.php', 'label' => 'TDS'],
    ['key' => 'aguas.resistividad', 'href' => '../controllers/Aguas/resistividad_controller.php', 'label' => 'Resistividad'],
    ['key' => 'aguas.cloruros', 'href' => '../controllers/Aguas/cloruros_controller.php', 'label' => 'Cloruros'],
    ['key' => 'aguas.alcanilidad', 'href' => '../controllers/Aguas/alcanilidad_controller.php', 'label' => 'Alcalinidad'],
    ['key' => 'aguas.bicarbonato', 'href' => '../controllers/Aguas/bicarbonato_controller.php', 'label' => 'Bicarbonatos'],
]);

$foliares = labc_visible_analysis([
    ['key' => 'foliares.macros', 'href' => '../controllers/Foliares/macros_controller.php', 'label' => 'Macronutrientes'],
    ['key' => 'foliares.nitrogeno', 'href' => '../controllers/Foliares/nitrogeno_controller.php', 'label' => 'Nitrogeno'],
    ['key' => 'foliares.boro', 'href' => '../controllers/Foliares/boro_controller.php', 'label' => 'Boro'],
    ['key' => 'foliares.micros', 'href' => '../controllers/Foliares/micros_controller.php', 'label' => 'Micro Nutrientes (Cu, Zn, Fe, Mn, K)'],
    ['key' => 'foliares.fosforo', 'href' => '../controllers/Foliares/fosforo_controller.php', 'label' => 'Fósforo'],
]);

$cana = labc_visible_analysis([
    ['key' => 'cana.peso_seco', 'href' => '../controllers/Cana/peso_seco_controller.php', 'label' => 'Peso seco'],
    ['key' => 'cana.fibra', 'href' => '../controllers/Cana/fibra_controller.php', 'label' => 'Fibra'],
    ['key' => 'cana.humedad', 'href' => '../controllers/Cana/humedad_controller.php', 'label' => '% de Humedad'],
    ['key' => 'cana.brixpol', 'href' => '../controllers/Cana/brixpol_controller.php', 'label' => 'Determinación de Brix y Pol'],
]);

$mieles = labc_visible_analysis([
    ['key' => 'mieles.brix', 'href' => '../controllers/Mieles/brix_controller.php', 'label' => 'Brix'],
]);

$canCreateSolicitud = lab_can('laboratorio.solicitudes.crear');
$canAnalisis = lab_can('laboratorio.analisis.ver');
$canBlancoControl = lab_can('laboratorio.blanco_control.ver');
$canConsolidacion = lab_can('laboratorio.consolidacion.ver');
$canManageUsers = lab_can('laboratorio.usuarios.gestionar');

$suelosCardsFisicos = labc_prepare_cards($suelosFisicos, 'suelos.textura');
$suelosCardsQuimicos = labc_prepare_cards($suelosQuimicos, 'suelos.ph');
$aguasCards = labc_prepare_cards($aguas, 'aguas.ph');
$foliaresCards = labc_prepare_cards($foliares, 'foliares.micros');
$canaCards = labc_prepare_cards($cana, 'cana.humedad');
$mielesCards = labc_prepare_cards(array_merge($mieles, [
    [
        'key' => 'mieles.proximos',
        'href' => '#',
        'label' => 'Próximos Análisis',
        'icon' => 'fa-circle-plus',
        'ghost' => true,
    ],
]), 'mieles.brix');

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
}
$utilityCards = labc_prepare_cards($utilityCards, 'consolidacion');

$sections = [
    [
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
    [
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
    [
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
    [
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
    [
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
    $activeSection = $sections[0];
}

$sectionsById = [];
foreach ($sections as $section) {
    $sectionsById[$section['id']] = $section;
}

$displayOrder = array_merge(
    [$activeArea],
    array_values(array_diff(['suelos', 'aguas', 'foliares', 'cana', 'mieles'], [$activeArea]))
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
                    <span class="hero-stat">
                        <strong><?= (int) ($sectionCounts['suelos'] ?? 0) ?></strong>
                        <small>Suelos</small>
                    </span>
                    <span class="hero-stat">
                        <strong><?= (int) ($sectionCounts['aguas'] ?? 0) ?></strong>
                        <small>Aguas</small>
                    </span>
                    <span class="hero-stat">
                        <strong><?= (int) ($sectionCounts['foliares'] ?? 0) ?></strong>
                        <small>Foliares</small>
                    </span>
                    <span class="hero-stat">
                        <strong><?= (int) ($sectionCounts['cana'] ?? 0) ?></strong>
                        <small>Caña</small>
                    </span>
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
