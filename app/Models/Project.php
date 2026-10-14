<?php

namespace App\Models;

use App\Enums\Industry;
use App\Models\Concerns\HasResponsiveImages;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'industry', 'url', 'cover', 'sort', 'is_published'])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, HasResponsiveImages;

    protected array $responsiveImages = ['cover' => ['cover_width', 'cover_height']];

    protected function casts(): array
    {
        return [
            'industry' => Industry::class,
            'is_published' => 'boolean',
            'sort' => 'integer',
        ];
    }

    public function pageTemplates(): HasMany
    {
        return $this->hasMany(PageTemplate::class)->orderBy('sort');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('sort')->orderBy('id');
    }
}
