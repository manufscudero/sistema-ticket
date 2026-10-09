(function () {
    const canvas = document.getElementById('particles-canvas');
    if (!canvas) return;

    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReduced) return;

    const ctx = canvas.getContext('2d');
    let particles = [];
    let width, height, dpr;

    // Colores de las partículas (coherentes con la paleta)
    const COLORS = [
        'rgba(77, 107, 254, ',   // azul
        'rgba(34, 211, 238, ',   // cian
        'rgba(124, 58, 237, ',   // violeta
        'rgba(255, 255, 255, '   // blanco
    ];

    function resize() {
        dpr = window.devicePixelRatio || 1;
        width = window.innerWidth;
        height = window.innerHeight;
        canvas.width = width * dpr;
        canvas.height = height * dpr;
        canvas.style.width = width + 'px';
        canvas.style.height = height + 'px';
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

        // Ajustar cantidad según tamaño de pantalla
        const count = Math.min(90, Math.floor((width * height) / 18000));
        particles = Array.from({ length: count }, createParticle);
    }

    function createParticle() {
        const color = COLORS[Math.floor(Math.random() * COLORS.length)];
        return {
            x: Math.random() * width,
            y: Math.random() * height,
            vx: (Math.random() - 0.5) * 0.35,
            vy: (Math.random() - 0.5) * 0.35,
            r: Math.random() * 1.8 + 0.6,
            alpha: Math.random() * 0.5 + 0.3,
            color: color,
            pulse: Math.random() * Math.PI * 2
        };
    }

    function draw() {
        ctx.clearRect(0, 0, width, height);

        // Dibujar líneas de conexión primero
        for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
                const a = particles[i], b = particles[j];
                const dx = a.x - b.x, dy = a.y - b.y;
                const dist = Math.sqrt(dx * dx + dy * dy);

                if (dist < 130) {
                    const opacity = (1 - dist / 130) * 0.22;
                    ctx.strokeStyle = `rgba(120, 160, 255, ${opacity})`;
                    ctx.lineWidth = 0.6;
                    ctx.beginPath();
                    ctx.moveTo(a.x, a.y);
                    ctx.lineTo(b.x, b.y);
                    ctx.stroke();
                }
            }
        }

        // Dibujar partículas con glow
        particles.forEach(p => {
            p.pulse += 0.02;
            const twinkle = 0.7 + Math.sin(p.pulse) * 0.3;

            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = p.color + (p.alpha * twinkle) + ')';
            ctx.shadowBlur = 12;
            ctx.shadowColor = p.color + '0.9)';
            ctx.fill();
            ctx.shadowBlur = 0;

            // Movimiento
            p.x += p.vx;
            p.y += p.vy;

            // Rebote en bordes
            if (p.x < 0 || p.x > width)  p.vx *= -1;
            if (p.y < 0 || p.y > height) p.vy *= -1;
        });

        requestAnimationFrame(draw);
    }

    // Interacción suave con el mouse (las partículas huyen un poco)
    let mouseX = null, mouseY = null;
    window.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
    });
    window.addEventListener('mouseleave', () => {
        mouseX = null;
        mouseY = null;
    });

    // Fuerza de repulsión
    function applyMouseForce() {
        if (mouseX === null) return;
        particles.forEach(p => {
            const dx = p.x - mouseX;
            const dy = p.y - mouseY;
            const dist = Math.sqrt(dx * dx + dy * dy);
            if (dist < 120 && dist > 0.1) {
                const force = (120 - dist) / 120 * 0.6;
                p.x += (dx / dist) * force;
                p.y += (dy / dist) * force;
            }
        });
        requestAnimationFrame(applyMouseForce);
    }
    applyMouseForce();

    window.addEventListener('resize', resize);
    resize();
    draw();
})();