// Reveal-on-scroll + count-up for the public Vue pages.
// Reuses the existing app.css rules (.anim-ready [data-animate] / .is-in).
// Bulletproof: content is visible unless JS runs; if JS runs it hides then reveals.
export function runPublicAnimations(root = document) {
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduce) {
        root.querySelectorAll('[data-animate]').forEach((el) => el.classList.add('is-in'));
        root.querySelectorAll('[data-count]').forEach((el) => {
            el.textContent = Number(el.dataset.count).toLocaleString() + (el.dataset.countSuffix || '');
        });
        return () => {};
    }

    document.documentElement.classList.add('anim-ready');

    // Stagger grouped children.
    root.querySelectorAll('[data-animate-group]').forEach((group) => {
        group.querySelectorAll('[data-animate]').forEach((el, i) => {
            el.style.setProperty('--reveal-delay', `${i * 80}ms`);
        });
    });

    const revealIO = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((e) => {
                if (e.isIntersecting) { e.target.classList.add('is-in'); obs.unobserve(e.target); }
            });
        },
        { rootMargin: '0px 0px -8% 0px', threshold: 0.12 },
    );
    root.querySelectorAll('[data-animate]').forEach((el) => revealIO.observe(el));

    const countIO = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((e) => {
                if (!e.isIntersecting) return;
                const el = e.target;
                obs.unobserve(el);
                const end = Number(el.dataset.count) || 0;
                const suffix = el.dataset.countSuffix || '';
                const start = performance.now();
                const dur = 1400;
                const tick = (now) => {
                    const p = Math.min(1, (now - start) / dur);
                    const eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = Math.round(end * eased).toLocaleString() + suffix;
                    if (p < 1) requestAnimationFrame(tick);
                };
                requestAnimationFrame(tick);
            });
        },
        { threshold: 0.5 },
    );
    root.querySelectorAll('[data-count]').forEach((el) => countIO.observe(el));

    // Failsafe: reveal anything still hidden shortly after mount.
    const t = window.setTimeout(() => {
        root.querySelectorAll('[data-animate]:not(.is-in)').forEach((el) => {
            const r = el.getBoundingClientRect();
            if (r.top < window.innerHeight && r.bottom > 0) el.classList.add('is-in');
        });
    }, 2500);

    return () => { revealIO.disconnect(); countIO.disconnect(); clearTimeout(t); };
}
