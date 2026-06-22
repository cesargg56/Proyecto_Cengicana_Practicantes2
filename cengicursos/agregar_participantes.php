<?php

require_once "revisar_permisos.php";
cengi_require_admin();

require_once("conexion.php");

$db = conectar();                    // Supabase PostgreSQL
$usersDb = conectar_usuarios_menu(); // MySQL usuarios

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-theme.css" rel="stylesheet">
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">
</head>

<body>

<?php include_once("menu.php"); ?>

<div class="container" id="cargar_participantes">

    <div class="panel panel-success">

        <div class="panel-heading">
            <h3 class="panel-title">Carga de Participantes</h3>
        </div>

        <div class="panel-body">

            <form method="POST"
                  action="carga_participantes.php"
                  enctype="multipart/form-data"
                  autocomplete="off"
                  accept-charset="UTF-8">

                <!-- INGENIO -->
                <div class="form-group">

                    <label class="col-sm-2 control-label">
                        Ingenio
                    </label>

                    <?php

                    $sqlIngenios = "
                        SELECT id, nombre_ingenios
                        FROM ingenios
                        ORDER BY nombre_ingenios
                    ";

                    $ingenios = $db->query($sqlIngenios);

                    ?>

                    <select class="form-control"
                            id="ingenio"
                            name="ingenio"
                            required>

                        <?php while ($ingenio = $ingenios->fetch(PDO::FETCH_ASSOC)) { ?>

                            <option value="<?= $ingenio['id']; ?>">
                                <?= htmlspecialchars($ingenio['nombre_ingenios']); ?>
                            </option>

                        <?php } ?>

                    </select>

                </div>

                <!-- DELEGADO -->
                <div class="form-group">

                    <label class="col-sm-2 control-label">
                        Delegado
                    </label>

                    <?php

                    $sqlUsers = "
                        SELECT id, nombre
                        FROM users
                        ORDER BY nombre
                    ";

                    $users = mysqli_query($usersDb, $sqlUsers);

                    ?>

                    <select class="form-control"
                            id="user"
                            name="user"
                            required>

                        <?php while ($user = mysqli_fetch_assoc($users)) { ?>

                            <option value="<?= $user['id']; ?>">
                                <?= htmlspecialchars($user['nombre']); ?>
                            </option>

                        <?php } ?>

                    </select>

                </div>

                <!-- CURSO -->
                <div class="form-group">

                    <label class="col-sm-2 control-label">
                        Curso
                    </label>

                    <?php

                    $sqlCursos = "
                        SELECT id, nombre_cursos
                        FROM cursos
                        ORDER BY nombre_cursos
                    ";

                    $cursos = $db->query($sqlCursos);

                    ?>

                    <select class="form-control"
                            id="curso"
                            name="curso"
                            required>

                        <?php while ($curso = $cursos->fetch(PDO::FETCH_ASSOC)) { ?>

                            <option value="<?= $curso['id']; ?>">
                                <?= htmlspecialchars($curso['nombre_cursos']); ?>
                            </option>

                        <?php } ?>

                    </select>

                </div>

                <!-- ARCHIVO -->
                <div class="form-group">

                    <label class="col-sm-2 control-label">
                        Archivo
                    </label>

                    <input id="archivo"
                           accept=".csv"
                           class="form-control"
                           name="archivo"
                           type="file">

                </div>

                <!-- BOTONES -->
                <div class="form-group">

                    <button type="submit" class="btn btn-success">
                        Guardar
                    </button>

                    <a href="index.php" class="btn btn-danger">
                        Cancelar
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

</body>
</html>