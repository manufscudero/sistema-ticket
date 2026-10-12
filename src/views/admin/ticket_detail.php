<?php
$ticket = $ticket ?? [];
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar_admin.php';
?>
<div class="grid-overlay"></div>
<div class="container pb-5">
    <a href="<?= BASE_URL ?>/admin/tickets" class="btn btn-sm btn-outline-secondary mb-3"><i class="fa-solid fa-arrow-left me-1"></i> Volver al listado</a>

    <?php if ($flash = Session::getFlash()): ?>
        <div class="alert alert-<?= $flash['type'] ?> bg-opacity-25 text-white border-0 mb-3">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Información del Ticket -->
        <div class="col-md-7">
            <div class="card glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold text-info mb-0"><?= $ticket['codigo_ticket'] ?></h4>
                    <span class="badge bg-secondary"><?= $ticket['gravedad'] ?> Prioridad</span>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">Solicitante</small>
                    <strong class="text-white fs-5"><?= htmlspecialchars($ticket['solicitante_nombre']) ?></strong>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <small class="text-muted d-block">Ubicación</small>
                        <span><?= htmlspecialchars($ticket['piso']) ?></span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Dependencia</small>
                        <span><?= htmlspecialchars($ticket['dependencia']) ?></span>
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">Categoría</small>
                    <span class="badge bg-dark border border-secondary"><?= htmlspecialchars($ticket['categoria']) ?></span>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">Descripción del Problema</small>
                    <div class="p-3 rounded bg-dark bg-opacity-50 text-light border border-secondary border-opacity-25 mt-1">
                        <?= nl2br(htmlspecialchars($ticket['descripcion'])) ?>
                    </div>
                </div>

                <div class="small text-muted border-top border-secondary border-opacity-25 pt-2">
                    Creado el: <?= date('d/m/Y H:i', strtotime($ticket['fecha_creacion'])) ?>
                </div>
            </div>
        </div>

        <!-- Gestión / Actualización de Estado -->
        <div class="col-md-5">
            <div class="card glass-card p-4">
                <h5 class="fw-bold text-white mb-3"><i class="fa-solid fa-sliders text-info me-2"></i>Gestión Técnica</h5>

                <form action="<?= BASE_URL ?>/admin/ticket/actualizar" method="POST">
                    <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">

                    <div class="mb-3">
                        <label for="estado" class="form-label text-muted small">Estado del Ticket</label>
                        <select name="estado" id="estado" class="form-select">
                            <option value="Nuevo" <?= $ticket['estado'] === 'Nuevo' ? 'selected' : '' ?>>Nuevo</option>
                            <option value="En Proceso" <?= $ticket['estado'] === 'En Proceso' ? 'selected' : '' ?>>En Proceso</option>
                            <option value="Resuelto" <?= $ticket['estado'] === 'Resuelto' ? 'selected' : '' ?>>Resuelto</option>
                            <option value="Cerrado" <?= $ticket['estado'] === 'Cerrado' ? 'selected' : '' ?>>Cerrado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tecnico_id" class="form-label text-muted small">Técnico Asignado / Solucionador</label>
                        <select name="tecnico_id" id="tecnico_id" class="form-select">
                            <option value="">-- Seleccionar Técnico --</option>
                            <?php
                            $tecnicosList = $tecnicos ?? [];
                            foreach ($tecnicosList as $tec):
                            ?>
                                <option value="<?= $tec['id'] ?>" <?= (isset($ticket['tecnico_id']) && $ticket['tecnico_id'] == $tec['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tec['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="observacion_tecnica" class="form-label text-muted small">Observación / Solución Técnica</label>
                        <textarea name="observacion_tecnica" id="observacion_tecnica" class="form-control" rows="5" placeholder="Detalle la solución aplicada o motivos de demora..."><?= htmlspecialchars($ticket['observacion_tecnica'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-info text-dark w-100 fw-bold">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Cambios
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>