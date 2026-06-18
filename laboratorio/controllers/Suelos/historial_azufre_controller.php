<?php

require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.azufre');

require_once __DIR__ . '/../../models/Suelos/azufre_model.php';

$historial = obtenerHistorialAzufre();

require_once __DIR__ . '/../../view/Suelos/historial_azufre_view.php';

?>
