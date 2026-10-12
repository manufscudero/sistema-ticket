<?php 
$usuarios = $usuarios ?? [];
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/navbar_admin.php'; 
?>
<div class="grid-overlay"></div>
<div class="container pb-5">
    <div class="row g-4">
        <!-- Formulario para Nuevo Usuario -->
        <div class="col-lg-4">
            <div class="card glass-card p-4">
                <h5 class="fw-bold text-white mb-3"><i class="fa-solid fa-user-plus me-2 text-info"></i>Nuevo Usuario</h5>
                
                <form action="<?= BASE_URL ?>/admin/usuario/guardar" method="POST">
                    <div class="mb-3">
                        <label for="nombre_completo" class="form-label text-muted small">Nombre Completo</label>
                        <input type="text" name="nombre_completo" id="nombre_completo" class="form-control" required placeholder="Ej. Juan Pérez">
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label text-muted small">Usuario (Login)</label>
                        <input type="text" name="username" id="username" class="form-control" required placeholder="jperez">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label text-muted small">Correo Electrónico</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="correo@dgvs.gov.py">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label text-muted small">Contraseña</label>
                        <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
                    </div>

                    <button type="submit" class="btn btn-info text-dark w-100 fw-bold">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Usuario
                    </button>
                </form>
            </div>
        </div>

        <!-- Listado de Usuarios -->
        <div class="col-lg-8">
            <div class="card glass-card p-4">
                <h5 class="fw-bold text-white mb-3"><i class="fa-solid fa-users me-2 text-info"></i>Personal / Técnicos Registrados</h5>
                
                <div class="table-responsive">
                    <table class="table table-glass align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Nombre Completo</th>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($usuarios)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No hay usuarios registrados.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($usuarios as $u): ?>
                                    <tr>
                                        <td class="fw-bold text-white"><?= htmlspecialchars($u['nombre_completo']) ?></td>
                                        <td><code><?= htmlspecialchars($u['username']) ?></code></td>
                                        <td><small class="text-muted"><?= htmlspecialchars($u['email'] ?? 'Sin correo') ?></small></td>
                                        <td>
                                            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25">Activo</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>