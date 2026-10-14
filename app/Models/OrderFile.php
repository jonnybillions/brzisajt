<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['path', 'original_name', 'mime', 'size'])]
class OrderFile extends Model
{
    public const DISK = 'orders';

    protected static function booted(): void
    {
        static::deleted(fn (self $file) => Storage::disk(self::DISK)->delete($file->path));
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function humanSize(): string
    {
        return $this->size >= 1048576
            ? number_format($this->size / 1048576, 1, ',', ' ').' MB'
            : max(1, (int) round($this->size / 1024)).' KB';
    }
}
