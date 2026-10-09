<?php 
$codigo = $codigo ?? '';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<!-- Importar Bootstrap Icons por si no está cargado en el header -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="grid-overlay"></div>

<div class="container py-5 position-relative" style="z-index: 1;">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            
            <!-- TARJETA PRINCIPAL (MODO OSCURO AHUMADO) -->
            <div class="main-card p-4 p-md-5">
                
                <!-- ÍCONO CHECK ESMERALDA NEÓN CON GLOW -->
                <div class="mb-3">
                    <i class="bi bi-check-circle-fill display-1" style="color: #34d399; filter: drop-shadow(0 0 15px rgba(52, 211, 153, 0.4));"></i>
                </div>
                
                <h3 class="hero-title mb-2" style="font-size: 2rem;">¡Solicitud Registrada!</h3>
                <p class="text-hero-subtitle mb-4">Tu ticket ha sido enviado al equipo de la UTIC.</p>

                <!-- CAJA DEL CÓDIGO CON CRISTAL OSCURO TRASLÚCIDO -->
                <div class="p-3 rounded-4 mb-3 border d-flex align-items-center justify-content-center flex-wrap gap-2" 
                     style="background: rgba(30, 41, 59, 0.6); border-color: rgba(255, 255, 255, 0.15) !important; backdrop-filter: blur(12px);">
                    
                    <div class="text-center me-2">
                        <span class="d-block small text-muted text-uppercase fw-bold" style="letter-spacing: 0.8px; font-size: 0.72rem; color: #94a3b8 !important;">Código de Seguimiento</span>
                        <strong id="ticketCode" class="fs-3 font-monospace fw-bold" style="color: #38bdf8; text-shadow: 0 0 10px rgba(56, 189, 248, 0.3);"><?= htmlspecialchars($codigo) ?></strong>
                    </div>

                    <!-- BOTÓN COPIAR ESTILO NEÓN -->
                    <button type="button" 
                            class="btn btn-clear btn-sm px-3 py-2 rounded-3 fw-semibold d-inline-flex align-items-center gap-2" 
                            id="btnCopyTicket" 
                            onclick="copyTicketCode()" 
                            title="Copiar código al portapapeles">
                        <i class="bi bi-copy" id="copyIcon"></i>
                        <span id="copyText">Copiar</span>
                    </button>
                </div>

                <p class="small mb-4" style="color: #94a3b8;">Guarda este código para verificar el estado de tu ticket en cualquier momento.</p>

                <!-- ACCIONES -->
                <div class="d-grid gap-2">
                    <a href="<?= BASE_URL ?>/ticket/crear" class="btn btn-modern">Registrar otro ticket</a>
                    <a href="<?= BASE_URL ?>/ticket/consultar?codigo=<?= urlencode($codigo) ?>" class="btn btn-clear">Ver Estado de mi Ticket</a>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
function copyTicketCode() {
    const codeElement = document.getElementById('ticketCode');
    if (!codeElement) return;
    
    const codeText = codeElement.innerText.trim();

    navigator.clipboard.writeText(codeText).then(() => {
        const icon = document.getElementById('copyIcon');
        const text = document.getElementById('copyText');
        const btn = document.getElementById('btnCopyTicket');

        // Estado: Copiado exitosamente
        icon.className = 'bi bi-check-lg';
        text.innerText = '¡Copiado!';
        btn.style.background = 'rgba(16, 185, 129, 0.25)';
        btn.style.borderColor = '#10b981';
        btn.style.color = '#34d399';

        // Restaurar estado inicial tras 2.5 segundos
        setTimeout(() => {
            icon.className = 'bi bi-copy';
            text.innerText = 'Copiar';
            btn.style.background = '';
            btn.style.borderColor = '';
            btn.style.color = '';
        }, 2500);
    }).catch(err => {
        console.error('Error al copiar al portapapeles: ', err);
    });
}
</script>

<?php 
require_once __DIR__ . '/../layouts/footer.php'; 
?>