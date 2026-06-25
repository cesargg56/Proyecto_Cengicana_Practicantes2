<?php
require_once "conexion.php";
require_once "menu.php";

$db = conectar();
$puedeGestionar = cengi_puede_gestionar();
$puedeCalificar = cengi_puede_calificar();
$esEstudiante = cengi_es_estudiante();
$campo = trim($_POST['campo'] ?? '');
$params = [];

if ($esEstudiante) {
    $sql = "
        SELECT
            c.id AS idcurso,
            c.nombre_cursos,
            ca.descripcion_categorias_cursos,
            i.nombre_ingenios,
            c.jornada_cursos,
            c.inicio,
            c.fin,
            COALESCE(cc.posevaluacion, cc.evaluacion, 0) AS nota
        FROM asignaciones a
        INNER JOIN cursos c ON c.id = a.cursos_id
        INNER JOIN categorias_cursos ca ON ca.id = c.categoria_curso_id
        INNER JOIN ingenios i ON i.id = c.ingenio_id
        INNER JOIN participantes p ON p.id = a.participantes_id
        LEFT JOIN control_cursos cc ON cc.asignacion_id = a.id
        WHERE (a.usuarios_id = ? OR p.usuarios_id = ?)
    ";

    $params[] = cengi_usuario_actual_id();
    $params[] = cengi_usuario_actual_id();

    if ($campo !== '') {
        $sql .= " AND c.nombre_cursos LIKE ?";
        $params[] = '%' . $campo . '%';
    }

    $sql .= " ORDER BY c.inicio DESC, c.nombre_cursos";
} else {
    $condiciones = [];

    if ($campo !== '') {
        $condiciones[] = "c.nombre_cursos LIKE ?";
        $params[] = '%' . $campo . '%';
    }

    if (!cengi_ve_todo_por_rol_o_ingenio()) {
        $normalizado = cengi_texto_normalizado_fuerte(cengi_ingenio_nombre_actual());
        $condiciones[] = "regexp_replace(lower(translate(i.nombre_ingenios, 'áéíóúÁÉÍÓÚñÑ', 'aeiouAEIOUnN')), '[^a-z0-9]+', '', 'g') = ?";
        $params[] = $normalizado;
        $condiciones[] = "
            EXISTS (
                SELECT 1
                FROM asignaciones a
                INNER JOIN participantes p ON p.id = a.participantes_id
                INNER JOIN ingenios ip ON ip.id = p.ingenio_id
                WHERE a.cursos_id = c.id
                  AND a.estado_asignaciones = 1
                  AND regexp_replace(lower(translate(ip.nombre_ingenios, 'áéíóúÁÉÍÓÚñÑ', 'aeiouAEIOUnN')), '[^a-z0-9]+', '', 'g') = ?
            )
        ";
        $params[] = $normalizado;
    }

    $where = $condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '';

    $sql = "
        SELECT
            c.id AS idcurso,
            ca.descripcion_categorias_cursos,
            i.nombre_ingenios,
            c.nombre_cursos,
            c.jornada_cursos,
            c.dias,
            c.horario,
            c.inicio,
            c.fin
        FROM cursos c
        INNER JOIN categorias_cursos ca ON c.categoria_curso_id = ca.id
        INNER JOIN ingenios i ON c.ingenio_id = i.id
        {$where}
        ORDER BY c.nombre_cursos
    ";
}

$stmt = $db->prepare($sql);
$stmt->execute($params);
?>

<html lang="es">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css">
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <meta charset="utf-8">
</head>

<body>
    <?php menu_render(); ?>
    <div class="container">
        <div class="cengi-hero">
            <span class="cengi-chip">Cursos</span>
            <h2><?php echo $esEstudiante ? 'Mis cursos asignados' : 'Cursos registrados'; ?></h2>
            <p>
                <?php echo $esEstudiante
                    ? 'Consulta tus cursos y la nota registrada sin permisos de edicion.'
                    : 'Administra la oferta de cursos por categoria, ingenio y calendario.'; ?>
            </p>
        </div>

        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $esEstudiante ? 'Cursos y nota' : 'Cursos registrados'; ?></h3>
            </div>

            <div class="panel-body">
                <?php if ($puedeGestionar): ?>
                    <div class="row">
                        <div class="col-sm-6">
                            <a href="agregar_cursos.php" class="btn btn-primary">Nuevo registro</a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                        <div class="col-sm-4">
                            <input type="text" placeholder="Nombre del curso" class="form-control" name="campo" id="campo" value="<?php echo htmlspecialchars($campo); ?>">
                        </div>
                        <div class="col-sm-2">
                            <input type="submit" name="enviar" id="enviar" value="Buscar" class="btn btn-success">
                        </div>
                    </form>
                </div>

                <br>

                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <?php if ($esEstudiante): ?>
                        <tr>
                            <th>ID</th>
                            <th>Curso</th>
                            <th>Categoria</th>
                            <th>Ingenio</th>
                            <th>Jornada</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Nota</th>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <th>ID</th>
                            <th>Curso</th>
                            <th>Categoria</th>
                            <th>Ingenio</th>
                            <th>Jornada</th>
                            <th>Dias</th>
                            <th>Horario</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <?php if ($puedeGestionar || $puedeCalificar): ?><th>Acciones</th><?php endif; ?>
                        </tr>
                    <?php endif; ?>
                    </thead>
                    <tbody>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['idcurso']); ?></td>
                                <td><?php echo htmlspecialchars($row['nombre_cursos']); ?></td>
                                <td><?php echo htmlspecialchars($row['descripcion_categorias_cursos']); ?></td>
                                <td><?php echo htmlspecialchars($row['nombre_ingenios']); ?></td>
                                <td><?php echo htmlspecialchars($row['jornada_cursos']); ?></td>
                                <?php if ($esEstudiante): ?>
                                    <td><?php echo htmlspecialchars($row['inicio']); ?></td>
                                    <td><?php echo htmlspecialchars($row['fin']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['nota']); ?></strong></td>
                                <?php else: ?>
                                    <td><?php echo htmlspecialchars($row['dias']); ?></td>
                                    <td><?php echo htmlspecialchars($row['horario']); ?></td>
                                    <td><?php echo htmlspecialchars($row['inicio']); ?></td>
                                    <td><?php echo htmlspecialchars($row['fin']); ?></td>
                                    <?php if ($puedeGestionar || $puedeCalificar): ?>
                                        <td>
                                            <?php if ($puedeGestionar): ?>
                                                <a href="modificar_cursos.php?id=<?php echo (int) $row['idcurso']; ?>"><span class="glyphicon glyphicon-pencil"></span></a>
                                                &nbsp;
                                                <a href="#" data-href="eliminar_cursos.php?id=<?php echo (int) $row['idcurso']; ?>" data-toggle="modal" data-target="#confirm-delete"><span class="glyphicon glyphicon-trash"></span></a>
                                            <?php endif; ?>
                                            <a href="ver_participante_curso.php?id=<?php echo (int) $row['idcurso']; ?>"><span class="glyphicon glyphicon-list-alt"></span></a>
                                        </td>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if (!$esEstudiante): ?>
    <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="model-header">
                    <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Eliminar registro</h4>
                </div>
                <div class="modal-body">Desea eliminar este registro?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <a class="btn btn-danger btn-ok">Eliminar</a>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $('#confirm-delete').on('show.bs.modal', function (e) {
            $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
        });
    </script>
    <?php endif; ?>
</body>
</html>


