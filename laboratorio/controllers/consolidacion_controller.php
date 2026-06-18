<?php

require_once __DIR__ . '/../includes/auth.php';
lab_require_permission('laboratorio.consolidacion.ver');

require_once __DIR__ . '/../models/consolidacion_model.php';

$tiposMuestra = listarTiposMuestraConsolidacion();
$tipoSeleccionado = filter_input(INPUT_GET, 'tipo', FILTER_VALIDATE_INT);
$loteSeleccionado = trim((string) ($_GET['lote'] ?? ''));

if (!$tipoSeleccionado && !empty($tiposMuestra)) {
    $tipoSeleccionado = (int) $tiposMuestra[0]['id_tipo'];
}

$tipoActual = $tipoSeleccionado ? obtenerTipoMuestraConsolidacion($tipoSeleccionado) : null;
$analisis = $tipoSeleccionado ? obtenerAnalisisConsolidacion($tipoSeleccionado) : [];
$filas = $tipoSeleccionado ? obtenerFilasConsolidacion($tipoSeleccionado, $loteSeleccionado) : [];
$estados = $tipoSeleccionado ? obtenerEstadosAnalisisConsolidacion($tipoSeleccionado, $filas) : [];

require_once __DIR__ . '/../view/consolidacion_view.php';

?>
