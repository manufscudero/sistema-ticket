<?php
require_once __DIR__ . '/../layouts/header.php';
try {
    $db = Database::getConnection();

    $pisos = $db->query("SELECT * FROM pisos ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    $dependencias = $db->query("SELECT * FROM dependencias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
    $categorias = $db->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $pisos = [];
    $dependencias = [];
    $categorias = [];
}
?>

<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&display=swap" rel="stylesheet">

<div class="grid-overlay"></div>

<canvas id="particles-canvas"></canvas>

<div class="container py-5 position-relative" style="z-index: 1;">
    <div class="row justify-content-center">
        <div class="text-center mb-2">
            <div class="d-inline-flex justify-content-center">
                <img src="<?= BASE_URL ?>/assets/img/uticket_logo.png" alt="UTicket" style="max-height: 40px; width: auto; object-fit: contain;">
            </div>
            <p class="text-white-50 m-0 mt-1" style="font-size: 0.78rem;">Sistema de Gestión de Requerimientos Técnicos</p>
        </div>

        <!-- Tarjeta Principal -->
        <div class="main-card p-4 p-md-5">

            <?php if ($flash = Session::getFlash()): ?>
                <div class="alert alert-dismissible fade show text-light border border-danger border-opacity-50 mb-4" style="background-color: #7f1d1d;" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2 text-danger"></i>
                    <span>
                        <?php
                        if (is_array($flash)) {
                            echo htmlspecialchars($flash['message'] ?? '');
                        } else {
                            echo htmlspecialchars($flash);
                        }
                        ?>
                    </span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <form action="<?= BASE_URL ?>/ticket/store" method="POST" enctype="multipart/form-data">

                <h5 class="fw-bold mb-3">Datos del Solicitante</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="solicitante_cedula" class="form-label form-label-custom">Número de Cédula *</label>
                        <input type="text" class="form-control form-control-modern" id="solicitante_cedula" name="solicitante_cedula" required placeholder="Ej: 1234567">
                    </div>
                    <div class="col-md-6">
                        <label for="solicitante_nombre" class="form-label form-label-custom">Nombre y Apellido *</label>
                        <input type="text" class="form-control form-control-modern" id="solicitante_nombre" name="solicitante_nombre" required placeholder="Ingrese su nombre completo">
                    </div>
                </div>

                <h5 class="fw-bold mb-3">Ubicación y Área</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="piso_id" class="form-label form-label-custom">Piso / Nivel *</label>
                        <select class="form-select form-select-modern" id="piso_id" required>
                            <option value="">Seleccione un piso...</option>
                            <?php foreach ($pisos as $piso): ?>
                                <option value="<?= $piso['id'] ?>"><?= htmlspecialchars($piso['nombre']) ?></option>
                            <?php endforeach; ?>
                            <option value="otro">Otro / No listado</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="dependencia_id" class="form-label form-label-custom">Dependencia / Oficina *</label>

                        <!-- Select normal de dependencias -->
                        <select class="form-select form-select-modern" id="dependencia_id" name="dependencia_id" required>
                            <option value="">Seleccione una dependencia...</option>
                            <?php foreach ($dependencias as $dep): ?>
                                <option value="<?= $dep['id'] ?>" data-piso="<?= $dep['piso_id'] ?>">
                                    <?= htmlspecialchars($dep['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <input type="text" class="form-control form-control-modern d-none" id="dependencia_texto" name="dependencia_texto" placeholder="Escriba el nombre de su oficina o dependencia">
                    </div>
                </div>

                <h5 class="fw-bold mb-3">Detalle del Requerimiento</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label for="categoria_id" class="form-label form-label-custom">Categoría del Incidente *</label>
                        <select class="form-select form-select-modern" id="categoria_id" name="categoria_id" required>
                            <option value="">Seleccione una categoría...</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="descripcion" class="form-label form-label-custom">Descripción del Problema *</label>
                        <textarea class="form-control form-control-modern" id="descripcion" name="descripcion" rows="3" placeholder="Describa brevemente el problema técnico o solicitud..." required></textarea>
                    </div>
                </div>

                <!-- CAPTCHA LOCAL -->
                <div class="row g-3 mb-4 align-items-center">
                    <div class="col-md-6">
                        <label for="captcha_input" class="form-label form-label-custom d-flex align-items-center">
                            Inserte el Código
                            <span class="ms-2 px-3 py-1 rounded text-white fw-bold shadow-sm" style="background-color: #00aeff; font-size: 1.1rem; letter-spacing: 2px;">
                                <?= Session::get('captcha_code') ?>
                            </span>
                        </label>
                        <input type="text" class="form-control form-control-modern" id="captcha_input" name="captcha_input" required placeholder="Ingrese el código de seguridad">
                    </div>
                </div>

                <div class="d-grid gap-3 d-md-flex justify-content-md-end pt-2">
                    <a href="<?= BASE_URL ?>/ticket/consultar" class="btn btn-clear align-self-center">Consultar Mi Ticket</a>
                    <button type="submit" class="btn btn-modern px-4"><i class="bi bi-send me-2"></i>Enviar Solicitud</button>
                </div>

            </form>

        </div>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pisoSelect = document.getElementById('piso_id');
        const dependenciaSelect = document.getElementById('dependencia_id');
        const dependenciaTexto = document.getElementById('dependencia_texto');
        const cedulaInput = document.getElementById('solicitante_cedula');
        const nombreInput = document.getElementById('solicitante_nombre');

        // Memorizar opciones originales
        const opcionesDependencia = Array.from(dependenciaSelect.querySelectorAll('option')).slice(1);
        dependenciaSelect.disabled = true;

        // actualizar las dependencias segun el piso seleccionado
        function filtrarDependencias(pisoId, callback) {
            if (pisoId === "otro") {
                dependenciaSelect.classList.add('d-none');
                dependenciaSelect.disabled = true;
                dependenciaSelect.required = false;

                dependenciaTexto.classList.remove('d-none');
                dependenciaTexto.required = true;
                if (callback) callback();
                return;
            }

            dependenciaTexto.classList.add('d-none');
            dependenciaTexto.required = false;
            dependenciaTexto.value = '';

            dependenciaSelect.classList.remove('d-none');
            dependenciaSelect.required = true;
            dependenciaSelect.innerHTML = '<option value="">Seleccione una dependencia...</option>';

            if (pisoId === "") {
                dependenciaSelect.disabled = true;
                if (callback) callback();
                return;
            }

            dependenciaSelect.disabled = false;
            opcionesDependencia.forEach(option => {
                if (option.getAttribute('data-piso') === pisoId) {
                    dependenciaSelect.appendChild(option);
                }
            });

            if (callback) callback();
        }

        // Manejo de filtrado de pisos y opción  otro por selección del usuario
        pisoSelect.addEventListener('change', function() {
            filtrarDependencias(this.value);
            if (this.value === "otro") {
                dependenciaTexto.focus();
            }
        });

        // Autocompletar datos al salir del campo de Cédula
        if (cedulaInput) {
            cedulaInput.addEventListener('blur', function() {
                let cedula = this.value.trim();
                if (cedula.length < 3) return;

                fetch(`<?= BASE_URL ?>/ticket/buscar-por-cedula?cedula=${encodeURIComponent(cedula)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.encontrado) {
                            if (data.nombre && nombreInput) {
                                nombreInput.value = data.nombre;
                            }

                            if (data.piso_id) {
                                pisoSelect.value = data.piso_id;

                                filtrarDependencias(String(data.piso_id), () => {
                                    if (data.dependencia_id) {
                                        dependenciaSelect.value = data.dependencia_id;
                                    }
                                });
                            }
                        }
                    })
                    .catch(error => console.error('Error al buscar los datos de la cédula:', error));
            });
        }

        document.body.classList.add('page-fixed');
    });
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>