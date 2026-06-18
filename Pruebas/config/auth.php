<?php
require_once __DIR__ . "/conexion.php";
require_once __DIR__ . "/PermissionManager.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login()
{
    if (!isset($_SESSION['id_usuario'])) {
        header("Location: ../../../login/login.php");
        exit;
    }

    PermissionManager::loadUserPermissions();
}

function can_access($permission)
{
    return PermissionManager::can($permission);
}

function can_access_any(array $permissions)
{
    foreach ($permissions as $permission) {
        if (can_access($permission)) {
            return true;
        }
    }

    return false;
}

function current_role_name()
{
    return strtolower(trim($_SESSION['rol'] ?? 'usuario'));
}
