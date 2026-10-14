<?php

namespace App\Livewire\Forms;

use App\Configurator\Options;
use Livewire\Form;

/** Svi odgovori iz konfiguratora. Podrazumevane vrednosti — da pregled odmah bude živ. */
class OrderForm extends Form
{
    // Korak 1 — Struktura
    public ?string $type = null;

    public string $scroll = 'classic';

    /** @var list<string> */
    public array $pages = ['home'];

    public string $customPage = '';

    /** @var array<string, int|null> tip stranice → id šablona */
    public array $pageStyles = [];

    // Korak 2 — Izgled
    public string $style = Options::STYLE_TRUST;

    public string $hero = 'slider';

    public string $menuType = 'regular';

    public string $menuPosition = 'top';

    public string $motion = 'smooth';

    public bool $colorsAuto = true;

    /** @var list<string> */
    public array $colors = [];

    // Korak 3 — Sadržaj
    public ?string $industry = null;

    public string $langs = 'one';

    public string $secondLang = '';

    /** @var list<string> */
    public array $features = [];

    public ?string $texts = null;

    public ?string $images = null;

    public ?string $logo = null;

    public bool $seo = false;

    public string $cloudLink = '';

    // Korak 4 — Kontakt
    public string $name = '';

    public string $company = '';

    public string $phone = '';

    public string $email = '';

    public string $channel = 'call';

    public string $comment = '';

    public bool $consent = false;

    /** Polja koja čuvamo u localStorage i vraćamo. Saglasnost — ne. */
    public static function persistable(): array
    {
        return [
            'type', 'scroll', 'pages', 'customPage', 'pageStyles', 'style', 'hero', 'menuType', 'menuPosition',
            'motion', 'colorsAuto', 'colors', 'industry', 'langs', 'secondLang', 'features', 'texts', 'images',
            'logo', 'seo', 'cloudLink', 'name', 'company', 'phone', 'email', 'channel', 'comment',
        ];
    }

    /** Vraćanje iz localStorage: samo poznati ključevi i odgovarajući tipovi. */
    public function restore(array $data): void
    {
        foreach (self::persistable() as $key) {
            if (! array_key_exists($key, $data)) {
                continue;
            }

            $value = $data[$key];
            $current = $this->{$key};

            $ok = match (true) {
                is_array($current) => is_array($value),
                is_bool($current) => is_bool($value),
                default => is_string($value) || $value === null,
            };

            if ($ok) {
                $this->{$key} = is_array($value) ? array_slice($value, 0, 20, true) : $value;
            }
        }

        if (! in_array('home', $this->pages, true)) {
            array_unshift($this->pages, 'home');
        }
    }

    /** Normalizovan skup odgovora za čuvanje u upitu. */
    public function config(): array
    {
        $onepage = $this->type === 'onepage';
        $custom = in_array(Options::CUSTOM_PAGE, $this->pages, true);

        return [
            'type' => $this->type,
            'scroll' => $onepage ? $this->scroll : null,
            'pages' => array_values($this->pages),
            'custom_page' => $custom ? trim($this->customPage) : null,
            'page_styles' => collect($this->pageStyles)
                ->only($this->pages)
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->all(),
            'style' => $this->style,
            'hero' => $this->hero,
            'menu_type' => $this->menuType,
            'menu_position' => $this->menuPosition,
            'motion' => $this->motion,
            'colors' => $this->colorsAuto ? [] : array_values(array_unique(array_map('strtoupper', $this->colors))),
            'industry' => $this->industry,
            'langs' => $this->langs,
            'second_lang' => $this->langs === 'two' ? trim($this->secondLang) : null,
            'features' => array_values($this->features),
            'texts' => $this->texts,
            'images' => $this->images,
            'logo' => $this->logo,
            'seo' => $this->seo,
        ];
    }

    /** @return list<string> */
    public function extras(): array
    {
        return array_values(array_filter([
            $this->logo === 'new' ? 'logo' : null,
            $this->seo ? 'seo' : null,
        ]));
    }
}
