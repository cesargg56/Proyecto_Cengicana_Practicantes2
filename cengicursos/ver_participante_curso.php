<?php
require_once "conexion.php";
require_once "menu.php";

cengi_require_calificador('ver_cursos.php');

$db = conectar();
$puedeGestionar = cengi_puede_gestionar();
$soloCalifica = cengi_puede_calificar() && !$puedeGestionar;
$puedeSubirDiploma = cengi_puede_subir_diploma();
$idcurso = (int) ($_GET['id'] ?? 0);

$sql = "
    SELECT
        a.id AS asignacion_id,
        a.estado_asignaciones,
        p.nombre_participantes,
        p.cui_participantes,
        i.nombre_ingenios,
        cc.asistencia,
        cc.evaluacion,
        cc.posevaluacion,
        cc.diploma
    FROM asignaciones a
    INNER JOIN participantes p ON a.participantes_id = p.id
    INNER JOIN ingenios i ON p.ingenio_id = i.id
    LEFT JOIN control_cursos cc ON a.id = cc.asignacion_id
    WHERE a.cursos_id = ?
";

if ($soloCalifica) {
    $sql .= " AND a.estado_asignaciones = 1";
}

if (!cengi_ve_todo_por_rol_o_ingenio()) {
    $sql .= " AND p.ingenio_id = " . (int) cengi_ingenio_id_actual();
}

$stmt = $db->prepare($sql);
$stmt->execute([$idcurso]);
$filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css">
<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<title>Participantes del Curso</title>
</head>

<body>
<?php menu_render(); ?>

<div class="container">
    <div class="panel panel-success">
        <div class="panel-heading">
            <h3 class="panel-title">Participantes del Curso</h3>
        </div>

        <div class="panel-body">
            <?php if ($soloCalifica): ?>
                <div class="cengi-empty" style="margin-bottom: 20px;">
                    En esta vista solo puedes registrar notas del curso.
                </div>
            <?php endif; ?>

            <?php if ($puedeGestionar): ?>
                <div style="margin-bottom: 20px; text-align: right;">
                    <a href="agregar_participantes1.php?curso_id=<?php echo $idcurso; ?>" class="btn btn-success">
                        Agregar participante al curso
                    </a>
                </div>
            <?php endif; ?>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>CUI</th>
                        <th>Ingenio</th>
                        <?php if ($puedeGestionar): ?><th>Estado</th><?php endif; ?>
                        <?php if ($puedeGestionar): ?><th>Asistencia</th><?php endif; ?>
                        <th>Pre-Evaluacion</th>
                        <th>Pos-Evaluacion</th>
                        <th>Diploma</th>
                        <th>Guardar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($filas)) { ?>
                        <tr>
                            <td colspan="<?php echo $puedeGestionar ? 9 : 7; ?>" class="text-center">
                                No hay participantes asignados a este curso todavia.
                            </td>
                        </tr>
                    <?php } ?>

                    <?php foreach ($filas as $fila) { ?>
                        <tr>
                            <form action="guardar_control.php" method="POST" enctype="multipart/form-data">
                                <td><?= htmlspecialchars($fila['nombre_participantes']) ?></td>
                                <td><?= htmlspecialchars($fila['cui_participantes']) ?></td>
                                <td><?= htmlspecialchars($fila['nombre_ingenios']) ?></td>
                                <?php if ($puedeGestionar): ?>
                                    <td>
                                        <?php if ((int) $fila['estado_asignaciones'] === 1) { ?>
                                            <span class="label label-success">Activo</span>
                                        <?php } else { ?>
                                            <span class="label label-default">Inactivo</span>
                                        <?php } ?>
                                    </td>
                                <?php endif; ?>

                                <?php if ($puedeGestionar): ?>
                                    <td>
                                        <input
                                            type="number"
                                            name="asistencia"
                                            class="form-control"
                                            min="0"
                                            max="100"
                                            step="0.01"
                                            value="<?= htmlspecialchars($fila['asistencia']) ?>"
                                        >
                                    </td>
                                <?php endif; ?>

                                <td>
                                    <input
                                        type="number"
                                        name="evaluacion"
                                        class="form-control"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        value="<?= htmlspecialchars($fila['evaluacion']) ?>"
                                    >
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        name="posevaluacion"
                                        class="form-control"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        value="<?= htmlspecialchars($fila['posevaluacion']) ?>"
                                    >
                                </td>

                                <td>
                                    <?php if ($puedeSubirDiploma): ?>
                                        <input type="file" name="diploma" class="form-control">
                                        <br>
                                    <?php endif; ?>
                                    <?php if (!empty($fila['diploma'])) { ?>
                                        <a href="<?= htmlspecialchars($fila['diploma']) ?>" target="_blank" class="btn btn-info btn-sm">
                                            Ver PDF
                                        </a>
                                    <?php } elseif (!$puedeGestionar) { ?>
                                        <span class="text-muted">Sin diploma</span>
                                    <?php } ?>
                                </td>

                                <td>
                                    <input type="hidden" name="asignacion_id" value="<?= (int) $fila['asignacion_id'] ?>">
                                    <button type="submit" class="btn btn-success">Guardar</button>
                                    <?php if ($puedeGestionar): ?>
                                        <a
                                            href="toggle_asignacion.php?id=<?= (int) $fila['asignacion_id'] ?>&curso_id=<?= $idcurso ?>&estado=<?= (int) $fila['estado_asignaciones'] ?>"
                                            class="btn btn-<?php echo (int) $fila['estado_asignaciones'] === 1 ? 'warning' : 'info'; ?> btn-sm"
                                            style="margin-top:8px;display:inline-block;"
                                        >
                                            <?php echo (int) $fila['estado_asignaciones'] === 1 ? 'Desactivar del curso' : 'Reactivar en curso'; ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </form>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="row" style="text-align: center;">
            <a href="ver_cursos.php" class="btn btn-success">Regresar</a>
        </div>
    </div>
</div>
</body>
</html>
