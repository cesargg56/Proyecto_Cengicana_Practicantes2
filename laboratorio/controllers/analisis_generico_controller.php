<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/analisis_post_helper.php';
require_once __DIR__ . '/../includes/analisis_generico_config.php';
require_once __DIR__ . '/../models/analisis_generico_model.php';

function lab_generic_controller_run(string $slug): void
{
    $config = lab_generic_analysis_config($slug);
    if (!$config) {
        lab_forbidden('El formulario solicitado no esta configurado.');
    }

    lab_require_analysis_access($config['key']);

    $resultado = null;
    $labAnalysisContexto = [
        'tipos' => $config['tipos'],
        'analisis' => $config['analisis'],
        'label' => $config['elemento'] . ' en ' . $config['tipo'],
    ];
    $labSkipFooterBaseSave = true;
    $GLOBALS['labAnalysisContexto'] = $labAnalysisContexto;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fieldNames = array_map(fn($field) => $field['name'], $config['fields']);
        $rowCountFields = array_merge($fieldNames, ['lote', 'numero_laboratorio']);
        $rows = [];

        for ($fila = 0, $total = lab_post_row_count($rowCountFields); $fila < $total; $fila++) {
            if (!lab_post_row_has_data($rowCountFields, $fila)) {
                continue;
            }

            $row = [
                'lote' => lab_post_string('lote', $fila),
                'numero_laboratorio' => lab_post_string('numero_laboratorio', $fila),
            ];

            foreach ($config['fields'] as $field) {
                $name = $field['name'];
                $type = $field['type'] ?? 'number';
                $row[$name] = $type === 'text'
                    ? lab_post_string($name, $fila)
                    : lab_post_float($name, $fila);
            }

            $rows[] = $row;
        }

        $resultado = labGenericGuardarAnalisis(
            $config,
            $rows,
            trim((string) ($_POST['fecha'] ?? date('Y-m-d'))),
            trim((string) ($_POST['analista'] ?? ''))
        );
    }

    require __DIR__ . '/../view/analisis_generico_view.php';
}
