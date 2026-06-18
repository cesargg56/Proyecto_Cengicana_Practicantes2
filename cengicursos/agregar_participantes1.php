<?php
require_once "revisar_permisos.php";
require_once "conexion.php";
require_once "menu.php";

cengi_require_admin("ver_cursos.php");

$db = conectar();
$cursoID = (int) ($_GET['curso_id'] ?? 0);

$stmtIngenios = $db->query("
    SELECT id, nombre_ingenios
    FROM ingenios
    ORDER BY nombre_ingenios
");

$stmtCursos = $db->query("
    SELECT id, nombre_cursos
    FROM cursos
    WHERE estado_cursos = 1
    ORDER BY nombre_cursos
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nuevo participante</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css">
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</head>
<body>
    <?php menu_render(); ?>
    <div class="container">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">Nuevo participante del curso</h3>
            </div>
            <div class="panel-body">
                <form method="POST" action="guardar_participante_curso.php" autocomplete="off">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-2">
                                <label for="cui" class="control-label">CUI</label>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="cui" name="cui" placeholder="0000-00000-0000" required>
                            </div>
                            <div class="col-sm-2">
                                <label for="nombre" class="control-label">Nombre</label>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del participante" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-2">
                                <label for="ingenio" class="control-label">Ingenio</label>
                            </div>
                            <div class="col-sm-4">
                                <select class="form-control" id="ingenio" name="ingenio" required>
                                    <option value="">Selecciona un ingenio</option>
<?php while ($ingenio = $stmtIngenios->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <option value="<?php echo (int) $ingenio['id']; ?>">
                                        <?php echo htmlspecialchars($ingenio['nombre_ingenios']); ?>
                                    </option>
<?php } ?>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <label for="curso" class="control-label">Curso</label>
                            </div>
                            <div class="col-sm-4">
                                <select class="form-control" id="curso" name="curso" required>
                                    <option value="">Selecciona un curso</option>
<?php while ($curso = $stmtCursos->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <option value="<?php echo (int) $curso['id']; ?>" <?php echo $cursoID === (int) $curso['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($curso['nombre_cursos']); ?>
                                    </option>
<?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-2">
                                <label for="area" class="control-label">Area</label>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="area" name="area" placeholder="Area" required>
                            </div>
                            <div class="col-sm-2">
                                <label for="puesto" class="control-label">Puesto</label>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="puesto" name="puesto" placeholder="Puesto" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-success">Guardar y asignar</button>
                                <a href="<?php echo $cursoID > 0 ? 'ver_participante_curso.php?id=' . $cursoID : 'participantes.php'; ?>" class="btn btn-danger">Cancelar</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
