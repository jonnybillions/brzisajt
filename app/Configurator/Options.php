<?php

namespace App\Configurator;

use App\Enums\PageType;

/**
 * Opcije odgovora u konfiguratoru. Natpisi — u lang/sr/cfg.php.
 */
class Options
{
    public const TYPES = ['onepage', 'multipage'];

    public const SCROLLS = ['classic', 'slides', 'horizontal'];

    public const CUSTOM_PAGE = 'custom';

    public const HEROES = ['slider', 'video', 'image'];

    public const MENU_TYPES = ['regular', 'burger'];

    public const MENU_POSITIONS = ['top', 'side'];

    public const MOTIONS = ['minimal', 'smooth', 'expressive'];

    public const LANGS = ['one', 'two'];

    /** „Kontakt forma“ je uvek uključena i nije na ovom spisku. */
    public const FEATURES = ['catalog', 'map', 'messengers'];

    public const TEXTS = ['have', 'help'];

    public const IMAGES = ['own', 'stock'];

    public const LOGOS = ['have', 'new'];

    public const CHANNELS = ['call', 'viber', 'whatsapp', 'email'];

    public const STYLE_TRUST = 'trust';

    public const MAX_COLORS = 3;

    /** @return list<string> */
    public static function pages(): array
    {
        return [...array_map(fn (PageType $t) => $t->value, PageType::cases()), self::CUSTOM_PAGE];
    }

    /** @return list<string> */
    public static function industries(): array
    {
        return array_keys(__('cfg.industry.options'));
    }

    public static function maxPages(): int
    {
        return (int) config('brzisajt.limits.max_pages', 7);
    }
}
