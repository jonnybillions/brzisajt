// Скриншоты примеров для сидов: обложка (1440×900) и внутренние страницы.
//
//   node scripts/capture-examples.mjs                 — снять реальные сайты
//   node scripts/capture-examples.mjs --placeholders  — заглушки для недоступных
//   node scripts/capture-examples.mjs --only=oriental — один проект
//
// Результат: database/seeders/assets/projects/{slug}/cover.jpg, {page_type}.jpg
// и manifest.json (фактический URL, заголовок сайта, найденные страницы).
// Нужен Chromium: PLAYWRIGHT_BROWSERS_PATH или CHROMIUM_PATH.

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

// Ключевые слова в URL и тексте ссылки → тип страницы.
const PAGE_TYPES = {
    about: [/about/, /o-nas/, /o-kompanii/, /onas/, /компани/, /о нас/, /обо мне/, /about-us/, /studio/, /студи/],
    services: [/services?/, /uslugi/, /услуг/, /направлен/],
    catalog: [/catalog/, /katalog/, /menu/, /меню/, /каталог/, /products?/, /продукц/],
    gallery: [/gallery/, /galereya/, /portfolio/, /projects?/, /галере/, /портфолио/, /проект/, /работы/, /works?/],
    team: [/team/, /komanda/, /команд/, /people/],
    reviews: [/reviews?/, /otzyvy/, /отзыв/, /testimonials?/],
    blog: [/blog/, /news/, /novosti/, /новост/, /блог/, /press/, /статьи/, /journal/],
    contacts: [/contacts?/, /kontakty/, /контакт/],
};

const LABELS = {
    home: 'Главная', about: 'О компании', services: 'Услуги', catalog: 'Меню / Каталог', gallery: 'Галерея / Портфолио',
    team: 'Команда', reviews: 'Отзывы', blog: 'Новости / Блог', contacts: 'Контакты',
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
    const texts = ['Принять', 'Согласен', 'Хорошо', 'Понятно', 'OK', 'Accept', 'Закрыть'];
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

async function findRuVersion(page, url) {
    const ru = await page.evaluate(() => {
        const alt = document.querySelector('link[rel="alternate"][hreflang^="ru"]');
        if (alt) return alt.href;
        const a = [...document.querySelectorAll('a[href]')].find((el) => {
            const t = el.textContent.trim().toLowerCase();
            return (t === 'ru' || t === 'рус' || t === 'русский') || /\/ru\/?$/.test(el.getAttribute('href'));
        });
        return a ? a.href : null;
    });
    return ru || url;
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
    if (project.prefer_ru) {
        url = await findRuVersion(page, url);
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
    return `<!doctype html><html lang="ru"><head><meta charset="utf-8"><style>
        @font-face { font-family: U; src: url('file://${root}/node_modules/@fontsource-variable/unbounded/files/unbounded-cyrillic-wght-normal.woff2') format('woff2'); unicode-range: U+0400-04FF; font-weight: 100 900; }
        @font-face { font-family: U; src: url('file://${root}/node_modules/@fontsource-variable/unbounded/files/unbounded-latin-wght-normal.woff2') format('woff2'); font-weight: 100 900; }
        @font-face { font-family: M; src: url('file://${root}/node_modules/@fontsource-variable/manrope/files/manrope-cyrillic-wght-normal.woff2') format('woff2'); unicode-range: U+0400-04FF; font-weight: 200 800; }
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
        <div class="hero"><span class="ph">Заглушка — заменить скриншотом</span><h1>${project.name}</h1><p>${label}</p><div class="blob"></div><span class="tag">${new URL(project.url).hostname}</span></div>
    </body></html>`;
}

async function capturePlaceholder(browser, project, index) {
    const dir = path.join(outDir, project.slug);
    fs.mkdirSync(dir, { recursive: true });
    const page = await browser.newPage({ viewport: VIEWPORT });
    const types = Object.keys(PAGE_TYPES);
    // Распределяем типы страниц так, чтобы у каждого было по 3 шаблона.
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
        console.log(placeholders ? 'заглушка' : `ok (${Object.keys(manifest[project.slug].pages).join(', ') || 'только обложка'})`);
    } catch (e) {
        failed.push(project.slug);
        console.log(`недоступен: ${e.message.split('\n')[0]}`);
    }
}

await browser.close();
fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 4) + '\n');

if (failed.length) {
    console.log(`\nНе удалось снять: ${failed.join(', ')}. Запустите с --placeholders, чтобы создать заглушки.`);
}
