import '../css/app.css';
import Lenis from 'lenis';
import gsap from 'gsap';

const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/* --------------------------------------------------------------------------
 | Smooth scrolling (Lenis)
 * ------------------------------------------------------------------------ */
function initLenis() {
    if (prefersReduced) return;

    const lenis = new Lenis({
        duration: 1.1,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        smoothWheel: true,
    });

    gsap.ticker.add((time) => lenis.raf(time * 1000));
    gsap.ticker.lagSmoothing(0);

    document.querySelectorAll('a[href^="#"]').forEach((a) => {
        a.addEventListener('click', (e) => {
            const target = document.querySelector(a.getAttribute('href'));
            if (target) {
                e.preventDefault();
                lenis.scrollTo(target, { offset: -90 });
            }
        });
    });
}

/* --------------------------------------------------------------------------
 | Reveal-on-scroll via IntersectionObserver.
 | CSS hides [data-animate] ONLY when <html> has `.anim-ready`, so if JS
 | fails to run, content stays fully visible (no blank sections, SEO-safe).
 * ------------------------------------------------------------------------ */
function initReveals() {
    if (prefersReduced) return;

    document.documentElement.classList.add('anim-ready');

    // Assign stagger delays to grouped children.
    document.querySelectorAll('[data-animate-group]').forEach((group) => {
        group.querySelectorAll('[data-animate]').forEach((el, i) => {
            el.style.setProperty('--reveal-delay', `${i * 90}ms`);
        });
    });

    const io = new IntersectionObserver(
        (entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-in');
                    observer.unobserve(entry.target);
                }
            });
        },
        { rootMargin: '0px 0px -8% 0px', threshold: 0.12 }
    );

    document.querySelectorAll('[data-animate]').forEach((el) => io.observe(el));

    // Failsafe: if anything is still hidden 3s after load, reveal it.
    window.setTimeout(() => {
        document.querySelectorAll('[data-animate]:not(.is-in)').forEach((el) => {
            const r = el.getBoundingClientRect();
            if (r.top < window.innerHeight && r.bottom > 0) el.classList.add('is-in');
        });
    }, 3000);
}

/* --------------------------------------------------------------------------
 | Hero intro timeline + floating plane
 * ------------------------------------------------------------------------ */
function initHero() {
    const hero = document.querySelector('[data-hero]');
    if (!hero || prefersReduced) return;

    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });
    tl.from('[data-hero-badge]', { opacity: 0, y: 18, duration: 0.6 })
        .from('[data-hero-title] > *', { opacity: 0, y: 40, duration: 0.8, stagger: 0.12 }, '-=0.2')
        .from('[data-hero-copy]', { opacity: 0, y: 24, duration: 0.7 }, '-=0.4')
        // CTA (search bar + button) animates as ONE unit with NO opacity,
        // so the button can never be left invisible if the tween is interrupted.
        .from('[data-hero-cta]', { y: 20, duration: 0.6 }, '-=0.4')
        .from('[data-hero-stat]', { opacity: 0, y: 24, scale: 0.96, duration: 0.6, stagger: 0.12 }, '-=0.3');

    // Belt-and-suspenders: guarantee every hero element is visible shortly
    // after load, regardless of what happened to the timeline.
    window.setTimeout(() => {
        gsap.set(
            ['[data-hero-badge]', '[data-hero-title] > *', '[data-hero-copy]', '[data-hero-cta]', '[data-hero-stat]'],
            { opacity: 1, clearProps: 'opacity' },
        );
    }, 2600);

    gsap.to('[data-hero-plane]', {
        y: -14, x: 10, rotation: 2,
        duration: 3.5, ease: 'sine.inOut', yoyo: true, repeat: -1,
    });
}

/* --------------------------------------------------------------------------
 | Count-up numbers when they scroll into view (IntersectionObserver)
 * ------------------------------------------------------------------------ */
function initCounters() {
    const io = new IntersectionObserver(
        (entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                observer.unobserve(el);
                const end = parseFloat(el.dataset.count);
                const suffix = el.dataset.countSuffix || '';
                if (prefersReduced) {
                    el.textContent = end.toLocaleString() + suffix;
                    return;
                }
                const obj = { val: 0 };
                gsap.to(obj, {
                    val: end, duration: 1.6, ease: 'power2.out',
                    onUpdate: () => { el.textContent = Math.round(obj.val).toLocaleString() + suffix; },
                });
            });
        },
        { threshold: 0.5 }
    );
    document.querySelectorAll('[data-count]').forEach((el) => io.observe(el));
}

/* --------------------------------------------------------------------------
 | Hero visa quick-search: select a country + button → go to that visa page
 * ------------------------------------------------------------------------ */
function initVisaJump() {
    const form = document.querySelector('[data-visa-jump]');
    if (!form) return;
    const select = form.querySelector('select');
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        if (select.value) window.location.href = '/visa/' + select.value;
    });
}

/* --------------------------------------------------------------------------
 | Mobile navigation toggle
 * ------------------------------------------------------------------------ */
function initMobileMenu() {
    const toggle = document.querySelector('[data-mobile-toggle]');
    const menu = document.querySelector('[data-mobile-menu]');
    if (!toggle || !menu) return;

    const openIcon = toggle.querySelector('[data-menu-open]');
    const closeIcon = toggle.querySelector('[data-menu-close]');

    const setOpen = (open) => {
        menu.classList.toggle('hidden', !open);
        openIcon?.classList.toggle('hidden', open);
        closeIcon?.classList.toggle('hidden', !open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    };

    toggle.addEventListener('click', () => setOpen(menu.classList.contains('hidden')));
    menu.querySelectorAll('a').forEach((a) => a.addEventListener('click', () => setOpen(false)));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setOpen(false); });
}

function boot() {
    initLenis();
    initHero();
    initReveals();
    initCounters();
    initVisaJump();
    initMobileMenu();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
