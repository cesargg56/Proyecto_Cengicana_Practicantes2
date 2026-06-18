<?php
session_start();
require_once("../config/conexion.php");
require_once("../config/permisos_roles.php");

if (empty($_SESSION['es_superadmin']) || (int) $_SESSION['es_superadmin'] !== 1) {
    die("Acceso restringido");
}

$conn = Conexion::conectar();
asegurar_tablas_permisos($conn);

$permisos = obtener_permisos($conn);
$gruposPermisos = agrupar_permisos($permisos);
$error = "";

if ($_POST) {
    $nombre = trim($_POST['nombre'] ?? '');
    $permisosSeleccionados = $_POST['permisos'] ?? [];

    if ($nombre === "") {
        $error = "El nombre no puede estar vacio";
    } else {
        $check = $conn->prepare("SELECT id FROM roles WHERE nombre_rol = ?");
        $check->execute([$nombre]);

        if ($check->rowCount() > 0) {
            $error = "Este rol ya existe";
        } else {
            $stmt = $conn->prepare("INSERT INTO roles (nombre_rol) VALUES (?)");
            $stmt->execute([$nombre]);

            $rolId = $conn->lastInsertId();
            guardar_permisos_rol($conn, $rolId, $permisosSeleccionados);

            header("Location: roles.php");
            exit;
        }
    }
}
?>

<link rel="stylesheet" href="../assets/usuarios.css">

<a href="roles.php" class="btn-volver">Volver</a>

<form method="POST" class="form-rol">
<div class="form-rol-header">
    <div>
        <h2>Crear Rol</h2>
        <p>Define el nombre y marca las acciones permitidas.</p>
    </div>
</div>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<label>Nombre del rol</label>
<input
    name="nombre"
    placeholder="Ej: Analista, Supervisor"
    required
>

<div class="permisos-toolbar">
    <h3>Permisos del rol</h3>
    <div>
        <button type="button" class="btn-light" onclick="marcarPermisos(true)">Seleccionar todos</button>
        <button type="button" class="btn-light" onclick="marcarPermisos(false)">Limpiar</button>
    </div>
</div>

<?php if (empty($permisos)): ?>
    <div class="empty-permissions">
        No hay permisos en la base de datos. Ejecuta primero el script de permisos en MySQL Workbench.
    </div>
<?php endif; ?>

<?php foreach ($gruposPermisos as $grupo => $items): ?>
    <section class="permiso-section">
        <div class="permiso-section-head">
            <div>
                <span class="permiso-kicker"><?= htmlspecialchars(titulo_corto_grupo_permiso($grupo)) ?></span>
                <h4><?= htmlspecialchars($grupo) ?></h4>
            </div>
            <span class="permiso-count"><?= count($items) ?> permisos</span>
        </div>
        <p class="permiso-section-copy"><?= htmlspecialchars(descripcion_grupo_permiso($grupo)) ?></p>
        <div class="permisos-grid">
            <?php foreach ($items as $permiso): ?>
                <label class="permiso-item">
                    <input
                        type="checkbox"
                        name="permisos[]"
                        value="<?= htmlspecialchars($permiso['nombre_permiso']) ?>"
                    >
                    <span>
                        <strong><?= htmlspecialchars(etiqueta_permiso($permiso['nombre_permiso'])) ?></strong>
                        <small><?= htmlspecialchars($permiso['descripcion'] ?: $permiso['nombre_permiso']) ?></small>
                    </span>
                </label>
            <?php endforeach; ?>
        </div>
    </section>
<?php endforeach; ?>

<button type="submit">Crear Rol</button>
</form>

<script>
function marcarPermisos(checked) {
    document.querySelectorAll('input[name="permisos[]"]').forEach(input => {
        input.checked = checked;
    });
}
</script>
