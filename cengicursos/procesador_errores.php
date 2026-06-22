<?php

$errores = [
    1 => "No se cargó el archivo",
    2 => "El tipo de archivo cargado es incorrecto"
];

function mensajeError($error)
{
    global $errores;

    return $errores[$error] ?? "Error desconocido";
}

?>