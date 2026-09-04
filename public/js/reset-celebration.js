(function () {
    const canvas = document.getElementById("resetConfetti");

    if (!canvas) {
        return;
    }

    const ctx = canvas.getContext("2d");
    const colors = ["#ff6f00", "#004d40", "#fffdd0", "#ffd54f", "#80cbc4", "#ff8a65"];
    const pieces = [];
    const count = 140;
    let frame = 0;

    function resize() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }

    function spawn(burst) {
        const originX = burst ? canvas.width / 2 : Math.random() * canvas.width;
        const originY = burst ? canvas.height * 0.28 : -12;

        for (let i = 0; i < (burst ? count : 3); i++) {
            const angle = burst ? (Math.random() * Math.PI) + Math.PI : Math.PI / 2;
            const speed = burst ? 8 + Math.random() * 10 : 2 + Math.random() * 3;

            pieces.push({
                x: originX + (burst ? (Math.random() - 0.5) * 80 : 0),
                y: originY,
                vx: Math.cos(angle) * speed * (burst ? (Math.random() * 1.4 - 0.2) : 0.2),
                vy: Math.sin(angle) * speed + (burst ? -6 : 2),
                w: 6 + Math.random() * 8,
                h: 8 + Math.random() * 10,
                rot: Math.random() * Math.PI,
                vr: (Math.random() - 0.5) * 0.3,
                color: colors[Math.floor(Math.random() * colors.length)],
                life: 180 + Math.random() * 80,
            });
        }
    }

    function tick() {
        frame += 1;
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (frame < 90 && frame % 8 === 0) {
            spawn(false);
        }

        for (let i = pieces.length - 1; i >= 0; i--) {
            const piece = pieces[i];
            piece.vy += 0.18;
            piece.x += piece.vx;
            piece.y += piece.vy;
            piece.rot += piece.vr;
            piece.life -= 1;

            ctx.save();
            ctx.translate(piece.x, piece.y);
            ctx.rotate(piece.rot);
            ctx.globalAlpha = Math.max(0, piece.life / 80);
            ctx.fillStyle = piece.color;
            ctx.fillRect(-piece.w / 2, -piece.h / 2, piece.w, piece.h);
            ctx.restore();

            if (piece.life <= 0 || piece.y > canvas.height + 20) {
                pieces.splice(i, 1);
            }
        }

        if (pieces.length || frame < 120) {
            requestAnimationFrame(tick);
        } else {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
    }

    resize();
    window.addEventListener("resize", resize);
    spawn(true);
    requestAnimationFrame(tick);
})();
