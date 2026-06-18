<?php

require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.boro');

require_once __DIR__ . '/../../models/Suelos/boro_model.php';

$historial = obtenerHistorialBoro();

require_once __DIR__ . '/../../view/Suelos/historial_boro_view.php';

?>
