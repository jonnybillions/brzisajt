// Klijentski deo konfiguratora: nacrt u localStorage, prelaz
// iz „Želim ovaj stil“, donji panel na mobilnom i natpisi za rezime.
// Stanje forme živi u Livewire-u ($wire.form) — pregled i rezime ga čitaju direktno.

const STORAGE_KEY = 'brzisajt.configurator.v1';

const storage = {
    read() {
        try {
            const raw = window.localStorage.getItem(STORAGE_KEY);
            return raw ? JSON.parse(raw) : null;
        } catch {
            return null;
        }
    },
    write(value) {
        try {
            window.localStorage.setItem(STORAGE_KEY, JSON.stringify(value));
        } catch {
            /* privatni režim ili zabrana — jednostavno ne čuvamo */
        }
    },
    clear() {
        try {
            window.localStorage.removeItem(STORAGE_KEY);
        } catch {
            /* ništa */
        }
    },
};

// Pre učitavanja konfiguratora pamtimo izabrani stil i skrolujemo do njega.
window.addEventListener('cfg-style', (e) => {
    if (!document.querySelector('[data-configurator]')) {
        window.brziPendingStyle = e.detail.slug;
        document.getElementById('configurator')?.scrollIntoView({ behavior: 'smooth' });
    }
});

export default (Alpine) => {
    Alpine.data('configurator', (opts) => ({
        labels: opts.labels,
        previewOpen: false,
        sheetOpen: false,
        inView: false,
        ready: false,
        saveTimer: null,

        init() {
            // Klik na „Želim ovaj stil“ je mogao da se desi pre učitavanja komponente.
            if (window.brziPendingStyle) {
                const pending = window.brziPendingStyle;
                window.brziPendingStyle = null;
                this.$nextTick(() => this.pickStyle(pending));
            }

            if (opts.submitted) {
                storage.clear();
                return;
            }

            const saved = storage.read();
            const restore = saved && saved.form
                ? this.$wire.restore(saved.form, saved.step || 1)
                : Promise.resolve();

            Promise.resolve(restore).finally(() => {
                this.ready = true;
                // Svaka izmena forme ili koraka — čuvamo nacrt (sa kašnjenjem).
                Alpine.effect(() => {
                    const snapshot = { form: JSON.parse(JSON.stringify(this.$wire.form)), step: this.$wire.step };
                    delete snapshot.form.consent;
                    if (!this.ready) return;
                    clearTimeout(this.saveTimer);
                    this.saveTimer = setTimeout(() => storage.write(snapshot), 300);
                });
            });

            this.$wire.$on('cfg-submitted', () => {
                clearTimeout(this.saveTimer);
                this.ready = false;
                storage.clear();
                this.sheetOpen = false;
                this.$nextTick(() => this.scrollToTop());
            });

            this.$wire.$on('cfg-step', () => this.$nextTick(() => {
                this.scrollToTop();
                this.$root.querySelector('[data-step-heading]')?.focus({ preventScroll: true });
            }));
        },

        // Stigli smo iz sekcije „Primeri“.
        pickStyle(slug) {
            this.$wire.set('form.style', slug, false);
            try {
                const url = new URL(window.location.href);
                url.searchParams.set('style', slug);
                url.hash = 'configurator';
                window.history.replaceState(null, '', url);
            } catch { /* ništa */ }
            this.scrollToTop();
        },

        scrollToTop() {
            const el = document.getElementById('configurator');
            if (!el) return;
            const top = el.getBoundingClientRect().top + window.scrollY - 72;
            if (Math.abs(window.scrollY - top) > 40) {
                window.scrollTo({ top, behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
            }
        },

        // --- natpisi za pregled i rezime ---
        get f() {
            return this.$wire.form;
        },
        get pagesCount() {
            return (this.f.pages || []).length;
        },
        get palette() {
            const colors = this.f.colorsAuto ? [] : (this.f.colors || []).filter(Boolean);
            const fallback = ['#6B3BFF', '#C8F43A', '#3EC5FF'];
            return [0, 1, 2].map((i) => colors[i] || (colors.length ? colors[0] : fallback[i]));
        },
        pageLabel(key) {
            if (key === 'custom') return (this.f.customPage || '').trim() || this.labels.pages.custom;
            return this.labels.pages[key] || key;
        },
        get pageLabels() {
            return (this.f.pages || []).map((p) => this.pageLabel(p));
        },
        get styleProject() {
            return this.labels.projects.find((p) => p.slug === this.f.style) || null;
        },
        get summary() {
            const l = this.labels;
            const f = this.f;
            const rows = [];
            const push = (label, value) => value && rows.push({ label, value });

            push(l.sum.type, f.type ? l.type[f.type] : null);
            push(l.sum.pages, this.pageLabels.join(', '));
            push(l.sum.style, this.styleProject ? this.styleProject.name : l.styleTrust);
            push(l.sum.hero, l.hero[f.hero]);
            push(l.sum.menu, [l.menuType[f.menuType], (l.menuPosition[f.menuPosition] || '').toLowerCase()].filter(Boolean).join(', '));
            push(l.sum.motion, l.motion[f.motion]);
            push(l.sum.colors, f.colorsAuto || !(f.colors || []).length ? l.colorsAuto : f.colors.join(', '));
            push(l.sum.industry, f.industry ? l.industry[f.industry] : null);
            push(l.sum.langs, f.langs === 'two' ? `${l.langs.two.replace(/\s*\(.*\)/, '')}${f.secondLang ? ': ' + f.secondLang : ''}` : l.langs.one);
            const features = [l.features.form, ...(f.features || []).map((k) => l.features[k])];
            push(l.sum.features, features.join(', '));
            return rows;
        },
        get extras() {
            const list = [];
            if (this.f.logo === 'new') list.push(this.labels.extraLogo);
            if (this.f.seo) list.push(this.labels.extraSeo);
            return list;
        },
    }));
};
