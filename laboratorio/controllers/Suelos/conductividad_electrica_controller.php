<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.conductividad_electrica');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../models/Suelos/conductividad_electrica_model.php';

$resultado = lab_analysis_take_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = [
        'exito' => false,
        'mensaje' => 'Pendiente de completar logica y base de datos para Conductividad Electrica.',
    ];
}

lab_analysis_redirect_after_success($resultado);
require_once __DIR__ . '/../../view/Suelos/conductividad_electrica_view.php';
