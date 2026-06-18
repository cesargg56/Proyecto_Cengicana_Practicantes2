<?php
class PermissionManager {
    private static $userPermissions = null;

    /**
     * Carga los permisos del usuario actual en la sesión.
     * Debe llamarse una vez al inicio de la sesión o en el Menu.php
     */
    public static function loadUserPermissions() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idUsuario = $_SESSION['id_usuario'] ?? null;
        if (!$idUsuario) return;

        try {
            $conn = Conexion::conectarUsuariosMenu();
            
            // Obtenemos el rol del usuario y sus permisos asociados
            $sql = "
                SELECT p.nombre_permiso 
                FROM usuarios u
                JOIN roles r ON u.rol_id = r.id
                JOIN rol_permiso rp ON r.id = rp.rol_id
                JOIN permisos p ON rp.permiso_id = p.id
                WHERE u.id = ?
            ";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([$idUsuario]);
            $perms = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $_SESSION['user_permissions'] = $perms;
        } catch (Exception $e) {
            error_log("Error cargando permisos: " . $e->getMessage());
            $_SESSION['user_permissions'] = [];
        }
    }

    /**
     * Verifica si el usuario tiene un permiso específico.
     */
    public static function can($permission) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // El SuperAdmin tiene acceso total por defecto
        if (isset($_SESSION['es_superadmin']) && $_SESSION['es_superadmin'] == 1) {
            return true;
        }

        $userPermissions = $_SESSION['user_permissions'] ?? [];
        return in_array($permission, $userPermissions);
    }
}
