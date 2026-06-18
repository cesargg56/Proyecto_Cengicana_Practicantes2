<?php
session_start();
require_once "../config/conexion.php";

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit;
}

function user_scope_config($scope)
{
    $scope = strtolower(trim((string) $scope));

    $configs = [
        'cursos' => [
            'module_names' => ['cursos', 'cengicursos'],
            'return_url' => '../../cengicursos/ver_usuarios.php',
            'title' => 'Usuarios del modulo Cursos',
        ],
        'visitas' => [
            'module_names' => ['solicitud de visitas'],
            'return_url' => '../../Pruebas/public/admin/dashboard_unificado.php?modulo=usuarios',
            'title' => 'Usuarios del modulo Solicitud de visitas',
        ],
    ];

    return $configs[$scope] ?? null;
}

$conn = Conexion::conectar();
$idUsuario = (int) $_SESSION['id_usuario'];
$esSuperadmin = (int) ($_SESSION['es_superadmin'] ?? 0) === 1;
$scope = $_GET['scope'] ?? '';
$scopeConfig = user_scope_config($scope);

if (!$esSuperadmin && !$scopeConfig) {
    header("Location: ../Menu.php");
    exit;
}

$params = [];
$where = [];

if ($scopeConfig) {
    $placeholders = implode(',', array_fill(0, count($scopeConfig['module_names']), '?'));
    $where[] = "EXISTS (
        SELECT 1
        FROM usuario_modulo umf
        INNER JOIN modulos mf ON mf.id = umf.modulo_id
        WHERE umf.usuario_id = u.id
          AND LOWER(mf.nombre) IN ($placeholders)
    )";
    $params = array_merge($params, $scopeConfig['module_names']);
}

if (!$esSuperadmin && !$scopeConfig) {
    $where[] = "EXISTS (
        SELECT 1
        FROM usuario_modulo umscope
        WHERE umscope.usuario_id = u.id
          AND umscope.modulo_id IN (
              SELECT modulo_id FROM usuario_modulo WHERE usuario_id = ?
          )
    )";
    $params[] = $idUsuario;
}

$sql = "
    SELECT
        u.id,
        u.nombre,
        u.correo,
        r.nombre_rol,
        COALESCE(i.nombre_ingenio, 'Sin ingenio') AS ingenio,
        COALESCE(GROUP_CONCAT(DISTINCT m.nombre ORDER BY m.nombre SEPARATOR ', '), 'Sin modulo') AS modulos
    FROM usuarios u
    INNER JOIN roles r ON r.id = u.rol_id
    LEFT JOIN ingenios i ON i.id = u.ingenio_id
    LEFT JOIN usuario_modulo um ON um.usuario_id = u.id
    LEFT JOIN modulos m ON m.id = um.modulo_id
";

if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}

$sql .= "
    GROUP BY u.id, u.nombre, u.correo, r.nombre_rol, i.nombre_ingenio
    ORDER BY u.nombre
";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$titulo = $scopeConfig['title'] ?? 'Usuarios del sistema';
?>

<link rel="stylesheet" href="../assets/usuarios.css">

<a href="../Menu.php" class="btn-volver">Volver</a>

<h2><?php echo htmlspecialchars($titulo); ?></h2>

<a href="crear_usuario.php<?php echo $scope ? '?scope=' . urlencode($scope) : ''; ?>">Crear usuario</a>

<table border="1">
<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Correo</th>
    <th>Ingenio</th>
    <th>Rol</th>
    <th>Modulos</th>
    <th>Acciones</th>
</tr>

<?php foreach ($usuarios as $usuario): ?>
<tr>
    <td><?php echo (int) $usuario['id']; ?></td>
    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
    <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
    <td><?php echo htmlspecialchars($usuario['ingenio']); ?></td>
    <td><?php echo htmlspecialchars($usuario['nombre_rol']); ?></td>
    <td><?php echo htmlspecialchars($usuario['modulos']); ?></td>
    <td>
        <a href="editar_usuario.php?id=<?php echo (int) $usuario['id']; ?><?php echo $scope ? '&scope=' . urlencode($scope) : ''; ?>">Editar</a>
        <?php if (strtolower((string) $usuario['nombre_rol']) !== 'superadmin'): ?>
            <a href="#" class="btn-delete" data-id="<?php echo (int) $usuario['id']; ?>">Eliminar</a>
        <?php else: ?>
            <span>Bloqueado</span>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>

<div id="deleteModal" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);display:none;justify-content:center;align-items:center;z-index:9999;">
    <div style="background:white;padding:30px;border-radius:15px;width:320px;text-align:center;box-shadow:0 20px 50px rgba(0,0,0,0.3);">
        <h3>Eliminar usuario?</h3>
        <p>Esta accion no se puede deshacer.</p>

        <div style="margin-top:20px;display:flex;gap:10px;">
            <button id="cancelBtn" style="flex:1;padding:10px;border:none;background:#ccc;border-radius:8px;cursor:pointer;">Cancelar</button>
            <a id="confirmDelete" href="#" style="flex:1;padding:10px;background:#e74c3c;color:white;text-decoration:none;border-radius:8px;display:flex;justify-content:center;align-items:center;">Eliminar</a>
        </div>
    </div>
</div>

<script>
const modal = document.getElementById("deleteModal");
const confirmBtn = document.getElementById("confirmDelete");
const cancelBtn = document.getElementById("cancelBtn");

document.querySelectorAll(".btn-delete").forEach((btn) => {
    btn.addEventListener("click", function (e) {
        e.preventDefault();
        confirmBtn.href = "eliminar_usuario.php?id=" + this.getAttribute("data-id");
        modal.style.display = "flex";
    });
});

cancelBtn.addEventListener("click", () => {
    modal.style.display = "none";
});

window.addEventListener("click", (e) => {
    if (e.target === modal) {
        modal.style.display = "none";
    }
});
</script>
