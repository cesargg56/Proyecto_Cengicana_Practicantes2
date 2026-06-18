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
            'label' => 'Cengicursos',
        ],
        'visitas' => [
            'module_names' => ['solicitud de visitas'],
            'return_url' => '../../Pruebas/public/admin/dashboard_unificado.php?modulo=usuarios',
            'label' => 'Solicitud de visitas',
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
    header("Location: usuarios.php");
    exit;
}

$modulosActualesUsuario = [];
if (!$esSuperadmin) {
    $stmtAdminMod = $conn->prepare("
        SELECT modulo_id
        FROM usuario_modulo
        WHERE usuario_id = ?
    ");
    $stmtAdminMod->execute([$idUsuario]);
    $modulosActualesUsuario = array_map('intval', $stmtAdminMod->fetchAll(PDO::FETCH_COLUMN));
}

$modulosDisponibles = [];
$moduloIdsForzados = [];

if ($scopeConfig) {
    $placeholders = implode(',', array_fill(0, count($scopeConfig['module_names']), '?'));
    $stmtScopeMods = $conn->prepare("
        SELECT id, nombre
        FROM modulos
        WHERE LOWER(nombre) IN ($placeholders)
        ORDER BY nombre
    ");
    $stmtScopeMods->execute($scopeConfig['module_names']);
    $modulosDisponibles = $stmtScopeMods->fetchAll(PDO::FETCH_ASSOC);
    $moduloIdsForzados = array_map(fn($mod) => (int) $mod['id'], $modulosDisponibles);
} elseif ($esSuperadmin) {
    $stmtMod = $conn->query("SELECT id, nombre FROM modulos ORDER BY nombre");
    $modulosDisponibles = $stmtMod->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmtMod = $conn->prepare("
        SELECT id, nombre
        FROM modulos
        WHERE id IN (" . implode(',', array_fill(0, max(1, count($modulosActualesUsuario)), '?')) . ")
        ORDER BY nombre
    ");
    $stmtMod->execute($modulosActualesUsuario ?: [0]);
    $modulosDisponibles = $stmtMod->fetchAll(PDO::FETCH_ASSOC);
    $moduloIdsForzados = $modulosActualesUsuario;
}

$destinoRegreso = $scopeConfig['return_url'] ?? 'usuarios.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $contrasenaPlano = trim($_POST['contrasena'] ?? '');
    $rolId = (int) ($_POST['rol_id'] ?? 0);
    $ingenioId = $_POST['ingenio_id'] !== '' ? (int) $_POST['ingenio_id'] : null;

    if ($scopeConfig || !$esSuperadmin) {
        $moduloIdsSeleccionados = $moduloIdsForzados;
    } else {
        $moduloIdsSeleccionados = array_map('intval', $_POST['modulo_ids'] ?? []);
    }

    if ($nombre === '' || $correo === '' || $contrasenaPlano === '' || $rolId <= 0 || $ingenioId === null) {
        $error = "Completa todos los campos obligatorios.";
    } else {
        $check = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $check->execute([$correo]);

        if ($check->fetchColumn()) {
            $error = "El correo ya esta registrado.";
        } else {
            $esSuperadminNuevo = $rolId === 1 ? 1 : 0;
            $contrasenaHash = password_hash($contrasenaPlano, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("
                INSERT INTO usuarios (nombre, correo, contrasena, rol_id, ingenio_id, es_superadmin)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $nombre,
                $correo,
                $contrasenaHash,
                $rolId,
                $ingenioId,
                $esSuperadminNuevo,
            ]);

            $usuarioId = (int) $conn->lastInsertId();

            if (!$esSuperadminNuevo && !empty($moduloIdsSeleccionados)) {
                $stmtModulo = $conn->prepare("
                    INSERT INTO usuario_modulo (usuario_id, modulo_id)
                    VALUES (?, ?)
                ");

                foreach (array_unique($moduloIdsSeleccionados) as $moduloId) {
                    $stmtModulo->execute([$usuarioId, (int) $moduloId]);
                }
            }

            header("Location: {$destinoRegreso}");
            exit;
        }
    }
}

if ($esSuperadmin) {
    $stmtRoles = $conn->query("SELECT id, nombre_rol FROM roles ORDER BY nombre_rol");
} else {
    $stmtRoles = $conn->query("SELECT id, nombre_rol FROM roles WHERE id != 1 ORDER BY nombre_rol");
}
$roles = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);

$stmtIngenios = $conn->query("SELECT id, nombre_ingenio FROM ingenios ORDER BY nombre_ingenio");
$ingenios = $stmtIngenios->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../assets/usuarios.css">

<a href="<?php echo htmlspecialchars($destinoRegreso); ?>" class="btn-volver">Volver</a>

<?php if (!empty($error)): ?>
    <p style="color:#b42318;font-weight:700;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="POST">
    <h2>Crear usuario</h2>

    <input type="text" name="nombre" placeholder="Nombre" value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>" required>
    <input type="email" name="correo" placeholder="Correo" value="<?php echo htmlspecialchars($_POST['correo'] ?? ''); ?>" required>
    <input type="password" name="contrasena" placeholder="Contrasena" required>

    <label>Rol</label>
    <select name="rol_id" id="rolSelect" required>
        <option value="">Seleccione un rol</option>
        <?php foreach ($roles as $rol): ?>
            <option value="<?php echo (int) $rol['id']; ?>" <?php echo ((int) ($rol['id']) === (int) ($_POST['rol_id'] ?? 0)) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($rol['nombre_rol']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Ingenio asignado</label>
    <select name="ingenio_id" required>
        <option value="">Seleccione un ingenio</option>
        <?php foreach ($ingenios as $ingenio): ?>
            <option value="<?php echo (int) $ingenio['id']; ?>" <?php echo ((int) ($ingenio['id']) === (int) ($_POST['ingenio_id'] ?? 0)) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($ingenio['nombre_ingenio']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <p class="form-help">Este campo es obligatorio para que los filtros por ingenio funcionen correctamente.</p>

    <?php if ($scopeConfig): ?>
        <label>Modulo asignado</label>
        <div class="module-badges">
            <?php foreach ($modulosDisponibles as $modulo): ?>
                <span class="module-badge"><?php echo htmlspecialchars($modulo['nombre']); ?></span>
            <?php endforeach; ?>
        </div>
        <p class="form-help">Este usuario se guardara automaticamente en el modulo <?php echo htmlspecialchars($scopeConfig['label']); ?>.</p>
    <?php elseif ($esSuperadmin): ?>
        <div id="moduloContainer">
            <label>Modulos asignados</label>
            <select name="modulo_ids[]" multiple size="6" class="multi-select">
                <?php foreach ($modulosDisponibles as $modulo): ?>
                    <option value="<?php echo (int) $modulo['id']; ?>">
                        <?php echo htmlspecialchars($modulo['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="form-help">Mantiene presionada la tecla Ctrl para seleccionar mas de un modulo.</p>
        </div>
    <?php elseif (!empty($modulosDisponibles)): ?>
        <label>Modulo asignado</label>
        <div class="module-badges">
            <?php foreach ($modulosDisponibles as $modulo): ?>
                <span class="module-badge"><?php echo htmlspecialchars($modulo['nombre']); ?></span>
            <?php endforeach; ?>
        </div>
        <p class="form-help">Como administrador de modulo, solo puedes crear usuarios para tu propio modulo.</p>
    <?php endif; ?>

    <button type="submit">Crear usuario</button>
</form>

<?php if ($esSuperadmin && !$scopeConfig): ?>
<script>
document.getElementById("rolSelect").addEventListener("change", function () {
    const moduloDiv = document.getElementById("moduloContainer");
    if (!moduloDiv) return;
    moduloDiv.style.display = this.value === "1" ? "none" : "block";
});
</script>
<?php endif; ?>
