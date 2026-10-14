<?php

namespace App\Enums;

/** Filteri u sekciji „Primeri“. */
enum Industry: string
{
    case Restaurants = 'restaurants';
    case Architecture = 'architecture';
    case Business = 'business';

    public function label(): string
    {
        return __("site.industries.{$this->value}");
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
