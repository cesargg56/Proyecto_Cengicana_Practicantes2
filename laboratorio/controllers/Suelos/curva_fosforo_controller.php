<?php

require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.fosforo');

require_once __DIR__ . '/../../models/Suelos/fosforo_model.php';

$id_fosforo = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$datos_curva = obtenerCurvaFosforo($id_fosforo);

require_once __DIR__ . '/../../view/graficas/curva_view.php';

?>
