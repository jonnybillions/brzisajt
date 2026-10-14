// Генерирует public/og.png (1200×630) — превью для соцсетей и мессенджеров.
//   npm run og            — с базовой ценой 60 000 ₽
//   npm run og -- 65000   — если цена в админке изменилась
// Нужен Chromium: PLAYWRIGHT_BROWSERS_PATH или CHROMIUM_PATH.

import { chromium } from 'playwright';
import fs from 'node:fs';
import os from 'node:os';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const price = Number(process.argv[2] || 60000).toLocaleString('ru-RU').replace(/\s/g, ' ');
const font = (pkg, subset) => `file://${root}/node_modules/@fontsource-variable/${pkg}/files/${pkg}-${subset}-wght-normal.woff2`;

const html = `<!doctype html><html lang="ru"><head><meta charset="utf-8"><style>
@font-face { font-family: U; src: url('${font('unbounded', 'cyrillic')}'); unicode-range: U+0400-04FF; font-weight: 100 900; }
@font-face { font-family: U; src: url('${font('unbounded', 'latin')}'); font-weight: 100 900; }
@font-face { font-family: M; src: url('${font('manrope', 'cyrillic')}'); unicode-range: U+0400-04FF; font-weight: 200 800; }
@font-face { font-family: M; src: url('${font('manrope', 'latin')}'); font-weight: 200 800; }
* { margin: 0; box-sizing: border-box; }
body { width: 1200px; height: 630px; background: #F6F4EF; color: #121016; font-family: M; position: relative; overflow: hidden; }
.blob { position: absolute; right: -160px; top: -200px; width: 760px; height: 760px; filter: blur(70px); opacity: .75; border-radius: 45% 55% 60% 40%;
  background: radial-gradient(circle at 30% 30%, #6B3BFF 0, transparent 55%), radial-gradient(circle at 70% 40%, #3EC5FF 0, transparent 50%), radial-gradient(circle at 45% 75%, #FF5A4E 0, transparent 50%), radial-gradient(circle at 80% 80%, #C8F43A 0, transparent 45%); }
.wrap { position: relative; padding: 64px 72px; height: 100%; display: flex; flex-direction: column; }
.mark { font-family: U; font-weight: 800; font-size: 34px; letter-spacing: -0.02em; }
.mark b { color: #6B3BFF; font-weight: 800; } .mark small { font-family: M; font-size: 16px; font-weight: 700; opacity: .7; margin-left: 8px; }
h1 { margin-top: auto; font-family: U; font-weight: 800; font-size: 76px; line-height: 1.02; letter-spacing: -0.025em; }
.lime { display: inline-block; background: #C8F43A; border-radius: 22px; padding: 0 18px; transform: rotate(-1deg); margin: 8px 0; }
.v { color: #6B3BFF; }
.sticker { position: absolute; right: 80px; bottom: 90px; width: 210px; height: 210px; border-radius: 50%; background: #FF5A4E; padding: 24px; border: 4px solid #121016; box-shadow: 0 8px 0 #121016;
  display: grid; place-items: center; text-align: center; font-family: U; font-weight: 800; font-size: 17px; line-height: 1.1; transform: rotate(12deg); }
.url { position: absolute; left: 72px; bottom: 30px; font-weight: 700; font-size: 18px; opacity: .6; }
</style></head><body><div class="blob"></div><div class="wrap">
<div class="mark"><b>быстро</b>сайт<small>by otlr</small></div>
<h1>Сайт агентского уровня.<br><span class="lime">За 7 дней.</span><br>За <span class="v">${price}&nbsp;₽</span>.</h1>
</div><div class="sticker">Не<br>конструктор!</div><div class="url">otlr.digital</div></body></html>`;

function executablePath() {
    if (process.env.CHROMIUM_PATH) return process.env.CHROMIUM_PATH;
    if (fs.existsSync('/opt/pw-browsers')) {
        const dir = fs.readdirSync('/opt/pw-browsers').find((d) => /^chromium-\d+$/.test(d));
        if (dir) return path.join('/opt/pw-browsers', dir, 'chrome-linux/chrome');
    }
    return undefined;
}

const tmp = path.join(os.tmpdir(), 'bystrosite-og.html');
fs.writeFileSync(tmp, html);
const browser = await chromium.launch({ executablePath: executablePath() });
const page = await browser.newPage({ viewport: { width: 1200, height: 630 } });
await page.goto(`file://${tmp}`, { waitUntil: 'load' });
await page.evaluate(() => document.fonts.ready);
await page.screenshot({ path: path.join(root, 'public/og.png') });
await browser.close();
fs.rmSync(tmp);
console.log('public/og.png готов');
