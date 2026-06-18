<?php

require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.fosforo');

require_once __DIR__ . '/../../models/Suelos/fosforo_model.php';

$historial = obtenerHistorialFosforo();

require_once __DIR__ . '/../../view/Suelos/historial_fosforo_view.php';

?>
