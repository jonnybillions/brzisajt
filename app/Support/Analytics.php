<?php

namespace App\Support;

/**
 * Koja analitika je uključena. Plausible ne koristi kolačiće i učitava se odmah;
 * GA4 postavlja kolačiće, pa se učitava tek nakon saglasnosti posetioca.
 */
class Analytics
{
    public static function driver(): string
    {
        return match (settings('analytics_driver')) {
            'plausible' => filled(settings('plausible_domain')) ? 'plausible' : 'none',
            'ga4' => self::ga4Id() ? 'ga4' : 'none',
            default => 'none',
        };
    }

    public static function needsConsent(): bool
    {
        return self::driver() === 'ga4';
    }

    public static function ga4Id(): ?string
    {
        $id = (string) settings('ga4_id');

        return preg_match('/^G-[A-Z0-9]{4,20}$/', $id) ? $id : null;
    }
}
