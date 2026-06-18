<?php
session_start();
require_once("../../config/conexion.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$conn = Conexion::conectar();

$sql = "
SELECT
    s.id_solicitud,
    so.nombre_institucion,
    so.nombre_solicitante,
    so.telefono,
    MAX(COALESCE(m.total, 0)) AS total_museo,
    s.cantidad_visitantes,
    s.fecha_visita,
    s.hora_visita,
    e.nombre_estado,
    GROUP_CONCAT(DISTINCT ai.nombre_area SEPARATOR ', ') AS areas,
    COALESCE(ep.nombre_estado_pago, 'PENDIENTE') AS nombre_estado_pago,
    m.id_estado_pago
FROM solicitudes s
INNER JOIN solicitantes so ON s.id_solicitante = so.id_solicitante
INNER JOIN estados e ON s.id_estado = e.id_estado
LEFT JOIN solicitud_areas sa ON s.id_solicitud = sa.id_solicitud
LEFT JOIN areas_interes ai ON sa.id_area = ai.id_area
INNER JOIN solicitud_museo m ON s.id_solicitud = m.id_solicitud
LEFT JOIN estado_pago ep ON m.id_estado_pago = ep.id_estado_pago
WHERE LOWER(e.nombre_estado) = 'aprobado'
GROUP BY
    s.id_solicitud,
    so.nombre_institucion,
    so.nombre_solicitante,
    so.telefono,
    s.cantidad_visitantes,
    s.fecha_visita,
    s.hora_visita,
    e.nombre_estado,
    ep.nombre_estado_pago,
    m.id_estado_pago
ORDER BY s.fecha_registro DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
$bar_colors = ['blue', 'orange', 'slate', 'teal', 'rose'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Pagos</title>
    <link rel="stylesheet" href="../../assets/dashboard.css">
    <style>
        .status-paid { color: #0d6efd; font-weight: 700; }
        .status-pending { color: #d97706; font-weight: 700; }
        .payment-row { display: flex; gap: 8px; align-items: center; }
        .payment-select { padding: 6px 10px; border-radius: 6px; border: 1px solid #ccc; }
        .payment-btn { padding: 6px 10px; border: none; border-radius: 6px; background: #2563eb; color: white; cursor: pointer; }
        .payment-btn:disabled { background: #94a3b8; cursor: not-allowed; }
    </style>
</head>
<body>

<div class="layout">

<aside class="sidebar">
    <div class="logo">CENGICAÑA</div>

    <nav>
        <a href="../../../login/Menu.php">Inicio</a>
        <a href="dashboard_pago.php" class="active">Pagos</a>
    </nav>

    <a href="../admin/logout.php" class="btn-logout">Cerrar sesión</a>
</aside>

<main class="main-content">
    <header class="dashboard-header">
        <h2>Dashboard de Pagos</h2>
        <p>Solo solicitudes aprobadas.</p>
    </header>

    <div class="card-table">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Área</th>
                    <th>Institución</th>
                    <th>Solicitante</th>
                    <th>Teléfono</th>
                    <th>Total Museo</th>
                    <th>Visitantes</th>
                    <th>Fecha & Hora</th>
                    <th>Estado</th>
                    <th>Pago</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos as $index => $row): ?>
                    <?php $color = $bar_colors[$index % count($bar_colors)]; ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id_solicitud']) ?></td>
                        <td>
                            <div class="area-cell">
                                <div class="area-bar <?= $color ?>"></div>
                                <span class="area-name"><?= htmlspecialchars($row['areas']) ?></span>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($row['nombre_institucion']) ?></td>
                        <td><?= htmlspecialchars($row['nombre_solicitante']) ?></td>
                        <td><?= htmlspecialchars($row['telefono']) ?></td>
                        <td><?= $row['total_museo'] > 0 ? 'Q ' . number_format($row['total_museo'], 2) : 'Q 0.00' ?></td>
                        <td><?= str_pad($row['cantidad_visitantes'], 2, '0', STR_PAD_LEFT) ?></td>
                        <td>
                            <div><?= htmlspecialchars($row['fecha_visita']) ?></div>
                            <div><?= htmlspecialchars($row['hora_visita']) ?></div>
                        </td>
                        <td>
                            <span class="status-approved"><?= htmlspecialchars(strtoupper($row['nombre_estado'])) ?></span>
                        </td>
                        <td>
                            <?php if (empty($row['id_estado_pago']) || $row['id_estado_pago'] == 1): ?>
                                <form method="POST" action="actualizar_pago.php" class="payment-row">
                                    <input type="hidden" name="id_solicitud" value="<?= htmlspecialchars($row['id_solicitud']) ?>">
                                    <select name="id_estado_pago" class="payment-select">
                                        <option value="1" <?= (int)$row['id_estado_pago'] !== 2 ? 'selected' : '' ?>>PENDIENTE</option>
                                        <option value="2">PAGADO</option>
                                    </select>
                                    <button type="submit" class="payment-btn">Marcar pagado</button>
                                </form>
                            <?php else: ?>
                                <span class="status-paid"><?= htmlspecialchars(strtoupper($row['nombre_estado_pago'])) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

</div>

</body>
</html>
