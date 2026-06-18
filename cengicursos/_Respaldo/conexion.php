<?php
function conectar()
{
    $server  = "mysql";
    $usuario = 'root';
    $pass    = "u}bp*H}rWD4-}Q4%";
    $bdd     = "cengi_cursos";
    $con     = mysqli_connect($server, $usuario, $pass, $bdd) or die("error en la conexion" . mysqli_error());
    if (!mysqli_set_charset($con, "utf8")) {
        printf("Error cargando el conjunto de caracteres utf8: %s\n", $mysqli->error);
        exit();
    }

    return $con;
}
