<?php
session_start();
require_once("../../config/conexion.php");

if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 9) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard_pago.php");
    exit;
}

$id_solicitud = isset($_POST['id_solicitud']) ? (int) $_POST['id_solicitud'] : 0;
$id_estado_pago = isset($_POST['id_estado_pago']) ? (int) $_POST['id_estado_pago'] : 0;

if (!$id_solicitud || !$id_estado_pago) {
    header("Location: dashboard_pago.php");
    exit;
}

$conn = Conexion::conectar();
$stmt = $conn->prepare("SELECT id_estado_pago FROM solicitud_museo WHERE id_solicitud = ?");
$stmt->execute([$id_solicitud]);
$current = $stmt->fetchColumn();

if ($current !== false && $current == 1 && $id_estado_pago == 2) {
    $stmt = $conn->prepare("UPDATE solicitud_museo SET id_estado_pago = ? WHERE id_solicitud = ?");
    $stmt->execute([$id_estado_pago, $id_solicitud]);
}

header("Location: dashboard_pago.php");
exit;
