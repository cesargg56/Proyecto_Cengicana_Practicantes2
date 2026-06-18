<?php
session_start();
require_once("../config/conexion.php");

$conn = Conexion::conectar();

// 🔥 OBTENER MODULOS
$stmt = $conn->query("SELECT * FROM modulos");
$modulos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../assets/usuarios.css">

<a href="../Menu.php" class="btn-volver">← Volver</a>

<h2>Módulos del sistema</h2>

<a href="crear_modulo.php">➕ Crear Módulo</a>

<table border="1">
<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Acciones</th>
</tr>

<?php foreach ($modulos as $m): ?>
<tr>
    <td><?= $m['id'] ?></td>
    <td><?= $m['nombre'] ?></td>
    <td>
        <a href="editar_modulo.php?id=<?= $m['id'] ?>">✏️</a>
        <a href="#" class="btn-delete" data-url="eliminar_modulo.php?id=<?= $m['id'] ?>">🗑️</a>
    </td>
</tr>
<?php endforeach; ?>
</table>

<!-- 🔥 MODAL LIMPIO (SIN style) -->
<div id="deleteModal" class="modal">
  <div class="modal-content">
    <h3>¿Eliminar módulo?</h3>
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

    // 🔥 usamos data-url (mejor práctica)
    confirmBtn.href = this.getAttribute("data-url");

    modal.classList.add("active");
  });
});

cancelBtn.onclick = () => modal.classList.remove("active");

window.onclick = (e) => {
  if (e.target === modal) modal.classList.remove("active");
};
</script>