// Sitni interaktivni detalji landing stranice. Sve poštuje prefers-reduced-motion.

export const reducedMotion = () =>
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// content-visibility ubrzava prvo učitavanje; posle ga isključujemo,
// inače visine sekcija „plivaju“ i linkovi ka sidrima promašuju.
const renderAllSections = () => document.documentElement.classList.add('cv-off');
document.addEventListener('click', (e) => {
    const link = e.target.closest('a[href*="#"]');
    if (link && link.pathname === window.location.pathname) renderAllSections();
}, true);
window.addEventListener('load', () => {
    const idle = window.requestIdleCallback || ((cb) => setTimeout(cb, 1500));
    idle(renderAllSections, { timeout: 3000 });
});
if (window.location.hash) renderAllSections();

export default function ui(Alpine) {
    // Pojavljivanje pri skrolovanju: x-reveal (ili x-reveal="120" — kašnjenje u ms).
    let observer = null;
    const getObserver = () => {
        if (observer) return observer;
        observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-in');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { rootMargin: '0px 0px -10% 0px', threshold: 0.08 },
        );
        return observer;
    };

    Alpine.directive('reveal', (el, { expression }) => {
        el.classList.add('reveal');
        if (expression) el.style.setProperty('--d', `${parseInt(expression, 10) || 0}ms`);
        if (reducedMotion() || !('IntersectionObserver' in window)) {
            el.classList.add('is-in');
            return;
        }
        getObserver().observe(el);
    });

    // Zaglavlje: smanjuje se pri skrolovanju, hamburger na mobilnom.
    Alpine.data('siteHeader', () => ({
        scrolled: false,
        open: false,
        init() {
            const onScroll = () => (this.scrolled = window.scrollY > 24);
            onScroll();
            window.addEventListener('scroll', onScroll, { passive: true });
            this.$watch('open', (value) => document.body.classList.toggle('overflow-hidden', value));
        },
        close() {
            this.open = false;
        },
    }));

    // Brojač za velike brojeve: x-data="countUp(600)".
    Alpine.data('countUp', (target, duration = 1400) => ({
        value: target,
        init() {
            if (reducedMotion() || !('IntersectionObserver' in window)) return;
            this.value = 0;
            const io = new IntersectionObserver(([entry]) => {
                if (!entry.isIntersecting) return;
                io.disconnect();
                const start = performance.now();
                const tick = (now) => {
                    const p = Math.min(1, (now - start) / duration);
                    const eased = 1 - Math.pow(1 - p, 4);
                    this.value = Math.round(target * eased);
                    if (p < 1) requestAnimationFrame(tick);
                };
                requestAnimationFrame(tick);
            });
            io.observe(this.$el);
        },
        get formatted() {
            return String(this.value).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        },
    }));

    // Isticanje koje prati kursor (samo desktop sa mišem).
    Alpine.directive('spotlight', (el) => {
        if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;
        el.addEventListener('pointermove', (e) => {
            const r = el.getBoundingClientRect();
            el.style.setProperty('--mx', `${e.clientX - r.left}px`);
            el.style.setProperty('--my', `${e.clientY - r.top}px`);
        });
    });

    // FAQ harmonika: jedan otvoren panel.
    Alpine.data('accordion', () => ({
        active: null,
        toggle(i) {
            this.active = this.active === i ? null : i;
        },
        isOpen(i) {
            return this.active === i;
        },
    }));
}
