// Saglasnost za kolačiće i GA4. Dok posetilac ne prihvati, Google Analytics
// se ne učitava i ne šalje se nijedan zahtev ka Google-u.

const KEY = 'brzisajt.cookie-consent';

const read = () => {
    try {
        return window.localStorage.getItem(KEY);
    } catch {
        return null;
    }
};

const write = (value) => {
    try {
        window.localStorage.setItem(KEY, value);
    } catch {
        /* privatni režim — izbor važi samo za ovu posetu */
    }
};

let loaded = false;
const loadGa = (id) => {
    if (loaded || !id) return;
    loaded = true;
    window.dataLayer = window.dataLayer || [];
    window.gtag = function () { window.dataLayer.push(arguments); };
    window.gtag('js', new Date());
    window.gtag('config', id, { anonymize_ip: true });
    const s = document.createElement('script');
    s.async = true;
    s.src = `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(id)}`;
    document.head.appendChild(s);
};

export default (Alpine) => {
    Alpine.data('cookieConsent', (gaId) => ({
        open: false,
        init() {
            const choice = read();
            if (choice === 'granted') loadGa(gaId);
            else if (choice !== 'denied') this.open = true;
        },
        accept() {
            write('granted');
            this.open = false;
            loadGa(gaId);
        },
        decline() {
            const wasGranted = read() === 'granted';
            write('denied');
            this.open = false;
            // GA je već učitan u ovoj poseti — osvežavamo stranicu da se ugasi.
            if (wasGranted && loaded) window.location.reload();
        },
    }));
};
