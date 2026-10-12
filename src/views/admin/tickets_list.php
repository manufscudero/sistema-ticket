<?php 
$tickets = $tickets ?? [];
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/navbar_admin.php'; 
?>
<div class="grid-overlay"></div>
<div class="container pb-5">
    <div class="card glass-card p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <h4 class="fw-bold text-white mb-0"><i class="fa-solid fa-ticket me-2 text-info"></i>Listado de Tickets</h4>
            
            <div class="btn-group">
                <a href="<?= BASE_URL ?>/admin/tickets" class="btn btn-sm btn-outline-secondary">Todos</a>
                <a href="<?= BASE_URL ?>/admin/tickets?estado=Nuevo" class="btn btn-sm btn-outline-info">Nuevos</a>
                <a href="<?= BASE_URL ?>/admin/tickets?estado=En+Proceso" class="btn btn-sm btn-outline-warning">En Proceso</a>
                <a href="<?= BASE_URL ?>/admin/tickets?estado=Resuelto" class="btn btn-sm btn-outline-success">Resueltos</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-glass align-middle mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Solicitante</th>
                        <th>Ubicación / Dep.</th>
                        <th>Categoría</th>
                        <th>Técnico</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tickets)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No se encontraron tickets con este filtro.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tickets as $t): ?>
                            <?php 
                                $estadoConfig = match($t['estado']) {
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
                                    <?php if (!empty($t['tecnico_nombre'])): ?>
                                        <span class="text-light small"><i class="fa-solid fa-user-gear me-1 text-info"></i><?= htmlspecialchars($t['tecnico_nombre']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small fst-italic">Sin asignar</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $estadoConfig['badge'] ?> px-3 py-1"><?= $t['estado'] ?></span>
                                </td>
                                <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($t['fecha_creacion'])) ?></small></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/ticket/detalle?id=<?= $t['id'] ?>" class="btn btn-sm <?= $estadoConfig['btn'] ?> fw-bold">
                                        <i class="fa-solid fa-arrow-right"></i>
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