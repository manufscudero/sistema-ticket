<?php 
$tickets = $tickets ?? [];
$dependenciasList = $dependenciasList ?? [];
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar_admin.php';
?>
<div class="grid-overlay"></div>
<div class="container pb-5">
    <div class="card glass-card p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <h4 class="fw-bold text-white mb-0"><i class="fa-solid fa-ticket me-2 text-info"></i>Listado de Tickets</h4>

            <div class="btn-group">
                <a href="<?= BASE_URL ?>/admin/tickets" class="btn btn-sm btn-outline-secondary text-white">Todos</a>
                <a href="<?= BASE_URL ?>/admin/tickets?estado=Nuevo" class="btn btn-sm btn-outline-info">Nuevos</a>
                <a href="<?= BASE_URL ?>/admin/tickets?estado=En+Proceso" class="btn btn-sm btn-outline-warning">En Proceso</a>
                <a href="<?= BASE_URL ?>/admin/tickets?estado=Resuelto" class="btn btn-sm btn-outline-success">Resueltos</a>
            </div>
        </div>

        <!-- Formulario de Filtros Optimizado y Compacto -->
        <form method="GET" action="<?= BASE_URL ?>/admin/tickets" class="row g-2 align-items-center mb-4 bg-dark bg-opacity-25 p-3 rounded border border-secondary border-opacity-25">
            <?php if (!empty($_GET['estado'])): ?>
                <input type="hidden" name="estado" value="<?= htmlspecialchars($_GET['estado']) ?>">
            <?php endif; ?>

            <!-- Selector de Oficinas / Dependencias -->
            <div class="col-md-5">
                <select name="dependencia_id" class="form-select form-select-sm bg-dark text-light border-secondary">
                    <option value="">Todas las oficinas / dependencias</option>
                    <?php foreach ($dependenciasList as $dep): ?>
                        <option value="<?= $dep['id'] ?>" <?= (isset($_GET['dependencia_id']) && $_GET['dependencia_id'] == $dep['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dep['piso_nombre'] . ' - ' . $dep['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Input de Cédula -->
            <div class="col-md-4">
                <input type="text" name="cedula" class="form-control form-control-sm bg-dark text-light border-secondary" placeholder="Filtrar por Cédula..." value="<?= htmlspecialchars($_GET['cedula'] ?? '') ?>">
            </div>

            <!-- Botones de Acción Compactos -->
            <div class="col-md-3 d-flex gap-2 justify-content-end">
                <button type="submit" class="btn btn-sm btn-info fw-bold px-4">
                    <i class="fa-solid fa-filter me-1"></i> Filtrar
                </button>

                <?php if (!empty($_GET['dependencia_id']) || !empty($_GET['cedula']) || !empty($_GET['estado'])): ?>
                    <a href="<?= BASE_URL ?>/admin/tickets" class="btn btn-sm btn-outline-danger px-3 d-flex align-items-center justify-content-center" title="Limpiar filtros">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>

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
                            $estadoConfig = match ($t['estado']) {
                                'Nuevo' => [
                                    'badge' => 'bg-info bg-opacity-25 text-info border border-info border-opacity-25',
                                    'btn' => 'btn-info text-white'
                                ],
                                'En Proceso' => [
                                    'badge' => 'bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25',
                                    'btn' => 'btn-warning text-white'
                                ],
                                'Resuelto' => [
                                    'badge' => 'bg-success bg-opacity-25 text-success border border-success border-opacity-25',
                                    'btn' => 'btn-success text-white'
                                ],
                                'Cerrado' => [
                                    'badge' => 'bg-secondary bg-opacity-25 text-secondary border border-secondary border-opacity-25',
                                    'btn' => 'btn-secondary text-white'
                                ],
                                default => [
                                    'badge' => 'bg-dark text-light border border-secondary',
                                    'btn' => 'btn-secondary text-white'
                                ]
                            };
                            ?>
                            <tr>
                                <td class="fw-bold text-info"><?= $t['codigo_ticket'] ?></td>
                                <td><?= htmlspecialchars($t['solicitante_nombre'] ?? '') ?></td>
                                <td>
                                    <small class="text-muted"><?= htmlspecialchars($t['piso'] ?? '') ?></small><br>
                                    <?= htmlspecialchars($t['dependencia'] ?? '') ?>
                                </td>
                                <td><?= htmlspecialchars($t['categoria'] ?? '') ?></td>
                                <td>
                                    <?php if (!empty($t['tecnico_nombre'])): ?>
                                        <span class="text-light small"><i class="fa-solid me-1 text-info"></i><?= htmlspecialchars($t['tecnico_nombre']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small fst-italic">Sin asignar</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $estadoConfig['badge'] ?> px-3 py-1"><?= $t['estado'] ?></span>
                                </td>
                                <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($t['fecha_creacion'])) ?></small></td>
                                <td>
                                    <!-- Botón de acción con ícono en blanco -->
                                    <a href="<?= BASE_URL ?>/admin/ticket/detalle?id=<?= $t['id'] ?>" class="btn btn-sm <?= $estadoConfig['btn'] ?> fw-bold" title="Ver detalle">
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>