<?php
require_once __DIR__ . '/../includes/auth.php';

lab_require_permission('laboratorio.solicitudes.crear');

$loteSeleccionado = trim((string) ($_GET['lote'] ?? ''));
$currentUser = lab_current_user();

function menuSolicitudUrl(string $tipo, string $lote): string
{
    $url = 'solicitud_formulario.php?tipo=' . rawurlencode($tipo);
    if ($lote !== '') {
        $url .= '&lote=' . rawurlencode($lote);
    }

    return $url;
}

function menuSolicitudEscape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function menuSolicitudAvatar(array $user): string
{
    $name = trim((string) ($user['nombre'] ?? ''));
    if ($name === '') {
        return 'LAB';
    }

    $initial = function_exists('mb_substr')
        ? mb_substr($name, 0, 1, 'UTF-8')
        : substr($name, 0, 1);

    return strtoupper((string) $initial);
}

$menuSolicitudAvatar = menuSolicitudAvatar($currentUser);
$newRequestHref = 'menu_solicitud.php' . ($loteSeleccionado !== '' ? '?lote=' . rawurlencode($loteSeleccionado) : '');

$sampleCards = [
    [
        'type' => 'suelo-fisico',
        'title' => 'Suelos',
        'subtitle' => 'Análisis físico químico',
        'icon' => 'fa-mountain',
        'accent' => 'suelos',
    ],
    [
        'type' => 'foliares',
        'title' => 'Foliares',
        'subtitle' => 'Tejido vegetal y nutrición',
        'icon' => 'fa-leaf',
        'accent' => 'foliares',
    ],
    [
        'type' => 'cana',
        'title' => 'Caña',
        'subtitle' => 'Rendimiento y sacarosa',
        'icon' => 'fa-tractor',
        'accent' => 'cana',
    ],
    [
        'type' => 'miel',
        'title' => 'Miel',
        'subtitle' => 'Pureza y componentes',
        'icon' => 'fa-mug-hot',
        'accent' => 'miel',
    ],
    [
        'type' => 'agua',
        'title' => 'Agua',
        'subtitle' => 'Calidad de riego y consumo',
        'icon' => 'fa-droplet',
        'accent' => 'agua',
        'wide' => true,
    ],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Nuevo análisis — AgroLab</title>

    <link rel="stylesheet" href="../styles/base.css">
    <link rel="stylesheet" href="../css/menu_solicitud.css?v=3">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="page-shell">
    <header class="topbar">
        <div class="topbar-left">
            <a class="brand-chip" href="../../Menu.php" aria-label="Ir al escritorio">
                LAB
            </a>

            <nav class="topnav" aria-label="Navegación principal">
                <a href="../../Menu.php">Escritorio</a>
                <a class="active" href="listar_lotes.php" aria-current="page">Muestras</a>
                <a href="../index.php">Laboratorio</a>
            </nav>
        </div>

        <div class="topbar-actions">
            <a class="action-button primary" href="<?= menuSolicitudEscape($newRequestHref) ?>">
                <i class="fa-solid fa-plus"></i>
                <span>Nueva solicitud</span>
            </a>

            <a class="action-button subtle" href="dashboard.php">Historial</a>

            <button type="button" class="icon-button" aria-label="Notificaciones">
                <i class="fa-regular fa-bell"></i>
            </button>

            <button type="button" class="icon-button" aria-label="Configuración">
                <i class="fa-solid fa-gear"></i>
            </button>

            <div class="avatar-chip" title="<?= menuSolicitudEscape((string) ($currentUser['nombre'] ?? 'Usuario')) ?>">
                <?= menuSolicitudEscape($menuSolicitudAvatar) ?>
            </div>
        </div>
    </header>

    <main class="page-main">
        <section class="request-panel">
            <div class="panel-head">
                <h1>Nuevo análisis</h1>
                <p>Seleccione el tipo de muestra que desea registrar para iniciar el proceso</p>
            </div>

            <div class="sample-grid" aria-label="Tipos de muestra disponibles">
                <?php foreach ($sampleCards as $card): ?>
                    <?php
                        $classes = ['sample-card', 'sample-card--' . $card['accent']];
                        if (!empty($card['wide'])) {
                            $classes[] = 'sample-card--wide';
                        }
                    ?>
                    <a
                        class="<?= menuSolicitudEscape(implode(' ', $classes)) ?>"
                        href="<?= menuSolicitudEscape(menuSolicitudUrl($card['type'], $loteSeleccionado)) ?>">
                        <span class="sample-card-icon" aria-hidden="true">
                            <i class="fa-solid <?= menuSolicitudEscape($card['icon']) ?>"></i>
                        </span>

                        <span class="sample-card-copy">
                            <strong><?= menuSolicitudEscape($card['title']) ?></strong>
                            <small><?= menuSolicitudEscape($card['subtitle']) ?></small>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="info-callout">
                <div class="info-callout-icon" aria-hidden="true">
                    <i class="fa-solid fa-circle-info"></i>
                </div>

                <p>
                    Al elegir un tipo, el sistema abrirá automáticamente el formulario correspondiente con sus
                    análisis y filtros configurados específicamente para ese rubro.
                </p>
            </div>

            <footer class="panel-footer">
                <a class="back-link" href="../index.php">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Volver al inicio</span>
                </a>
            </footer>
        </section>
    </main>
</div>
</body>
</html>
