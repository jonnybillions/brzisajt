// Snimci ekrana primera za seed: naslovna (1440×900) i unutrašnje stranice.
//
//   node scripts/capture-examples.mjs                 — snimi prave sajtove
//   node scripts/capture-examples.mjs --placeholders  — zamene za nedostupne
//   node scripts/capture-examples.mjs --only=oriental — jedan projekat
//
// Rezultat: database/seeders/assets/projects/{slug}/cover.jpg, {page_type}.jpg
// i manifest.json (stvarni URL, naslov sajta, pronađene stranice).
// Potreban je Chromium: PLAYWRIGHT_BROWSERS_PATH ili CHROMIUM_PATH.

import { chromium } from 'playwright';
import fs from 'node:fs';
import path from 'node:path';
import os from 'node:os';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const projects = JSON.parse(fs.readFileSync(path.join(root, 'database/seeders/data/projects.json'), 'utf8'));
const outDir = path.join(root, 'database/seeders/assets/projects');
const manifestPath = path.join(outDir, 'manifest.json');
const manifest = fs.existsSync(manifestPath) ? JSON.parse(fs.readFileSync(manifestPath, 'utf8')) : {};

const args = process.argv.slice(2);
const placeholders = args.includes('--placeholders');
const only = args.find((a) => a.startsWith('--only='))?.slice(7);

const VIEWPORT = { width: 1440, height: 900 };
const MAX_PER_TYPE = 3;

// Ključne reči u URL-u i tekstu linka → tip stranice (engleski i transliterisani slugovi).
const PAGE_TYPES = {
    about: [/about/, /o-nas/, /o-kompanii/, /onas/, /about-us/, /studio/, /company/, /who-we-are/],
    services: [/services?/, /uslugi/, /what-we-do/],
    catalog: [/catalog/, /katalog/, /menu/, /products?/, /shop/],
    gallery: [/gallery/, /galereya/, /portfolio/, /projects?/, /works?/, /interiors?/],
    team: [/team/, /komanda/, /people/],
    reviews: [/reviews?/, /otzyvy/, /testimonials?/],
    blog: [/blog/, /news/, /novosti/, /press/, /journal/, /articles?/],
    contacts: [/contacts?/, /kontakty/],
};

const LABELS = {
    home: 'Početna', about: 'O nama', services: 'Usluge', catalog: 'Meni / Katalog', gallery: 'Galerija / Portfolio',
    team: 'Tim', reviews: 'Utisci klijenata', blog: 'Vesti / Blog', contacts: 'Kontakt',
};

const PALETTE = ['#6B3BFF', '#FF5A4E', '#C8F43A', '#3EC5FF'];

function executablePath() {
    if (process.env.CHROMIUM_PATH) return process.env.CHROMIUM_PATH;
    const local = '/opt/pw-browsers/chromium';
    if (fs.existsSync(local)) {
        const dir = fs.readdirSync('/opt/pw-browsers').find((d) => /^chromium-\d+$/.test(d));
        if (dir) return path.join('/opt/pw-browsers', dir, 'chrome-linux/chrome');
    }
    return undefined;
}

async function dismissOverlays(page) {
    const texts = ['Accept', 'Accept all', 'Agree', 'OK', 'Got it', 'Close'];
    for (const t of texts) {
        const btn = page.getByRole('button', { name: t, exact: false }).first();
        if (await btn.isVisible().catch(() => false)) {
            await btn.click({ timeout: 1000 }).catch(() => {});
            break;
        }
    }
}

async function shoot(page, url, file) {
    await page.goto(url, { waitUntil: 'networkidle', timeout: 45000 }).catch(() => page.waitForTimeout(3000));
    await page.waitForTimeout(2500);
    await dismissOverlays(page);
    await page.mouse.move(5, 5);
    await page.waitForTimeout(500);
    await page.screenshot({ path: file, type: 'jpeg', quality: 82 });
}

async function findEnVersion(page, url) {
    const en = await page.evaluate(() => {
        const alt = document.querySelector('link[rel="alternate"][hreflang^="en"]');
        if (alt) return alt.href;
        const a = [...document.querySelectorAll('a[href]')].find((el) => {
            const t = el.textContent.trim().toLowerCase();
            return (t === 'en' || t === 'eng' || t === 'english') || /\/en\/?$/.test(el.getAttribute('href'));
        });
        return a ? a.href : null;
    });
    return en || url;
}

function classify(links, origin) {
    const found = {};
    for (const { href, text } of links) {
        let u;
        try { u = new URL(href); } catch { continue; }
        if (u.origin !== origin || u.hash && u.pathname === '/') continue;
        const hay = (decodeURIComponent(u.pathname) + ' ' + text).toLowerCase();
        for (const [type, patterns] of Object.entries(PAGE_TYPES)) {
            if (!found[type] && patterns.some((p) => p.test(hay))) {
                found[type] = u.href.split('#')[0];
            }
        }
    }
    return found;
}

async function captureReal(browser, project) {
    const dir = path.join(outDir, project.slug);
    fs.mkdirSync(dir, { recursive: true });
    const page = await browser.newPage({ viewport: VIEWPORT, locale: 'ru-RU' });

    await page.goto(project.url, { waitUntil: 'domcontentloaded', timeout: 45000 });
    let url = page.url();
    // Većina sajtova nema srpsku verziju — tražimo englesku.
    if (project.prefer_en) {
        url = await findEnVersion(page, url);
    }

    await shoot(page, url, path.join(dir, 'cover.jpg'));
    const meta = await page.evaluate(() => ({
        title: document.title,
        siteName: document.querySelector('meta[property="og:site_name"]')?.content || null,
        links: [...document.querySelectorAll('a[href]')].map((a) => ({ href: a.href, text: a.textContent.trim() })),
    }));

    const pages = classify(meta.links, new URL(page.url()).origin);
    for (const [type, pageUrl] of Object.entries(pages)) {
        try {
            await shoot(page, pageUrl, path.join(dir, `${type}.jpg`));
        } catch (e) {
            delete pages[type];
            console.warn(`  ${project.slug}/${type}: ${e.message}`);
        }
    }

    await page.close();
    return { url, title: meta.title, site_name: meta.siteName, pages, placeholder: false };
}

function placeholderHtml(project, type, accent) {
    const label = LABELS[type] || '';
    const dark = accent === '#6B3BFF' || accent === '#FF5A4E';
    return `<!doctype html><html lang="sr-Latn"><head><meta charset="utf-8"><style>
        @font-face { font-family: U; src: url('file://${root}/node_modules/@fontsource-variable/unbounded/files/unbounded-latin-ext-wght-normal.woff2') format('woff2'); unicode-range: U+0100-024F; font-weight: 100 900; }
        @font-face { font-family: U; src: url('file://${root}/node_modules/@fontsource-variable/unbounded/files/unbounded-latin-wght-normal.woff2') format('woff2'); font-weight: 100 900; }
        @font-face { font-family: M; src: url('file://${root}/node_modules/@fontsource-variable/manrope/files/manrope-latin-ext-wght-normal.woff2') format('woff2'); unicode-range: U+0100-024F; font-weight: 200 800; }
        @font-face { font-family: M; src: url('file://${root}/node_modules/@fontsource-variable/manrope/files/manrope-latin-wght-normal.woff2') format('woff2'); font-weight: 200 800; }
        * { box-sizing: border-box; margin: 0; }
        body { width: 1440px; height: 900px; background: #F6F4EF; font-family: M; color: #121016; overflow: hidden; }
        .nav { display: flex; justify-content: space-between; align-items: center; padding: 36px 72px; font-weight: 700; font-size: 18px; }
        .nav i { display: inline-block; width: 90px; height: 10px; border-radius: 9px; background: #12101622; margin-left: 28px; }
        .hero { margin: 8px 72px; height: 740px; border-radius: 48px; background: ${accent}; color: ${dark ? '#fff' : '#121016'}; padding: 72px; position: relative; overflow: hidden; }
        .hero h1 { font-family: U; font-weight: 800; font-size: 76px; line-height: 1.02; max-width: 900px; letter-spacing: -0.02em; }
        .hero p { margin-top: 28px; font-size: 22px; font-weight: 700; opacity: .85; }
        .blob { position: absolute; right: -80px; bottom: -120px; width: 520px; height: 520px; border-radius: 50%; background: #121016; opacity: .14; }
        .tag { position: absolute; left: 72px; bottom: 64px; padding: 12px 22px; border-radius: 99px; background: #F6F4EF; color: #121016; font-weight: 800; font-size: 18px; }
        .ph { position: absolute; right: 72px; top: 64px; padding: 10px 18px; border-radius: 99px; border: 2px dashed currentColor; font-weight: 800; font-size: 15px; }
    </style></head><body>
        <div class="nav"><span>${project.name}</span><span><i></i><i></i><i></i><i></i></span></div>
        <div class="hero"><span class="ph">Zamena — zameniti snimkom ekrana</span><h1>${project.name}</h1><p>${label}</p><div class="blob"></div><span class="tag">${new URL(project.url).hostname}</span></div>
    </body></html>`;
}

async function capturePlaceholder(browser, project, index) {
    const dir = path.join(outDir, project.slug);
    fs.mkdirSync(dir, { recursive: true });
    const page = await browser.newPage({ viewport: VIEWPORT });
    const types = Object.keys(PAGE_TYPES);
    // Raspoređujemo tipove stranica tako da svaki ima po 3 šablona.
    const pageTypes = types.filter((_, t) => (t + index) % projects.length < MAX_PER_TYPE);
    const accent = PALETTE[index % PALETTE.length];

    for (const type of ['home', ...pageTypes]) {
        const tmp = path.join(os.tmpdir(), `bystro-ph-${project.slug}-${type}.html`);
        fs.writeFileSync(tmp, placeholderHtml(project, type, accent));
        await page.goto(`file://${tmp}`, { waitUntil: 'load' });
        await page.evaluate(() => document.fonts.ready);
        await page.screenshot({ path: path.join(dir, type === 'home' ? 'cover.jpg' : `${type}.jpg`), type: 'jpeg', quality: 80 });
        fs.rmSync(tmp);
    }
    await page.close();

    return { url: project.url, title: null, site_name: null, pages: Object.fromEntries(pageTypes.map((t) => [t, null])), placeholder: true };
}

const browser = await chromium.launch({ executablePath: executablePath() });
const failed = [];

for (const [index, project] of projects.entries()) {
    if (only && project.slug !== only) continue;
    if (placeholders && manifest[project.slug] && !manifest[project.slug].placeholder) continue;

    process.stdout.write(`${project.slug} … `);
    try {
        manifest[project.slug] = placeholders
            ? await capturePlaceholder(browser, project, index)
            : await captureReal(browser, project);
        console.log(placeholders ? 'zamena' : `ok (${Object.keys(manifest[project.slug].pages).join(', ') || 'samo naslovna'})`);
    } catch (e) {
        failed.push(project.slug);
        console.log(`nedostupan: ${e.message.split('\n')[0]}`);
    }
}

await browser.close();
fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 4) + '\n');

if (failed.length) {
    console.log(`\nNije uspelo snimanje: ${failed.join(', ')}. Pokrenite sa --placeholders da napravite zamene.`);
}
