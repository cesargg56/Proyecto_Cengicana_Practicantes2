<?php

require_once("revisar_permisos.php");
cengi_require_admin();

require_once("conexion.php");

$db = conectar();

$error = '';
$resultado = false;

function obtener_post_requerido($clave)
{
    $valor = $_POST[$clave] ?? null;

    if (!is_string($valor)) {
        throw new InvalidArgumentException("Falta el campo obligatorio: {$clave}.");
    }

    $valor = trim($valor);

    if ($valor === '') {
        throw new InvalidArgumentException("El campo {$clave} no puede ir vacio.");
    }

    return $valor;
}

function sincronizar_secuencia_cursos(PDO $db)
{
    $db->exec("
        SELECT setval(
            pg_get_serial_sequence('cursos', 'id'),
            COALESCE((SELECT MAX(id) FROM cursos), 0) + 1,
            false
        )
    ");
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new InvalidArgumentException('La solicitud para guardar cursos debe enviarse por POST.');
    }

    $categorias = (int)obtener_post_requerido('categorias_cursos');
    $ingenio = (int)obtener_post_requerido('ingenio');

    $tipo = obtener_post_requerido('tipo');

    $curso = obtener_post_requerido('nombre_cursos');
    $jornada = obtener_post_requerido('jornada_cursos');

    $dias = obtener_post_requerido('dias');
    $horario = obtener_post_requerido('horario');

    $inicio = obtener_post_requerido('inicio');
    $fin = obtener_post_requerido('fin');

    if ($categorias <= 0 || $ingenio <= 0) {
        throw new InvalidArgumentException('Debe seleccionar una categoria y un ingenio validos.');
    }

    if (strtotime($inicio) === false || strtotime($fin) === false) {
        throw new InvalidArgumentException('Las fechas de inicio y fin no son validas.');
    }

    if ($inicio > $fin) {
        throw new InvalidArgumentException('La fecha de inicio no puede ser mayor a la fecha final.');
    }

    sincronizar_secuencia_cursos($db);

    $stmt = $db->prepare("
        INSERT INTO cursos
        (
            categoria_curso_id,
            ingenio_id,
            tipo,
            nombre_cursos,
            jornada_cursos,
            dias,
            horario,
            inicio,
            fin,
            creado
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
        )
    ");

    $resultado = $stmt->execute([
        $categorias,
        $ingenio,
        $tipo,
        $curso,
        $jornada,
        $dias,
        $horario,
        $inicio,
        $fin
    ]);

} catch (PDOException $e) {

    if ($e->getCode() === '23505') {
        try {
            sincronizar_secuencia_cursos($db);
            $resultado = $stmt->execute([
                $categorias,
                $ingenio,
                $tipo,
                $curso,
                $jornada,
                $dias,
                $horario,
                $inicio,
                $fin
            ]);

            if ($resultado) {
                $error = '';
            }
        } catch (Throwable $retryError) {
            $resultado = false;
            $error = $retryError->getMessage();
        }
    } else {
        $resultado = false;
        $error = $e->getMessage();
    }

} catch (Throwable $e) {

    $resultado = false;
    $error = $e->getMessage();

}

?>

<html lang="es">

<head>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-theme.css" rel="stylesheet">

    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</head>

<body>

<div class="container">

    <div class="row">

        <div class="row alert alert-info" style="text-align:center">

            <?php if ($resultado) { ?>

                <h3>REGISTRO GUARDADO</h3>

            <?php } else { ?>

                <h3>
                    ERROR AL GUARDAR:
                    <?php echo htmlspecialchars($error); ?>
                </h3>

            <?php } ?>

            <a href="ver_cursos.php" class="btn btn-primary">
                Regresar
            </a>

        </div>

    </div>

</div>

</body>

</html>
