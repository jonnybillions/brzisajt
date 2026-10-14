<?php

namespace App\Support;

/**
 * Srpska tipografija za kratke tekstove interfejsa: nelomljivi razmaci posle
 * kratkih predloga i veznika, ispred crte i €, unutar brojeva; „navodnici“.
 */
class Typo
{
    public const NBSP = "\u{00A0}";

    /** Kratke reči koje ne treba da ostanu same na kraju reda. */
    private const SHORT_WORDS = [
        'a', 'i', 'u', 'o', 's', 'k', 'sa', 'ka', 'za', 'na', 'od', 'do', 'po', 'iz', 'ne', 'ni', 'da', 'je', 'se', 'li',
        'bez', 'kod', 'pod', 'nad', 'pre', 'uz', 'kroz', 'ili', 'što', 'kao', 'vam', 'mi', 'vi',
    ];

    public static function apply(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $nb = self::NBSP;
        $words = implode('|', array_map(fn ($w) => preg_quote($w, '/'), self::SHORT_WORDS));

        // Predlog/veznik + razmak → nelomljivi razmak (dvaput — za „i u …“).
        for ($i = 0; $i < 2; $i++) {
            $text = preg_replace('/(?<![\p{L}\-])('.$words.')\s+(?=\S)/iu', '$1'.$nb, $text);
        }

        // Broj + razmak + reč/broj/znak: „7 dana“, „20 MB“.
        $text = preg_replace('/(\d)\s+(?=[\p{L}\d€%])/u', '$1'.$nb, $text);

        // Ispred crte i €.
        $text = preg_replace('/\s+(—|€)/u', $nb.'$1', $text);

        // „€/mes.“ ne lomimo posle kose crte.
        $text = str_replace('€/', "€/\u{2060}", $text);

        // "ravni" navodnici → „srpski“.
        $text = preg_replace('/"([^"]*)"/u', '„$1“', $text);

        return $text;
    }

    /** 4500 → „4.500 €“: tačka kao separator hiljada, nelomljivi razmak ispred €. */
    public static function money(int|float $amount, bool $withSign = true): string
    {
        $formatted = number_format($amount, 0, ',', '.');

        return $withSign ? $formatted.self::NBSP.'€' : $formatted;
    }
}
