<?php

require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.boro');

require_once __DIR__ . '/../../models/Suelos/boro_model.php';

$id_boro = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$datos_curva = obtenerCurvaBoro($id_boro);

require_once __DIR__ . '/../../view/graficas/curva_view.php';

?>
