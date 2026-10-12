<?php
$stats = $stats ?? ['total' => 0, 'nuevos' => 0, 'en_proceso' => 0, 'resueltos' => 0, 'cerrados' => 0];
$ultimos_tickets = $ultimos_tickets ?? [];

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar_admin.php';
?>
<div class="grid-overlay"></div>

<div class="container pb-5">
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card glass-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted d-block small">NUEVOS</span>
                        <h3 class="fw-bold text-info mb-0"><?= $stats['nuevos'] ?></h3>
                    </div>
                    <i class="fa-solid fa-bell fa-2x text-info opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card glass-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted d-block small">EN PROCESO</span>
                        <h3 class="fw-bold text-warning mb-0"><?= $stats['en_proceso'] ?></h3>
                    </div>
                    <i class="fa-solid fa-spinner fa-2x text-warning opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card glass-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted d-block small">RESUELTOS</span>
                        <h3 class="fw-bold text-success mb-0"><?= $stats['resueltos'] ?></h3>
                    </div>
                    <i class="fa-solid fa-circle-check fa-2x text-success opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card glass-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted d-block small">TOTAL HISTÓRICO</span>
                        <h3 class="fw-bold text-light mb-0"><?= $stats['total'] ?></h3>
                    </div>
                    <i class="fa-solid fa-layer-group fa-2x text-secondary opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla Últimos Tickets -->
    <div class="card glass-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-white mb-0"><i class="fa-solid fa-clock-rotate-left me-2 text-info"></i>Últimas Solicitudes</h5>
            <a href="<?= BASE_URL ?>/admin/tickets" class="btn btn-sm btn-outline-info">Ver todos</a>
        </div>

        <div class="table-responsive">
            <table class="table table-glass align-middle mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Solicitante</th>
                        <th>Ubicación</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ultimos_tickets)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No hay tickets registrados aún.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ultimos_tickets as $t): ?>
                            <?php
                            // Definir colores dinámicos por estado para el dashboard
                            $estadoConfig = match ($t['estado']) {
                                'Nuevo' => [
                                    'badge' => 'bg-info bg-opacity-25 text-info border border-info border-opacity-25',
                                    'btn' => 'btn-info text-dark'
                                ],
                                'En Proceso' => [
                                    'badge' => 'bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25',
                                    'btn' => 'btn-warning text-dark'
                                ],
                                'Resuelto' => [
                                    'badge' => 'bg-success bg-opacity-25 text-success border border-success border-opacity-25',
                                    'btn' => 'btn-success text-dark'
                                ],
                                'Cerrado' => [
                                    'badge' => 'bg-secondary bg-opacity-25 text-secondary border border-secondary border-opacity-25',
                                    'btn' => 'btn-secondary text-light'
                                ],
                                default => [
                                    'badge' => 'bg-dark text-light border border-secondary',
                                    'btn' => 'btn-secondary text-light'
                                ]
                            };
                            ?>
                            <tr>
                                <td class="fw-bold text-info"><?= $t['codigo_ticket'] ?></td>
                                <td><?= htmlspecialchars($t['solicitante_nombre']) ?></td>
                                <td><small class="text-muted"><?= $t['piso'] ?></small><br><?= $t['dependencia'] ?></td>
                                <td><?= $t['categoria'] ?></td>
                                <td>
                                    <span class="badge <?= $estadoConfig['badge'] ?> px-3 py-1"><?= $t['estado'] ?></span>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/ticket/detalle?id=<?= $t['id'] ?>" class="btn btn-sm <?= $estadoConfig['btn'] ?> fw-bold">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>