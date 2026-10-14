# Brzisajt (SR)

Prodajni sajt proizvoda „Brzisajt“ agencije otlr.digital. Samo srpski jezik, latinica; bez prebacivanja jezika i bez linkova ka drugim jezičkim verzijama.

## Stek
Laravel 13 (PHP 8.3+), Livewire 4 + Alpine (iz Livewire paketa), Filament 5 (`/admin`, sr_Latn), Tailwind CSS 4 preko Vite-a, Pest 4.
Lokalno SQLite, u produkciji MySQL/MariaDB. Redovi — database.

## Pravila
- Svi tekstovi interfejsa — u `lang/sr/*.php`, ne u Blade-u. U šablonima: `tx('ključ')` (prevod + tipografija + `:price`) ili `typo()`.
- Cene i kontakt — `config/brzisajt.php`, menjaju se u admin panelu; čitamo ih preko `settings('key')`.
- Cene formatiramo sa `money()`: `600 €`, `4.500 €` — tačka za hiljade, nelomljivi razmak ispred €.
- Navodnici „ovako“, ispravna slova č ć š ž đ svuda; slugovi: `Str::slug($s, '-', 'sr')`.
- Fontovi samo lokalno iz `@fontsource-variable/*` (latinica + `resources/fonts/*-sr-wght.woff2` za č ć š ž đ). Bez Google Fonts CDN-a.
- Spoljni servisi samo uz podešavanje: Plausible / GA4 (GA4 tek nakon saglasnosti za kolačiće), Cloudflare Turnstile, Telegram obaveštenja.
- Kontrast: na lime / sky / coral — samo taman tekst (`text-ink`); violet tekst na tamnoj pozadini — `text-violet-soft`.
- Nove pakete dodajemo samo uz dogovor.

## Komande
- `composer setup` — prva instalacija
- `composer dev` — server, red, logovi i Vite
- `php artisan test` — testovi (Pest)
- `npm run build` — build frontenda
