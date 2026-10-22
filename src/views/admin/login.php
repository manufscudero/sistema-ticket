<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="grid-overlay"></div>

<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="col-md-5 col-lg-4">
        <div class="card glass-card p-4">
            <div class="text-center mb-4">
                <img src="<?= BASE_URL; ?>/assets/img/uticket.png" alt="Logo UTicket" class="mb-3" style="max-height: 30px;">
                <h4 class="fw-bold text-white mb-1">Admin - UTIC</h4>
                <p class="text-muted small">Acceso al Panel Técnico DGVS</p>
            </div>

            <?php if ($flash = Session::getFlash()): ?>
                <div class="alert alert-<?= $flash['type'] ?> bg-opacity-25 text-white border-0 py-2" role="alert">
                    <small><?= htmlspecialchars($flash['message']) ?></small>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/admin/login" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label text-muted small">Usuario</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Ingrese el nombre de usuario" required autofocus>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label text-muted small">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-info text-dark w-100 fw-bold py-2 shadow-sm">
                    <i class="fa-solid fa-right-to-bracket me-2"></i>Ingresar
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>