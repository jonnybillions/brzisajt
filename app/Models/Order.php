<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'status', 'name', 'company', 'phone', 'email', 'channel', 'config', 'extras',
    'comment', 'cloud_link', 'consent_at', 'ip', 'user_agent', 'notes',
])]
class Order extends Model
{
    protected $attributes = [
        'status' => 'new',
    ];

    protected static function booted(): void
    {
        static::created(function (self $order) {
            $order->forceFill(['number' => self::formatNumber($order->id)])->saveQuietly();
        });
    }

    public static function formatNumber(int $id): string
    {
        return sprintf('BS-%04d', $id);
    }

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'config' => 'array',
            'extras' => 'array',
            'consent_at' => 'datetime',
        ];
    }

    public function files(): HasMany
    {
        return $this->hasMany(OrderFile::class);
    }

    public function scopeNew(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::New);
    }
}
