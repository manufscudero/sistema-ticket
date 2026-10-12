<?php
// Obtener categorías, pisos y dependencias para los desplegables del formulario
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

<!-- Importar Fuente -->
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&display=swap" rel="stylesheet">

<div class="grid-overlay"></div>

<!-- Partículas -->
<canvas id="particles-canvas"></canvas>

<div class="container py-5 position-relative" style="z-index: 1;">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="text-center mb-4">
                <h2 class="hero-title">Sistema de Tickets - UTIC</h2>
                <!-- <p class="text-hero-subtitle mb-0">Sistema de Tickets</p> -->
            </div>

            <!-- TARJETA PRINCIPAL -->
            <div class="main-card p-4 p-md-5">

                <?php if (class_exists('Session') && Session::getFlash('danger')): ?>
                    <div class="alert alert-custom p-3 mb-4 role=" alert">
                        <?= Session::getFlash('danger') ?>
                        <button type="button" class="btn-close float-end" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>/ticket/guardar" method="POST" enctype="multipart/form-data">

                    <h5 class="fw-bold mb-3"><i class="bi bi-person text-accent me-2"></i>Datos del Solicitante</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label for="solicitante_nombre" class="form-label form-label-custom">Nombre y Apellido *</label>
                            <input type="text" class="form-control form-control-modern" id="solicitante_nombre" name="solicitante_nombre" required placeholder="Ingrese su nombre completo">
                        </div>
                    </div>

                    <!-- <hr class="my-4""> -->

                    <h5 class="fw-bold mb-3"><i class="bi bi-geo-alt text-accent me-2"></i>Ubicación y Área</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="piso_id" class="form-label form-label-custom">Piso / Nivel *</label>
                            <select class="form-select form-select-modern" id="piso_id" required>
                                <option value="">Seleccione un piso...</option>
                                <?php foreach ($pisos as $piso): ?>
                                    <option value="<?= $piso['id'] ?>"><?= htmlspecialchars($piso['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="dependencia_id" class="form-label form-label-custom">Dependencia / Oficina *</label>
                            <select class="form-select form-select-modern" id="dependencia_id" name="dependencia_id" required>
                                <option value="">Seleccione una dependencia...</option>
                                <?php foreach ($dependencias as $dep): ?>
                                    <option value="<?= $dep['id'] ?>" data-piso="<?= $dep['piso_id'] ?>">
                                        <?= htmlspecialchars(mb_convert_encoding($dep['nombre'], 'ISO-8859-1', 'UTF-8')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- <hr class="my-4"> -->

                    <h5 class="fw-bold mb-3"><i class="bi bi-tools text-accent me-2"></i>Detalle del Requerimiento</h5>
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
                            <textarea class="form-control form-control-modern" id="descripcion" name="descripcion" rows="4" placeholder="Describa brevemente el problema técnico o solicitud..." required></textarea>
                        </div>
                    </div>

                    <div class="d-grid gap-3 d-md-flex justify-content-md-end pt-2">
                        <a href="<?= BASE_URL ?>/seguimiento" class="btn btn-clear align-self-center">Consultar Mi Ticket</a>
                        <button type="submit" class="btn btn-modern px-4"><i class="bi bi-send me-2"></i>Enviar Solicitud</button>
                    </div>
                    
                    
                </form>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.body.classList.add('page-fixed');
</script>
<?php 
require_once __DIR__ . '/../layouts/footer.php'; 
?>