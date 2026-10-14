# Быстросайт

Продающий сайт продукта «Быстросайт» от [otlr.digital](https://otlr.digital): сайт на Laravel за 7 рабочих дней за 60 000 ₽.
Лендинг, конфигуратор-заявка (без онлайн-оплаты), админка, уведомления команде и клиенту.

Только русский язык. Сайт независим от сербской версии и не ссылается на неё.

## Стек

| | |
|---|---|
| Бэкенд | Laravel 13, PHP 8.3+ |
| Интерактив | Livewire 4 + Alpine.js (из бандла Livewire) |
| Стили | Tailwind CSS 4 через Vite 8 |
| Админка | Filament 5 — `/admin` |
| БД | SQLite локально, MySQL / MariaDB в проде |
| Очереди | `database` |
| Тесты | Pest 4 |
| Шрифты | Unbounded + Manrope из `@fontsource-variable/*`, локально |

На публичном сайте нет внешних CDN, Google/Cloudflare-сервисов и сторонней капчи.
Наружу уходят только: Яндекс Метрика (если задан ID), Telegram-уведомление (если заданы токен и чат) и Яндекс SmartCaptcha (если включена).

## Локальный запуск

```bash
composer setup          # composer install, .env, ключ, миграции, npm install, сборка
php artisan db:seed     # админ из .env, логотипы, примеры работ и шаблоны страниц
php artisan storage:link
composer dev            # сервер, очередь, логи и Vite одновременно
```

Сайт — http://localhost:8000, админка — http://localhost:8000/admin.
Логин и пароль админа — `ADMIN_EMAIL` / `ADMIN_PASSWORD` в `.env` (без них сидер не создаёт админа).

Письма локально пишутся в `storage/logs/laravel.log` (`MAIL_MAILER=log`).

```bash
php artisan test        # тесты
vendor/bin/pint         # код-стайл
npm run build           # сборка фронтенда
```

## Как устроено

- **Тексты** — `lang/ru/*.php`: `site.php` (лендинг), `cfg.php` (конфигуратор), `mail.php`, `admin.php`, `validation.php`.
  В шаблонах — `tx('ключ')`: перевод + типографика (неразрывные пробелы после предлогов, перед тире и ₽) + подстановка `:price`.
- **Цены и контакты** — `config/bystrosite.php`, любое значение переопределяется в админке → «Настройки». В коде — `settings('key')`.
- **Секции лендинга** — `resources/views/sections/*`. Контроллер — `HomeController`.
- **Конфигуратор** — `App\Livewire\Configurator` + `resources/views/livewire/configurator/*`.
  Состояние — в `App\Livewire\Forms\OrderForm`, правила по шагам — `App\Configurator\OrderRules`,
  человекочитаемая сводка — `App\Configurator\OrderSummary` (письма, админка, Telegram).
  Живое превью и итоги читают `$wire.form` прямо в браузере, без запросов к серверу.
  Черновик хранится в `localStorage` и очищается после отправки. `?style=slug` выбирает стиль проекта.
- **Заявки** — `App\Services\OrderService`: сохраняет заявку, файлы — на приватный диск `orders`
  (`storage/app/private/orders`, скачиваются только из админки), ставит уведомления в очередь.
- **Антиспам** — ловушка-поле, минимальное время заполнения (`BYSTRO_MIN_FILL_SECONDS`), лимит заявок с IP в час
  (`BYSTRO_RATE_LIMIT`), серверная проверка всех полей. Яндекс SmartCaptcha включается `CAPTCHA_DRIVER=yandex` + ключи.
- **Картинки** — при загрузке обложки или шаблона генерируются WebP + JPEG 480/960/1440 (`App\Support\ImageVariants`),
  вывод — `<x-picture>` со `srcset` и размерами. AVIF не делаем: в GD на сервере его нет.
- **SVG** (логотипы клиентов и логотип сайта) очищаются от скриптов при сохранении (`App\Support\SvgSanitizer`).

### Скрипты для разработки

| Команда | Что делает |
|---|---|
| `npm run capture-examples` | Снимает скриншоты сайтов из `database/seeders/data/projects.json` в `database/seeders/assets/projects/` (обложка + внутренние страницы, `manifest.json`). `-- --placeholders` — заглушки для недоступных сайтов, `-- --only=slug` — один проект. Нужен Chromium (`CHROMIUM_PATH`). |
| `npm run og -- 60000` | Генерирует `public/og.png` с указанной ценой. Перегенерируйте, если поменяли цену в админке. |
| `scripts/subset-ruble.sh` | Пересобирает крошечные файлы шрифтов со знаком «₽» (нужен `pip install fonttools brotli`). |

## Деплой (сервер в России — 152-ФЗ)

Требования: PHP 8.3+ с `gd`, `intl`, `mbstring`, `pdo_mysql`, `zip`, `exif`; MySQL 8 / MariaDB 10.6+; Node 22 для сборки; nginx + php-fpm.

```bash
git clone … && cd bystrosite
composer install --no-dev --optimize-autoloader
npm ci && npm run build
cp .env.example .env && php artisan key:generate   # заполнить .env, см. ниже
php artisan migrate --force
php artisan db:seed --force                         # первый раз: админ + контент
php artisan storage:link
php artisan optimize                                # кеш конфигурации, роутов, вьюх
```

**Очередь** (письма и Telegram) — под supervisor или systemd:

```ini
[program:bystrosite-queue]
command=php /var/www/bystrosite/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
```

После каждого деплоя: `php artisan optimize && php artisan queue:restart`.

**Загрузка файлов до 20 МБ.** `public/.user.ini` поднимает лимиты для php-fpm. Если `.user.ini` отключён, задайте в `php.ini`:
`upload_max_filesize = 21M`, `post_max_size = 25M`; в nginx — `client_max_body_size 25m;`.

**nginx** (фрагмент):

```nginx
root /var/www/bystrosite/public;
client_max_body_size 25m;
gzip on;
gzip_types text/css application/javascript application/json image/svg+xml text/xml application/xml;

location /build/ { expires 1y; add_header Cache-Control "public, immutable"; }
location /storage/ { expires 30d; }
location / { try_files $uri $uri/ /index.php?$query_string; }
location ~ \.php$ { include fastcgi_params; fastcgi_pass unix:/run/php/php8.3-fpm.sock; fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name; }
```

`robots.txt` разрешает индексацию только при `APP_ENV=production`; на тестовых стендах сайт закрыт.

## Переменные окружения

Стандартные Laravel (`APP_*`, `DB_*`, `MAIL_*`, `QUEUE_CONNECTION=database`) плюс:

| Переменная | По умолчанию | Назначение |
|---|---|---|
| `APP_URL` | — | Боевой адрес сайта (канонические ссылки, sitemap, ссылки в письмах) |
| `APP_TIMEZONE` | `Europe/Moscow` | Время в заявках и письмах |
| `ADMIN_EMAIL`, `ADMIN_PASSWORD`, `ADMIN_NAME` | — | Админ, которого создаёт `AdminSeeder` |
| `ORDER_NOTIFY_EMAILS` | `filip@otlr.digital` | Кому отправлять заявки (через запятую). Можно поменять в админке |
| `BYSTRO_BASE_PRICE` | `60000` | Базовая цена, ₽ |
| `BYSTRO_EXTRA_LOGO_PRICE` | `20000` | Логотип — от, ₽ |
| `BYSTRO_EXTRA_SEO_PRICE` | `35000` | SEO — от, ₽/мес |
| `BYSTRO_PHONE`, `BYSTRO_EMAIL` | из ТЗ | Контакты на сайте |
| `BYSTRO_TELEGRAM`, `BYSTRO_WHATSAPP` | пусто | Ссылки на мессенджеры в контактах (скрыты, пока пусто) |
| `YANDEX_METRICA_ID` | пусто | Счётчик Метрики; без ID скрипт не подключается |
| `TELEGRAM_BOT_TOKEN`, `TELEGRAM_CHAT_ID` | пусто | Уведомления в чат команды; выключены, пока не заданы оба |
| `BYSTRO_MIN_FILL_SECONDS` | `8` | Минимальное время заполнения формы |
| `BYSTRO_RATE_LIMIT` | `5` | Заявок с одного IP в час |
| `CAPTCHA_DRIVER` | `none` | `none` или `yandex` |
| `YANDEX_SMARTCAPTCHA_CLIENT_KEY`, `YANDEX_SMARTCAPTCHA_SERVER_KEY` | пусто | Ключи SmartCaptcha |
| `MAIL_FROM_ADDRESS` | `noreply@bystrosite.ru` | Отправитель писем — заменить на реальный домен |

## Открытые вопросы ([TODO])

- Текст политики конфиденциальности (152-ФЗ) — `resources/views/privacy.blade.php`, сейчас видимая заглушка.
- Реальные логотипы клиентов — сейчас текстовые SVG-заглушки (админка → «Клиенты (логотипы)»).
- SVG-логотип сайта — админка → «Настройки» (пока текстовый вордмарк).
- Скриншоты и точные названия примеров работ — сейчас заглушки: сайты были недоступны из среды разработки.
  Запустите `npm run capture-examples`, сверьте названия в `projects.json` с сайтами, затем `php artisan db:seed --class=ProjectSeeder`.
- Ссылки на Telegram и WhatsApp, ID Яндекс Метрики, домен и `MAIL_FROM_ADDRESS`.
