<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.fosforo');

$doc_elemento  = "Fósforo";
$doc_tipo      = "Suelos";
$doc_codigo    = "LAB-FS-042";
$doc_fecha_doc = "2024-03-01";
$doc_edicion   = "03";
$doc_vf        = "V2";

$fecha_actual  = date('d-m-Y');
$lote_actual   = $_POST['lote'][0] ?? $_GET['lote'] ?? ($lote_actual ?? '');
$tecnicos      = [
    ['id' => 1, 'nombre' => 'Ana López Méndez'],
    ['id' => 2, 'nombre' => 'Carlos Ruiz'],
    ['id' => 3, 'nombre' => 'María Pérez'],
];
$observaciones = "";
$resultado = $resultado ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fósforo en Suelos</title>
    <link rel="stylesheet" href="../../styles/formularios.css">
</head>
<body>
<div class="page-wrap">

    <a href="../../view/labc_index.php" class="back-link">← Volver</a>

    <?php if (!empty($resultado)): ?>
        <div class="alerta <?= $resultado['exito'] ? 'exito' : 'error' ?>">
            <?= htmlspecialchars($resultado['mensaje']) ?>
        </div>
    <?php endif; ?>

    <div class="card">

        <?php include '../../components/encabezado_doc.php'; ?>

        <form method="POST" action="" data-lab-shared-rows="1">
            <div class="form-body">

                <div class="section-title">Datos de análisis</div>
                <div class="field-group">
                    <div class="field">
                        <label for="abs_blanco">Absorbancia blanco</label>
                        <input type="number" step="any" name="abs_blanco" id="abs_blanco" value="0.00" required>
                    </div>
                    <div class="field">
                        <label for="control">Control</label>
                        <input type="number" step="any" name="control" id="control" value="0.00" required>
                    </div>
                    <div class="field">
                        <label for="absorbancia">Absorbancia muestra</label>
                        <input type="number" step="any" name="absorbancia" id="absorbancia" required>
                    </div>
                </div>

                <hr class="divider">
                <div class="section-title">Curva de calibración</div>
                <div class="table-wrap calibration-table-wrap">
                    <table class="calibration-table">
                        <thead>
                            <tr>
                                <th>Punto curva</th>
                                <th>Absorbancia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td><input type="number" name="punto_curva[]" value="1"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
                            <tr><td><input type="number" name="punto_curva[]" value="1"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
                            <tr><td><input type="number" name="punto_curva[]" value="2"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
                            <tr><td><input type="number" name="punto_curva[]" value="2"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
                            <tr><td><input type="number" name="punto_curva[]" value="4"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
                            <tr><td><input type="number" name="punto_curva[]" value="4"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
                            <tr><td><input type="number" name="punto_curva[]" value="5"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
                            <tr><td><input type="number" name="punto_curva[]" value="5"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
                            <tr><td><input type="number" name="punto_curva[]" value="10"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
                            <tr><td><input type="number" name="punto_curva[]" value="10"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
                            <tr><td><input type="number" name="punto_curva[]" value="20"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
                            <tr><td><input type="number" name="punto_curva[]" value="20"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
                        </tbody>
                    </table>
                </div>
            <br>

            <?php include '../../components/pie_pagina.php'; ?>

        </form>

    </div><!-- /.card -->

</div><!-- /.page-wrap -->
</body>
</html>
