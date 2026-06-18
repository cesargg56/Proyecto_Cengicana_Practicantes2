<?php

require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.azufre');

require_once __DIR__ . '/../../models/Suelos/azufre_model.php';

$id_azufre = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$datos_curva = obtenerCurvaAzufre($id_azufre);

require_once __DIR__ . '/../../view/graficas/curva_view.php';

?>
