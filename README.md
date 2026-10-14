# Brzisajt

Prodajni sajt proizvoda „Brzisajt“ agencije [otlr.digital](https://otlr.digital): sajt na Laravelu za 7 radnih dana za 600 €.
Landing stranica, konfigurator-upit (bez online plaćanja), admin panel, obaveštenja timu i klijentu.

Samo srpski jezik, latinica. Sajt je potpuno samostalan — bez prebacivanja jezika i bez linkova ka drugim verzijama.

## Stek

| | |
|---|---|
| Backend | Laravel 13, PHP 8.3+ |
| Interakcija | Livewire 4 + Alpine.js (iz Livewire paketa) |
| Stilovi | Tailwind CSS 4 preko Vite 8 |
| Admin panel | Filament 5 — `/admin`, na srpskoj latinici |
| Baza | SQLite lokalno, MySQL / MariaDB u produkciji |
| Redovi | `database` |
| Testovi | Pest 4 |
| Fontovi | Unbounded + Manrope iz `@fontsource-variable/*`, lokalno |

Na javnom sajtu nema spoljnih CDN-ova ni Google Fonts-a. Spoljni servisi se uključuju samo podešavanjem:
Plausible ili GA4 (GA4 tek nakon saglasnosti za kolačiće), Cloudflare Turnstile i Telegram obaveštenja timu.

## Lokalno pokretanje

```bash
composer setup          # composer install, .env, ključ, migracije, npm install, build
php artisan db:seed     # admin iz .env, logotipi, primeri radova i šabloni stranica
php artisan storage:link
composer dev            # server, red, logovi i Vite istovremeno
```

Sajt — http://localhost:8000, admin panel — http://localhost:8000/admin.
Pristup adminu — `ADMIN_EMAIL` / `ADMIN_PASSWORD` iz `.env` (bez njih seeder ne pravi administratora).

Emailovi se lokalno upisuju u `storage/logs/laravel.log` (`MAIL_MAILER=log`).

```bash
php artisan test        # testovi
vendor/bin/pint         # stil koda
npm run build           # build frontenda
```

## Kako je napravljeno

- **Tekstovi** — `lang/sr/*.php`: `site.php` (landing), `cfg.php` (konfigurator), `mail.php`, `admin.php`, `validation.php`.
  U šablonima — `tx('ključ')`: prevod + tipografija (nelomljivi razmaci posle kratkih reči, ispred crte i €, „navodnici“) + `:price`.
- **Cene i kontakt** — `config/brzisajt.php`, svaka vrednost se menja u admin panelu → „Podešavanja“. U kodu — `settings('key')`.
  Cene se formatiraju sa `money()`: `600 €`, `4.500 €`.
- **Sekcije** — `resources/views/sections/*`. Kontroler — `HomeController`.
- **Konfigurator** — `App\Livewire\Configurator` + `resources/views/livewire/configurator/*`.
  Stanje — `App\Livewire\Forms\OrderForm`, pravila po koracima — `App\Configurator\OrderRules`,
  pregled za emailove, admin i Telegram — `App\Configurator\OrderSummary`.
  Živi pregled i rezime čitaju `$wire.form` direktno u pregledaču, bez zahteva ka serveru.
  Nacrt se čuva u `localStorage` i briše posle slanja. `?style=slug` bira stil projekta. Komponenta se učitava kada se do nje skroluje.
- **Upiti** — `App\Services\OrderService`: čuva upit, fajlove na privatni disk `orders`
  (`storage/app/private/orders`, preuzimaju se samo iz admin panela), stavlja obaveštenja u red.
- **Antispam** — zamka-polje, minimalno vreme popunjavanja (`BRZI_MIN_FILL_SECONDS`), limit upita po IP adresi na sat
  (`BRZI_RATE_LIMIT`), serverska provera svih polja. Cloudflare Turnstile: `CAPTCHA_DRIVER=turnstile` + ključevi.
- **Analitika** — `App\Support\Analytics`: `none`, `plausible` (bez kolačića, bez banera) ili `ga4`
  (baner za saglasnost; GA se učitava tek posle „Prihvatam“; izbor se menja preko „Podešavanja kolačića“ u futeru).
- **Slike** — pri otpremanju naslovne slike ili šablona generišu se WebP + JPEG 480/960/1440 (`App\Support\ImageVariants`),
  prikaz — `<x-picture>` sa `srcset` i dimenzijama. AVIF ne pravimo: GD na serveru ga nema.
- **SVG** (logotipi klijenata i logo sajta) čisti se od skripti pri čuvanju (`App\Support\SvgSanitizer`).
- **Fontovi** — samo latinica + mali fajl sa č ć š ž đ (`resources/fonts/*-sr-wght.woff2`) umesto celog latin-ext skupa;
  rezervni fontovi sa istom metrikom, pa nema pomeranja sadržaja pri učitavanju.

### Skripte za razvoj

| Komanda | Šta radi |
|---|---|
| `npm run capture-examples` | Snima ekrane sajtova iz `database/seeders/data/projects.json` u `database/seeders/assets/projects/` (naslovna + unutrašnje stranice, `manifest.json`), sa engleskom verzijom gde postoji. `-- --placeholders` — zamene za nedostupne sajtove, `-- --only=slug` — jedan projekat. Potreban je Chromium (`CHROMIUM_PATH`). |
| `npm run og -- 600` | Pravi `public/og.png` sa zadatom cenom. Pokrenite ponovo ako promenite cenu u admin panelu. |
| `scripts/subset-diacritics.sh` | Ponovo pravi male fajlove fontova sa slovima č ć š ž đ (potrebno: `pip install fonttools brotli`). |

## Postavljanje na server

Potrebno: PHP 8.3+ sa `gd`, `intl`, `mbstring`, `pdo_mysql`, `zip`, `exif`; MySQL 8 / MariaDB 10.6+; Node 22 za build; nginx + php-fpm.

```bash
git clone … && cd brzisajt
composer install --no-dev --optimize-autoloader
npm ci && npm run build
cp .env.example .env && php artisan key:generate   # popuniti .env, vidi ispod
php artisan migrate --force
php artisan db:seed --force                         # prvi put: admin + sadržaj
php artisan storage:link
php artisan optimize                                # keš konfiguracije, ruta i prikaza
```

**Red** (emailovi i Telegram) — preko supervisor-a ili systemd-a:

```ini
[program:brzisajt-queue]
command=php /var/www/brzisajt/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
```

Posle svakog postavljanja: `php artisan optimize && php artisan queue:restart`.

**Otpremanje fajlova do 20 MB.** `public/.user.ini` podiže limite za php-fpm. Ako je `.user.ini` isključen, u `php.ini` postavite
`upload_max_filesize = 21M`, `post_max_size = 25M`; u nginx-u — `client_max_body_size 25m;`.

**nginx** (deo):

```nginx
root /var/www/brzisajt/public;
client_max_body_size 25m;
gzip on;
gzip_types text/css application/javascript application/json image/svg+xml text/xml application/xml;

location /build/ { expires 1y; add_header Cache-Control "public, immutable"; }
location /storage/ { expires 30d; }
location / { try_files $uri $uri/ /index.php?$query_string; }
location ~ \.php$ { include fastcgi_params; fastcgi_pass unix:/run/php/php8.3-fpm.sock; fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name; }
```

`robots.txt` dozvoljava indeksiranje samo uz `APP_ENV=production`; test okruženja su zatvorena.

## Promenljive okruženja

Standardne Laravel promenljive (`APP_*`, `DB_*`, `MAIL_*`, `QUEUE_CONNECTION=database`) i:

| Promenljiva | Podrazumevano | Namena |
|---|---|---|
| `APP_URL` | — | Produkciona adresa (kanonski linkovi, sitemap, linkovi u emailovima) |
| `APP_TIMEZONE` | `Europe/Belgrade` | Vreme u upitima i emailovima |
| `ADMIN_EMAIL`, `ADMIN_PASSWORD`, `ADMIN_NAME` | — | Administrator koga pravi `AdminSeeder` |
| `ORDER_NOTIFY_EMAILS` | `filip@otlr.digital` | Kome se šalju upiti (odvojeno zarezom). Menja se i u admin panelu |
| `BRZI_BASE_PRICE` | `600` | Osnovna cena, € |
| `BRZI_EXTRA_LOGO_PRICE` | `200` | Logo — od, € |
| `BRZI_EXTRA_SEO_PRICE` | `400` | SEO — od, €/mes. |
| `BRZI_PHONE`, `BRZI_EMAIL` | iz specifikacije | Kontakt na sajtu |
| `BRZI_VIBER`, `BRZI_WHATSAPP` | prazno | Brojevi za Viber/WhatsApp dugmad u kontaktu (skrivena dok su prazna) |
| `ANALYTICS_DRIVER` | `none` | `none`, `plausible` ili `ga4` |
| `PLAUSIBLE_DOMAIN`, `PLAUSIBLE_SCRIPT_URL` | prazno / plausible.io | Plausible (bez kolačića) |
| `GA4_MEASUREMENT_ID` | prazno | GA4 (`G-…`); učitava se tek uz saglasnost |
| `TELEGRAM_BOT_TOKEN`, `TELEGRAM_CHAT_ID` | prazno | Obaveštenja u čet tima; isključena dok nisu podešena oba |
| `BRZI_MIN_FILL_SECONDS` | `8` | Minimalno vreme popunjavanja forme |
| `BRZI_RATE_LIMIT` | `5` | Upita sa jedne IP adrese na sat |
| `CAPTCHA_DRIVER` | `none` | `none` ili `turnstile` |
| `TURNSTILE_SITE_KEY`, `TURNSTILE_SECRET_KEY` | prazno | Ključevi za Cloudflare Turnstile |
| `MAIL_FROM_ADDRESS` | `noreply@brzisajt.rs` | Pošiljalac emailova — zameniti pravim domenom |

## Otvorena pitanja ([TODO])

- Tekst politike privatnosti (Zakon o zaštiti podataka o ličnosti) — `resources/views/privacy.blade.php`, sada vidljiva zamena.
- Pravi logotipi klijenata — sada tekstualne SVG zamene (admin → „Klijenti (logotipi)“).
- SVG logo sajta — admin → „Podešavanja“ (dok ga nema, prikazuje se tekstualni logo).
- Snimci ekrana i tačni nazivi primera — sada zamene: sajtovi nisu bili dostupni iz razvojnog okruženja.
  Pokrenite `npm run capture-examples`, uporedite nazive u `projects.json` sa sajtovima, pa `php artisan db:seed --class=ProjectSeeder`.
- Viber/WhatsApp brojevi, ID za analitiku, domen i `MAIL_FROM_ADDRESS`.
