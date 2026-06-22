<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.boro');

$doc_elemento  = "Boro";
$doc_tipo      = "Suelos";
$doc_codigo    = "LAB-FS-042";
$doc_fecha_doc = "2024-03-01";
$doc_edicion   = "03";
$doc_vf        = "V2";

$fecha_actual  = date('d-m-Y');
$lote_actual   = "LT-2025-083";
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
    <title>Boro</title>
    <link rel="stylesheet" href="../../styles/formularios.css">
</head>
<body>
    <div class="page-wrap">
    <a class="back-link" href="../../view/labc_index.php">← Volver</a>
    <h2>Boro en Suelos</h2>

    <?php if (!empty($resultado)): ?>
        <div class="alerta <?= $resultado['exito'] ? 'exito' : 'error' ?>">
            <?= htmlspecialchars($resultado['mensaje']) ?>
        </div>
    <?php endif; ?>

    <div class="card">

        <?php include '../../components/encabezado_doc.php'; ?>

    <form method="POST" action="">
        <div class="form-body">
            <div class="section-title">Datos de análisis</div>
            <div class="field-group">
            <div class="field">
        <label>Absorbancia Blanco
            <input type="number" step="any" name="abs_blanco" id="abs_blanco" value="0.00" required>
        </label>
        <label>Control
            <input type="number" step="any" name="control" id="control" value="0.00" required>
        </label>
        <label>Absorbancia Muestra
            <input type="number" step="any" name="absorbancia" id="absorbancia" required>
        </label>    

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
                            <tr><td><input type="number" name="punto_curva[]" value="0"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
                            <tr><td><input type="number" name="punto_curva[]" value="0"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
                            <tr><td><input type="number" name="punto_curva[]" value="0.25"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
                            <tr><td><input type="number" name="punto_curva[]" value="0.25"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
                            <tr><td><input type="number" name="punto_curva[]" value="0.50"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
                            <tr><td><input type="number" name="punto_curva[]" value="0.50"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
                            <tr><td><input type="number" name="punto_curva[]" value="1"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
                            <tr><td><input type="number" name="punto_curva[]" value="1"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
                            <tr><td><input type="number" name="punto_curva[]" value="2"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
                            <tr><td><input type="number" name="punto_curva[]" value="2"></td><td><input type="number" step="any" name="abs_curva[]"></td></tr>
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
