<?php
require_once("../config/conexion.php");

$conn = conexion::conectar();

/* =========================
   CARGAR ÁREAS
========================= */
$stmtAreas = $conn->prepare("SELECT * FROM areas_interes WHERE estado = 1");
$stmtAreas->execute();
$areas = $stmtAreas->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   CARGAR NIVELES
========================= */
$stmtNiveles = $conn->prepare("SELECT * FROM niveles_academicos WHERE estado = 1");
$stmtNiveles->execute();
$niveles = $stmtNiveles->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programa tu Visita - CENGICANA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/solicitud_visita.css">
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Programa tu Visita</h1>
        <p>Conoce nuestras instalaciones y laboratorios</p>
    </div>

    <div class="form-content">
        <?php if(isset($_GET['ok'])): ?>
            <div id="mensaje-exito" class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span>¡Solicitud enviada correctamente! En breve revisaremos tu solicitud.</span>
            </div>
        <?php endif; ?>

        <p class="subtitle">Completa los campos para procesar tu solicitud técnica.</p>
        <p class="required-note"><span class="required">*</span> Campos obligatorios</p>

        <form action="procesar_solicitud.php" method="POST" enctype="multipart/form-data">

            <!-- SECCIÓN: ÁREAS DE INTERÉS -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-leaf"></i>
                    Seleccione las áreas de interés
                </div>
                <div class="areas-grid">
                    <?php foreach($areas as $area): ?>
                        <div class="checkbox-wrapper">
                            <input type="checkbox" id="area_<?= $area['id_area'] ?>" name="areas[]" value="<?= $area['id_area'] ?>">
                            <label for="area_<?= $area['id_area'] ?>" class="checkbox-label">
                                <?= htmlspecialchars($area['nombre_area']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- SECCIÓN: INFORMACIÓN SOLICITANTE -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-user"></i>
                    Información del Solicitante
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nombre_solicitante">
                            Nombre del solicitante<span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="nombre_solicitante"
                            name="nombre_solicitante" 
                            placeholder="Nombre completo"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="institucion">
                            Nombre de la institución<span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="institucion"
                            name="institucion" 
                            placeholder="Ej: Universidad Nacional"
                            required>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: CONTACTO -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-envelope"></i>
                    Contacto
                </div>
                <div class="form-grid full">
                    <div class="form-group">
                        <label for="correo">
                            Correo electrónico<span class="required">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="correo"
                            name="correo" 
                            placeholder="tu@email.com"
                            required>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: DETALLES DE LA VISITA -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-calendar-alt"></i>
                    Detalles de la Visita
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="fecha_visita">
                            Fecha planificada<span class="required">*</span>
                        </label>
                        <input 
                            type="date" 
                            id="fecha_visita"
                            name="fecha_visita" 
                            required>
                    </div>
                    <div class="form-group">
                        <label>
                            Hora y jornada<span class="required">*</span>
                        </label>
                        <div class="time-inputs">
                            <input 
                                type="time" 
                                name="hora_visita" 
                                required>
                            <select name="periodo_jornada" required>
                                <option value="">Seleccione</option>
                                <option value="AM">AM</option>
                                <option value="PM">PM</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cantidad_visitantes">
                            Cantidad de personas<span class="required">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="cantidad_visitantes"
                            name="cantidad_visitantes" 
                            min="1"
                            placeholder="Ej: 30"
                            required>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: NIVEL ACADÉMICO -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-graduation-cap"></i>
                    Nivel Académico
                </div>
                <div class="form-grid full">
                    <div class="form-group">
                        <label for="id_nivel">
                            Nivel académico<span class="required">*</span> <small style="display: block; font-weight: normal; color: #757575; margin-top: 3px;">(si pertenece alguna institución educativa favor seleccionar alguna de las opciones, caso contario seleccionar NA)</small>
                        </label>
                        <select id="id_nivel" name="id_nivel" required>
                            <option value="">Seleccione nivel</option>
                            <?php foreach($niveles as $nivel): ?>
                                <option value="<?= $nivel['id_nivel'] ?>">
                                    <?= htmlspecialchars($nivel['nombre_nivel']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: DOCUMENTO -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-file-pdf"></i>
                    Documentación
                </div>
                <div class="form-grid full">
                    <div class="form-group">
                        <label for="carta_pdf">
                            Carta de solicitud<span class="required">*</span> <small style="display: block; font-weight: normal; color: #757575; margin-top: 3px;">Tipos aceptados: PDF, JPG, PNG (Tamaño máximo: 10 MB)</small> <small style="display: block; font-weight: normal; color: #757575; margin-top: 3px;">(Favor enviar una carta firmada explicando el motivo de la solicitud de la visita.)</small>
                        </label>
                        <div class="file-upload-wrapper">
                            <label for="carta_pdf" class="file-input-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <div class="file-input-text">
                                    <p>Subir archivo</p>
                                    <small>Arrastra tu archivo aquí o haz clic para seleccionar</small>
                                </div>
                            </label>
                            <input 
                                type="file" 
                                id="carta_pdf"
                                name="carta_pdf" 
                                accept=".pdf,.jpg,.jpeg,.png"
                                required>
                            <div class="file-name" id="file-name"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BOTONES -->
            <div class="button-group">
                <button type="button" id="btn-enviar" class="btn-submit">
                    <i class="fas fa-paper-plane" style="margin-right: 8px;"></i>Enviar Solicitud
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Gestionar nombre del archivo
    const fileInput = document.getElementById('carta_pdf');
    const fileNameDisplay = document.getElementById('file-name');

    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            fileNameDisplay.textContent = '✓ ' + this.files[0].name;
            quitarError('error-archivo');
        }
    });

    document.querySelectorAll('input[name="areas[]"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const seleccionadas = document.querySelectorAll('input[name="areas[]"]:checked');
            if (seleccionadas.length > 0) quitarError('error-areas');
        });
    });

    function mostrarError(id, despuesDe, mensaje) {
        quitarError(id);
        const error = document.createElement('p');
        error.id = id;
        error.style.cssText = 'color:#d32f2f;font-size:13px;margin-top:8px;display:flex;align-items:center;gap:6px;';
        error.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + mensaje;
        despuesDe.insertAdjacentElement('afterend', error);
    }

    function quitarError(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    document.getElementById('btn-enviar').addEventListener('click', function() {
        let hayError = false;

        // Validar áreas
        const seleccionadas = document.querySelectorAll('input[name="areas[]"]:checked');
        if (seleccionadas.length === 0) {
            mostrarError('error-areas', document.querySelector('.areas-grid'), 'Debe seleccionar al menos un área de interés.');
            hayError = true;
        } else {
            quitarError('error-areas');
        }

        // Validar archivo
        if (!fileInput.files || fileInput.files.length === 0) {
            mostrarError('error-archivo', document.querySelector('.file-upload-wrapper'), 'Debe adjuntar la carta de solicitud.');
            hayError = true;
        } else {
            quitarError('error-archivo');
        }

        if (hayError) {
            const primerError = document.querySelector('#error-areas, #error-archivo');
            if (primerError) primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        // Si todo está bien, enviar el formulario
        document.querySelector('form').submit();
    });

    // Desaparecer mensaje de éxito
    setTimeout(() => {
        const mensaje = document.getElementById("mensaje-exito");
        if (mensaje) {
            mensaje.style.transition = "opacity 0.5s ease";
            mensaje.style.opacity = "0";
            setTimeout(() => {
                mensaje.remove();
                window.history.replaceState({}, document.title, window.location.pathname);
            }, 500);
        }
    }, 3500);
</script>

</body>
</html>