<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("../../config/conexion.php");

if (!isset($_SESSION['usuario'])) {
header("Location: ../../../login/login.php");
exit;
}

$rol_usuario = strtolower($_SESSION['rol'] ?? 'usuario');
$rol_usuario = str_replace(['ó','á','é','í','ú'], ['o','a','e','i','u'], $rol_usuario);

$conn = conexion::conectar();


// =====================
// 📊 VARIABLES
// =====================
$totalUsuarios = 0;
$totalAreas = 0;
$pendientes = 0;
$aprobadas = 0;
$rechazadas = 0;

// =====================
// 👑 SUPERADMIN
// =====================
if ($rol_usuario === 'superadmin') {

    // 👥 Usuarios (otra BD si aplica)
    try {
        $connUsuarios = Conexion::conectarUsuariosMenu();
        $totalUsuarios = $connUsuarios->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    } catch(Exception $e) {
        $totalUsuarios = 0;
    }

    // 📍 Áreas
    $totalAreas = $conn->query("SELECT COUNT(*) FROM areas_interes WHERE estado=1")->fetchColumn();

    // 📊 Estados
    $sql = "
    SELECT e.nombre_estado, COUNT(*) as total
    FROM solicitudes s
    JOIN estados e ON s.id_estado = e.id_estado
    GROUP BY e.nombre_estado
    ";

    foreach($conn->query($sql) as $row){
        if ($row['nombre_estado'] == 'ENVIADO') $pendientes = $row['total'];
        if ($row['nombre_estado'] == 'APROBADO') $aprobadas = $row['total'];
        if ($row['nombre_estado'] == 'RECHAZADO') $rechazadas = $row['total'];
    }
}

// =====================
// 🧑‍💼 ADMIN
// =====================
elseif ($rol_usuario === 'administrador') {

    $aprobadas = $conn->query("
        SELECT COUNT(*)
        FROM solicitudes s
        JOIN estados e ON s.id_estado = e.id_estado
        WHERE e.nombre_estado = 'APROBADO'
    ")->fetchColumn();
}

// =====================
// 👤 USUARIO
// =====================
else {

    $sql = "
    SELECT e.nombre_estado, COUNT(*) as total
    FROM solicitudes s
    JOIN estados e ON s.id_estado = e.id_estado
    WHERE e.nombre_estado IN ('ENVIADO','RECHAZADO')
    GROUP BY e.nombre_estado
    ";

    foreach($conn->query($sql) as $row){
        if ($row['nombre_estado'] == 'ENVIADO') $pendientes = $row['total'];
        if ($row['nombre_estado'] == 'RECHAZADO') $rechazadas = $row['total'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Inicio Dashboard</title>
<link rel="stylesheet" href="../../assets/inicio.css">
</head>

<body>

<div class="layout">

    <aside class="sidebar">
        <div class="logo">CENGICAÑA</div>

<nav>

    <!-- 🔹 INICIO (TODOS) -->
    <a href="inicioad.php" class="<?= $modulo=='inicio' ? 'active' : '' ?>">
        Inicio
    </a>

    <!-- 👑 SUPERADMIN -->
    <?php if ($rol_usuario === 'superadmin'): ?>

        <a href="superadmin.php?modulo=usuarios" class="<?= $modulo=='usuarios' ? 'active' : '' ?>">
            Usuarios
        </a>

        <a href="superadmin.php?modulo=areas" class="<?= $modulo=='areas' ? 'active' : '' ?>">
            Áreas
        </a>

        <a href="superadmin.php?modulo=solicitudes" class="<?= $modulo=='solicitudes' ? 'active' : '' ?>">
            Solicitudes
        </a>

    <!-- 🧑‍💼 ADMIN -->
    <?php elseif ($rol_usuario === 'administrador'): ?>

        <a href="dashboard_AP.php" class="<?= $modulo=='admin' ? 'active' : '' ?>">
        Solicitudes
        </a>

    <!-- 👤 USUARIO -->
    <?php else: ?>

        <a href="dashboard.php" class="<?= $modulo=='usuario' ? 'active' : '' ?>">
        Solicitudes
    </a>

    <?php endif; ?>

</nav>

        <a href="../admin/logout.php" class="btn-logout">
            Cerrar sesión
        </a>
    </aside>

    <main class="main-content">

        <header class="dashboard-header">
            <h2>Dashboard General</h2>
        </header>

        <div class="cards">

        <?php if ($rol_usuario === 'superadmin'): ?>

   <div class="cards">

    <div class="card green">
        <div class="icon">👥</div>
        <h3>Usuarios</h3>
        <h2><?= $totalUsuarios ?></h2>
    </div>

    <div class="card blue">
        <div class="icon">📍</div>
        <h3>Áreas</h3>
        <h2><?= $totalAreas ?></h2>
    </div>

    <div class="card orange">
        <div class="icon">⏳</div>
        <h3>Pendientes</h3>
        <h2><?= $pendientes ?></h2>
    </div>

    <div class="card blue">
        <div class="icon">✅</div>
        <h3>Aprobadas</h3>
        <h2><?= $aprobadas ?></h2>
    </div>

    <div class="card red">
        <div class="icon">❌</div>
        <h3>Rechazadas</h3>
        <h2><?= $rechazadas ?></h2>
    </div>

</div>

        <?php elseif ($rol_usuario === 'administrador'): ?>

            <div class="card blue">
                <h3>Solicitudes Aprobadas</h3>
                <h2><?= $aprobadas ?></h2>
            </div>

        <?php else: ?>

            <div class="card orange">
                <h3>Pendientes</h3>
                <h2><?= $pendientes ?></h2>
            </div>

            <div class="card red">
                <h3>Rechazadas</h3>
                <h2><?= $rechazadas ?></h2>
            </div>

        <?php endif; ?>

        </div>

    </main>
</div>

</body>
</html>