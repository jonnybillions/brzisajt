<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Vrednosti iz config('brzisajt.settings'), preko kojih idu
 * izmene iz admin panela (tabela settings).
 */
class Settings
{
    private const CACHE_KEY = 'brzisajt.settings';

    private ?array $overrides = null;

    public function get(string $key, mixed $default = null): mixed
    {
        $overrides = $this->overrides();

        if (array_key_exists($key, $overrides) && $overrides[$key] !== null && $overrides[$key] !== '') {
            return $overrides[$key];
        }

        return config("brzisajt.settings.$key", $default);
    }

    public function int(string $key): int
    {
        return (int) $this->get($key);
    }

    /** @return array<string, mixed> */
    public function all(): array
    {
        return collect(array_keys(config('brzisajt.settings')))
            ->mapWithKeys(fn (string $key) => [$key => $this->get($key)])
            ->all();
    }

    /** @param array<string, mixed> $values */
    public function put(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->flush();
    }

    /** @return list<string> */
    public function notifyEmails(): array
    {
        return collect(explode(',', (string) $this->get('notify_emails')))
            ->map(fn (string $email) => trim($email))
            ->filter(fn (string $email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->values()
            ->all();
    }

    public function flush(): void
    {
        $this->overrides = null;
        Cache::forget(self::CACHE_KEY);
    }

    private function overrides(): array
    {
        if ($this->overrides !== null) {
            return $this->overrides;
        }

        try {
            return $this->overrides = Cache::rememberForever(
                self::CACHE_KEY,
                fn () => Schema::hasTable('settings') ? Setting::pluck('value', 'key')->all() : [],
            );
        } catch (Throwable) {
            // Baza još nije spremna (instalacija, build) — radimo sa vrednostima iz konfiguracije.
            return $this->overrides = [];
        }
    }
}
