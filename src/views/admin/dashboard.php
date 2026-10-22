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
                        <h3 id="stat-nuevos" class="fw-bold text-info mb-0"><?= $stats['nuevos'] ?></h3>
                    </div>
                    <i class="fa-solid fa-2x text-info opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card glass-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted d-block small">EN PROCESO</span>
                        <h3 id="stat-proceso" class="fw-bold text-warning mb-0"><?= $stats['en_proceso'] ?></h3>
                    </div>
                    <i class="fa-solid fa-2x text-warning opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card glass-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted d-block small">RESUELTOS</span>
                        <h3 id="stat-resueltos" class="fw-bold text-success mb-0"><?= $stats['resueltos'] ?></h3>
                    </div>
                    <i class="fa-solid fa-2x text-success opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card glass-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted d-block small">TOTAL HISTÓRICO</span>
                        <h3 id="stat-total" class="fw-bold text-light mb-0"><?= $stats['total'] ?></h3>
                    </div>
                    <i class="fa-solid fa-2x text-secondary opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla Últimos Tickets -->
    <div class="card glass-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-white mb-0"><i class="fa-solid fa-clock-rotate-left me-2 text-info"></i>Últimas Solicitudes</h5>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-light d-flex align-items-center gap-1" id="btnManualRefresh" title="Actualizar datos en tiempo real">
                    <i class="fa-solid fa-rotate-right" id="refreshIcon"></i> Actualizar
                </button>
                <a href="<?= BASE_URL ?>/admin/tickets" class="btn btn-sm btn-outline-info">Ver todos</a>
            </div>
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
                <tbody id="ticketsTableBody">
                    <?php if (empty($ultimos_tickets)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No hay tickets registrados aún.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ultimos_tickets as $t): ?>
                            <?php
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
                                <td><small class="text-muted"><?= htmlspecialchars($t['piso'] ?? '') ?></small><br><?= htmlspecialchars($t['dependencia'] ?? '') ?></td>
                                <td><?= htmlspecialchars($t['categoria'] ?? '') ?></td>
                                <td>
                                    <span class="badge <?= $estadoConfig['badge'] ?> px-3 py-1"><?= $t['estado'] ?></span>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/ticket/detalle?id=<?= $t['id'] ?>" class="btn btn-sm <?= $estadoConfig['btn'] ?? 'btn-info' ?> fw-bold" title="Ver detalle">
                                        <i class="fa-solid fa-arrow-right text-white"></i>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('ticketsTableBody');
    const statNuevos = document.getElementById('stat-nuevos');
    const statProceso = document.getElementById('stat-proceso');
    const statResueltos = document.getElementById('stat-resueltos');
    const statTotal = document.getElementById('stat-total');
    const btnRefresh = document.getElementById('btnManualRefresh');
    const refreshIcon = document.getElementById('refreshIcon');

    function fetchDashboardData() {
        if (refreshIcon) refreshIcon.classList.add('fa-spin');

        fetch('<?= BASE_URL ?>/admin/dashboard/json')
            .then(response => response.json())
            .then(data => {
                if (!data.success) return;

                // 1. Actualizar Contadores en tiempo real
                if (statNuevos) statNuevos.innerText = data.stats.nuevos ?? 0;
                if (statProceso) statProceso.innerText = data.stats.en_proceso ?? 0;
                if (statResueltos) statResueltos.innerText = data.stats.resueltos ?? 0;
                if (statTotal) statTotal.innerText = data.stats.total ?? 0;

                // 2. Reconstruir la tabla de últimas solicitudes de manera dinámica
                if (!data.tickets || data.tickets.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No hay tickets registrados aún.</td></tr>';
                    return;
                }

                let html = '';
                data.tickets.forEach(ticket => {
                    let badgeClass = 'bg-dark text-light border border-secondary';
                    let btnClass = 'btn-secondary text-light';

                    if (ticket.estado === 'Nuevo') {
                        badgeClass = 'bg-info bg-opacity-25 text-info border border-info border-opacity-25';
                        btnClass = 'btn-info text-dark';
                    } else if (ticket.estado === 'En Proceso') {
                        badgeClass = 'bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25';
                        btnClass = 'btn-warning text-dark';
                    } else if (ticket.estado === 'Resuelto') {
                        badgeClass = 'bg-success bg-opacity-25 text-success border border-success border-opacity-25';
                        btnClass = 'btn-success text-dark';
                    } else if (ticket.estado === 'Cerrado') {
                        badgeClass = 'bg-secondary bg-opacity-25 text-secondary border border-secondary border-opacity-25';
                        btnClass = 'btn-secondary text-light';
                    }

                    html += `
                        <tr>
                            <td class="fw-bold text-info">${ticket.codigo_ticket || ticket.codigo}</td>
                            <td>${escapeHtml(ticket.solicitante_nombre)}</td>
                            <td><small class="text-muted">${escapeHtml(ticket.piso || '')}</small><br>${escapeHtml(ticket.dependencia || '')}</td>
                            <td>${escapeHtml(ticket.categoria || '')}</td>
                            <td>
                                <span class="badge ${badgeClass} px-3 py-1">${ticket.estado}</span>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>/admin/ticket/detalle?id=${ticket.id}" class="btn btn-sm ${btnClass} fw-bold" title="Ver detalle">
                                    <i class="fa-solid fa-eye text-white"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                });

                tableBody.innerHTML = html;
            })
            .catch(error => console.error('Error al actualizar el dashboard en tiempo real:', error))
            .finally(() => {
                if (refreshIcon) {
                    setTimeout(() => refreshIcon.classList.remove('fa-spin'), 600);
                }
            });
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Configurar Polling automático cada 30 segundos
    setInterval(fetchDashboardData, 30000);

    // Activar evento en el botón manual de actualización
    if (btnRefresh) {
        btnRefresh.addEventListener('click', fetchDashboardData);
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>