<?php 
$ticket = $ticket ?? null;
require_once __DIR__ . '/../layouts/header.php'; 
?>
<div class="grid-overlay"></div>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-3"><i class="fa-solid fa-magnifying-glass me-2"></i>Consultar Estado de Ticket</h4>
                    
                    <?php if ($flash = Session::getFlash()): ?>
                        <div class="alert alert-<?= $flash['type'] ?>" role="alert">
                            <?= htmlspecialchars($flash['message']) ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/ticket/consultar" method="GET" class="row g-2">
                        <div class="col-8">
                            <input type="text" name="codigo" class="form-control" placeholder="Ej: UTIC-202610-A8F2" value="<?= htmlspecialchars($_GET['codigo'] ?? '') ?>" required>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary w-100 fw-bold">Buscar</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($ticket): ?>
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 fw-bold">Ticket: <?= htmlspecialchars($ticket['codigo_ticket']) ?></h5>
                        <?php 
                            $badge = match($ticket['estado']) {
                                'Nuevo' => 'bg-info',
                                'En Proceso' => 'bg-warning text-dark',
                                'Resuelto' => 'bg-success',
                                'Cerrado' => 'bg-secondary',
                                default => 'bg-secondary'
                            };
                        ?>
                        <span class="badge <?= $badge ?> px-3 py-2 fs-6"><?= $ticket['estado'] ?></span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Solicitante</small>
                                <strong><?= htmlspecialchars($ticket['solicitante_nombre']) ?></strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Ubicación / Dependencia</small>
                                <strong><?= htmlspecialchars($ticket['dependencia'] ?? 'Oficina Personalizada / Otro') ?></strong>
                            </div>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Categoría</small>
                            <span><?= htmlspecialchars($ticket['categoria']) ?></span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Descripción</small>
                            <div class="p-3 bg-dark rounded"><?= nl2br(htmlspecialchars($ticket['descripcion'])) ?></div>
                        </div>

                        <?php if (!empty($ticket['observacion_tecnica'])): ?>
                            <div class="alert alert-info mb-0">
                                <strong class="d-block mb-1"><i class="fa-solid fa-comment-dots me-1"></i> Respuesta de la UTIC:</strong>
                                <?= nl2br(htmlspecialchars($ticket['observacion_tecnica'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer text-muted small">
                        Fecha de creación: <?= date('d/m/Y H:i', strtotime($ticket['fecha_creacion'])) ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="text-center mt-3">
                <a href="<?= BASE_URL ?>/ticket/crear" class="text-decoration-none"><i class="fa-solid fa-arrow-left me-1"></i> Volver a crear ticket</a>
            </div>
        </div>
    </div>
</div>

<?php 
require_once __DIR__ . '/../layouts/footer.php'; 
?>