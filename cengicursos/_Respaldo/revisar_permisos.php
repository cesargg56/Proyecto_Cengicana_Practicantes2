<?php
if (isset($_SESSION["CMenus"])) {
    $elRol = $_SESSION["CMenus"];
    if (strcmp($elRol, 'Administrador') !== 0) {
        header("Location: Login_v6/index.php");
        exit();
    }
} else {
    header("Location: Login_v6/index.php");
    exit();
}
