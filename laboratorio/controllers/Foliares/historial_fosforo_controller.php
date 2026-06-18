<?php

require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('foliares.fosforo');

require_once __DIR__ . '/../../models/Foliares/fosforo_model.php';

$historial = obtenerHistorialFosforo();

require_once __DIR__ . '/../../view/Foliares/historial_fosforo_view.php';

?>
