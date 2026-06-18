<?php

require_once __DIR__ . '/../includes/auth.php';
lab_require_module_access();

if (!lab_can_view_error_forms()) {
    lab_forbidden('No tiene permisos para ver formularios con errores.');
}

require_once __DIR__ . '/../models/formularios_erroneos_model.php';

$idVersion = filter_input(INPUT_GET, 'id_version', FILTER_VALIDATE_INT);
$detalleError = null;

if ($idVersion) {
    $detalleError = obtenerFormularioErroneoDetalle((int) $idVersion);
    if (!$detalleError) {
        http_response_code(404);
        echo 'No se encontro el formulario con errores solicitado.';
        exit;
    }
}

$formulariosErroneos = obtenerFormulariosErroneos();

require_once __DIR__ . '/../view/formularios_erroneos_view.php';

?>
