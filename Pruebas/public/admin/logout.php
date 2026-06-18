<?php
session_start();

// destruir todas las variables de sesión
$_SESSION = [];

// destruir la sesión
session_destroy();

// redirigir al login
header("Location: ../../../login/login.php");
exit;