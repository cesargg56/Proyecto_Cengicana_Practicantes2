<?php
require_once __DIR__ . '/../includes/auth.php';

lab_require_module_access();

function labc_e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function labc_visible_analysis(array $items): array
{
    return array_values(array_filter($items, function ($item) {
        return lab_can_analysis($item['key']);
    }));
}

function labc_render_cards(array $items): void
{
    foreach ($items as $item) {
        echo '<a class="tarjeta" href="' . labc_e($item['href']) . '">' . labc_e($item['label']) . '</a>';
    }
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
    ['key' => 'suelos.mo', 'href' => '../controllers/Suelos/mo_controller.php', 'label' => '%MO'],
    ['key' => 'suelos.macroscic', 'href' => '../controllers/Suelos/macroscic_controller.php', 'label' => 'Macronutrientes y CIC'],
    ['key' => 'suelos.micros', 'href' => '../controllers/Suelos/micros_controller.php', 'label' => 'Micro Nutrientes (Cu, Zn, Fe, Mn, K)'],
    ['key' => 'suelos.nitrogeno', 'href' => '../controllers/Suelos/nitrogeno_controller.php', 'label' => 'Nitrógeno'],
    ['key' => 'suelos.boro', 'href' => '../controllers/Suelos/boro_controller.php', 'label' => 'Boro'],
    ['key' => 'suelos.azufre', 'href' => '../controllers/Suelos/azufre_controller.php', 'label' => 'Azufre'],
    ['key' => 'suelos.fosforo', 'href' => '../controllers/Suelos/fosforo_controller.php', 'label' => 'Fósforo'],
]);

$foliares = labc_visible_analysis([
    ['key' => 'foliares.macros', 'href' => '../controllers/Foliares/macros_controller.php', 'label' => 'Macronutrientes'],
    ['key' => 'foliares.nitrogeno', 'href' => '../controllers/Foliares/nitrogeno_controller.php', 'label' => 'Nitrógeno'],
    ['key' => 'foliares.boro', 'href' => '../controllers/Foliares/boro_controller.php', 'label' => 'Boro'],
    ['key' => 'foliares.micros', 'href' => '../controllers/Foliares/micros_controller.php', 'label' => 'Micro Nutrientes (Cu, Zn, Fe, Mn, K)'],
    ['key' => 'foliares.fosforo', 'href' => '../controllers/Foliares/fosforo_controller.php', 'label' => 'Fósforo'],
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

$cana = labc_visible_analysis([
    ['key' => 'cana.peso_seco', 'href' => '../controllers/Cana/peso_seco_controller.php', 'label' => 'Peso seco'],
    ['key' => 'cana.fibra', 'href' => '../controllers/Cana/fibra_controller.php', 'label' => 'Fibra'],
    ['key' => 'cana.humedad', 'href' => '../controllers/Cana/humedad_controller.php', 'label' => '% de Humedad'],
    ['key' => 'cana.brixpol', 'href' => '../controllers/Cana/brixpol_controller.php', 'label' => 'Determinación de Brix y Pol'],
]);

$mieles = labc_visible_analysis([
    ['key' => 'mieles.brix', 'href' => '../controllers/Mieles/brix_controller.php', 'label' => 'Brix'],
]);

$canBlancoControl = lab_can('laboratorio.blanco_control.ver');
$canConsolidacion = lab_can('laboratorio.consolidacion.ver');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formularios de Análisis</title>
    <link rel="stylesheet" href="../styles/index.css">
</head>
<body>
    <main class="page-wrap">
        <header class="doc-header">
            <div class="doc-header-left">
                <div class="logo-circle">LAB</div>
                <div>
                    <p class="doc-kicker">Laboratorio agrícola</p>
                    <h1 class="doc-title">Formularios de análisis</h1>
                    <p class="doc-subtitle">Selecciona el módulo que deseas registrar o consultar.</p>
                </div>
            </div>
            <div class="meta-badge">
                <span>Panel</span>
                LABC
            </div>
        </header>

        <div class="history-links">
            <a href="../index.php">Volver al inicio principal</a>
        </div>

        <?php if ($canBlancoControl || $canConsolidacion): ?>
            <section class="form-section">
                <h2 class="section-title">Blancos y Control</h2>
                <div class="card-grid">
                    <?php if ($canBlancoControl): ?>
                        <a class="tarjeta" href="../controllers/blanco_control_controller.php">Blancos y Control Generales</a>
                    <?php endif; ?>
                    <?php if ($canConsolidacion): ?>
                        <a class="tarjeta" href="../controllers/consolidacion_controller.php">Hoja de consolidación</a>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($suelosFisicos) || !empty($suelosQuimicos)): ?>
            <section class="form-section">
                <h2 class="section-title">Formularios de Suelos</h2>
                <p class="subtitulo">Selecciona el análisis a registrar</p>

                <?php if (!empty($suelosFisicos)): ?>
                    <h3>Físicos</h3>
                    <div class="card-grid">
                        <?php labc_render_cards($suelosFisicos); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($suelosQuimicos)): ?>
                    <h3>Químicos</h3>
                    <div class="card-grid">
                        <?php labc_render_cards($suelosQuimicos); ?>
                    </div>
                <?php endif; ?>

                <?php if (lab_can_analysis('suelos.boro') || lab_can_analysis('suelos.azufre') || lab_can_analysis('suelos.fosforo')): ?>
                    <div class="history-links">
                        <?php if (lab_can_analysis('suelos.boro')): ?>
                            <a href="../controllers/Suelos/historial_boro_controller.php">Ver historial de boro</a>
                        <?php endif; ?>
                        <?php if (lab_can_analysis('suelos.azufre')): ?>
                            <a href="../controllers/Suelos/historial_azufre_controller.php">Ver historial de azufre</a>
                        <?php endif; ?>
                        <?php if (lab_can_analysis('suelos.fosforo')): ?>
                            <a href="../controllers/Suelos/historial_fosforo_controller.php">Ver historial de fósforo</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <?php if (!empty($foliares)): ?>
            <section class="form-section">
                <h2 class="section-title">Formularios de Foliares</h2>
                <p class="subtitulo">Selecciona el análisis a registrar</p>
                <div class="card-grid">
                    <?php labc_render_cards($foliares); ?>
                </div>
                <?php if (lab_can_analysis('foliares.fosforo')): ?>
                    <div class="history-links">
                        <a href="../controllers/Foliares/historial_fosforo_controller.php">Ver historial de fósforo</a>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <?php if (!empty($aguas)): ?>
            <section class="form-section">
                <h2 class="section-title">Formularios de Aguas</h2>
                <p class="subtitulo">Selecciona el análisis a registrar</p>
                <div class="card-grid">
                    <?php labc_render_cards($aguas); ?>
                </div>
                <?php if (lab_can_analysis('aguas.fosforo')): ?>
                    <div class="history-links">
                        <a href="../controllers/Aguas/historial_fosforo_controller.php">Ver historial de fósforo</a>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <?php if (!empty($cana)): ?>
            <section class="form-section">
                <h2 class="section-title">Formularios de Caña</h2>
                <p class="subtitulo">Selecciona el análisis a registrar</p>
                <div class="card-grid">
                    <?php labc_render_cards($cana); ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($mieles)): ?>
            <section class="form-section">
                <h2 class="section-title">Formularios de Mieles</h2>
                <p class="subtitulo">Selecciona el análisis a registrar</p>
                <div class="card-grid">
                    <?php labc_render_cards($mieles); ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!$canBlancoControl && !$canConsolidacion && empty($suelosFisicos) && empty($suelosQuimicos) && empty($foliares) && empty($aguas) && empty($cana) && empty($mieles)): ?>
            <section class="form-section">
                <h2 class="section-title">Sin permisos asignados</h2>
                <p class="subtitulo">Tu usuario tiene acceso al módulo, pero no tiene permisos internos configurados para Laboratorio.</p>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>

