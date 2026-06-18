<?php
session_start();
require_once("../config/conexion.php");
require_once("../config/permisos_roles.php");

if (empty($_SESSION['es_superadmin']) || (int) $_SESSION['es_superadmin'] !== 1) {
    die("Acceso restringido");
}

$conn = Conexion::conectar();
asegurar_tablas_permisos($conn);

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID invalido");
}

$stmt = $conn->prepare("SELECT * FROM roles WHERE id = ?");
$stmt->execute([$id]);
$rol = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rol) {
    die("Rol no encontrado");
}

$error = "";
$permisos = obtener_permisos($conn);
$gruposPermisos = agrupar_permisos($permisos);
$permisosRol = obtener_permisos_rol($conn, $id);

if ($_POST) {
    $nombre = trim($_POST['nombre'] ?? '');
    $permisosSeleccionados = $_POST['permisos'] ?? [];

    if ($nombre === "") {
        $error = "El nombre no puede estar vacio";
    } else {
        $check = $conn->prepare("
            SELECT id
            FROM roles
            WHERE nombre_rol = ? AND id != ?
        ");
        $check->execute([$nombre, $id]);

        if ($check->rowCount() > 0) {
            $error = "Este rol ya existe";
        } else {
            $stmt = $conn->prepare("
                UPDATE roles
                SET nombre_rol = ?
                WHERE id = ?
            ");
            $stmt->execute([$nombre, $id]);

            guardar_permisos_rol($conn, $id, $permisosSeleccionados);

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
        <h2>Editar Rol y Permisos</h2>
        <p>Actualiza el nombre y las acciones disponibles para este rol.</p>
    </div>
</div>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<label>Nombre del rol</label>
<input
    name="nombre"
    value="<?= htmlspecialchars($rol['nombre_rol']) ?>"
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
                        <?= in_array($permiso['nombre_permiso'], $permisosRol, true) ? 'checked' : '' ?>
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

<button type="submit">Guardar Cambios</button>
</form>

<script>
function marcarPermisos(checked) {
    document.querySelectorAll('input[name="permisos[]"]').forEach(input => {
        input.checked = checked;
    });
}
</script>
