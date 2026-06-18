<?php
session_start();
require_once("../config/conexion.php");
require_once("../config/permisos_roles.php");

if (empty($_SESSION['es_superadmin']) || (int) $_SESSION['es_superadmin'] !== 1) {
    die("Acceso restringido");
}

$conn = Conexion::conectar();
asegurar_tablas_permisos($conn);

$stmt = $conn->query("
    SELECT
        r.id,
        r.nombre_rol,
        COUNT(rp.permiso_id) AS total_permisos,
        GROUP_CONCAT(p.nombre_permiso ORDER BY p.nombre_permiso SEPARATOR ', ') AS permisos
    FROM roles r
    LEFT JOIN rol_permiso rp ON rp.rol_id = r.id
    LEFT JOIN permisos p ON p.id = rp.permiso_id
    GROUP BY r.id, r.nombre_rol
    ORDER BY r.id
");
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../assets/usuarios.css">

<a href="../Menu.php" class="btn-volver">Volver</a>

<h2>Gestion de Roles</h2>

<a href="crear_rol.php" class="btn-crear">Crear Rol</a>

<table border="1">
<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Permisos asignados</th>
    <th>Acciones</th>
</tr>

<?php foreach ($roles as $r): ?>
<tr>
    <td><?= $r['id'] ?></td>
    <td><?= htmlspecialchars($r['nombre_rol']) ?></td>
    <td>
        <strong><?= (int) $r['total_permisos'] ?></strong>
        <?php
        $permisosRol = array_filter(array_map('trim', explode(',', (string) ($r['permisos'] ?? ''))));
        if ($permisosRol):
        ?>
            <div class="module-badges">
                <?php foreach ($permisosRol as $permisoNombre): ?>
                    <span class="module-badge"><?= htmlspecialchars(etiqueta_permiso($permisoNombre)) ?></span>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <span class="permisos-resumen">Sin permisos asignados</span>
        <?php endif; ?>
    </td>
    <td>
        <a href="editar_rol.php?id=<?= $r['id'] ?>" class="btn-edit">Permisos</a>

        <?php if (strtolower($r['nombre_rol']) !== 'superadmin'): ?>
            <a href="#" class="btn-delete" data-url="eliminar_rol.php?id=<?= $r['id'] ?>">Eliminar</a>
        <?php else: ?>
            Protegido
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>

<div id="deleteModal" class="modal">
  <div class="modal-content">
    <h3>Eliminar rol</h3>
    <p>Puede afectar usuarios asignados.</p>

    <div class="modal-buttons">
      <button id="cancelBtn">Cancelar</button>
      <a id="confirmDelete" href="#">Eliminar</a>
    </div>
  </div>
</div>

<script>
const modal = document.getElementById("deleteModal");
const confirmBtn = document.getElementById("confirmDelete");
const cancelBtn = document.getElementById("cancelBtn");

document.querySelectorAll(".btn-delete").forEach(btn => {
  btn.addEventListener("click", function(e) {
    e.preventDefault();
    confirmBtn.href = this.getAttribute("data-url");
    modal.classList.add("active");
  });
});

cancelBtn.onclick = () => modal.classList.remove("active");

window.onclick = (e) => {
  if (e.target === modal) modal.classList.remove("active");
};
</script>
