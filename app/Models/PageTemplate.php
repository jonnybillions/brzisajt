<?php

namespace App\Models;

use App\Enums\PageType;
use App\Models\Concerns\HasResponsiveImages;
use Database\Factories\PageTemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['project_id', 'page_type', 'name', 'image', 'sort'])]
class PageTemplate extends Model
{
    /** @use HasFactory<PageTemplateFactory> */
    use HasFactory, HasResponsiveImages;

    protected array $responsiveImages = ['image' => ['image_width', 'image_height']];

    protected function casts(): array
    {
        return [
            'page_type' => PageType::class,
            'sort' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
