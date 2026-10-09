<nav class="navbar navbar-expand-lg navbar-dark glass-nav mb-4 sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-white d-flex align-items-center" href="<?= BASE_URL ?>/admin/dashboard">
            <span class="badge bg-primary me-2 rounded-circle p-2"><i class="fa-solid fa-bolt"></i></span>
            <span>UTIC <span class="text-info">Panel</span></span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto ms-3">
                <li class="nav-item">
                    <a class="nav-link text-light" href="<?= BASE_URL ?>/admin/dashboard"><i class="fa-solid fa-chart-pie me-1 text-info"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="<?= BASE_URL ?>/admin/tickets"><i class="fa-solid fa-list-check me-1 text-info"></i> Tickets</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-light bg-dark px-3 rounded-pill border border-secondary" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-user-gear me-1 text-info"></i> <?= htmlspecialchars($_SESSION['admin_user']['nombre'] ?? 'Técnico') ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark glass-card">
                        <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/admin/logout"><i class="fa-solid fa-power-off me-2"></i> Cerrar Sesión</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>