<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/blanco_control_model.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$seccion = $_POST['seccion'] ?? $_GET['seccion'] ?? 'blanco';
$seccion = in_array($seccion, ['blanco', 'control'], true) ? $seccion : 'blanco';

if (in_array($action, ['save', 'delete'], true)) {
    lab_require_permission('laboratorio.blanco_control.gestionar');
} else {
    lab_require_permission('laboratorio.blanco_control.ver');
}

function postValue($primary, $fallback = null) {
    if (isset($_POST[$primary])) {
        return $_POST[$primary];
    }

    return $fallback !== null && isset($_POST[$fallback]) ? $_POST[$fallback] : null;
}

function postInt($primary, $fallback = null) {
    $value = postValue($primary, $fallback);
    return $value !== null && $value !== '' ? (int)$value : null;
}

function postFloat($primary, $fallback = null) {
    $value = postValue($primary, $fallback);
    return $value !== null && $value !== '' ? (float)$value : null;
}

function postStr($primary, $fallback = null) {
    $value = postValue($primary, $fallback);
    return $value !== null ? trim($value) : '';
}

function redirectBlancoControl($msg, $seccion) {
    header('Location: ../view/blanco_control_view.php?msg=' . urlencode($msg) . '&seccion=' . urlencode($seccion));
    exit;
}

if ($action === 'save') {
    $id = postInt('id');
    $id_rango = postInt('id_rango');
    $id_tipo = postInt('id_tipo_analisis');
    $codigo = postStr('codigo');
    $descripcion = postStr('descripcion');
    $valor = $seccion === 'blanco'
        ? postFloat('valor_blanco', 'valor')
        : postFloat('valor_control', 'valor');
    $activo = isset($_POST['activo']) ? 1 : 0;

    if ($id_rango === null || $id_tipo === null || $codigo === '' || $valor === null) {
        redirectBlancoControl('missing', $seccion);
    }

    if ($seccion === 'blanco') {
        if ($id) {
            actualizarBlanco($id, $id_rango, $id_tipo, $codigo, $descripcion, $valor, $activo);
            redirectBlancoControl('updated', $seccion);
        }

        guardarBlanco($id_rango, $id_tipo, $codigo, $descripcion, $valor, $activo);
        redirectBlancoControl('created', $seccion);
    }

    $minimo = postFloat('minimo_control', 'minimo');
    $maximo = postFloat('maximo_control', 'maximo');

    if ($id) {
        actualizarControl($id, $id_rango, $id_tipo, $codigo, $descripcion, $valor, $minimo, $maximo, $activo);
        redirectBlancoControl('updated', $seccion);
    }

    guardarControl($id_rango, $id_tipo, $codigo, $descripcion, $valor, $minimo, $maximo, $activo);
    redirectBlancoControl('created', $seccion);
}

if ($action === 'delete') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id) {
        $seccion === 'blanco' ? eliminarBlanco($id) : eliminarControl($id);
    }

    redirectBlancoControl('deleted', $seccion);
}

header('Location: ../view/blanco_control_view.php');
exit;
