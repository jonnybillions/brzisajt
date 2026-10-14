<?php

namespace App\Models\Concerns;

use App\Support\ImageVariants;

/**
 * Model sa slikama na disku public: pri čuvanju generiše varijante
 * i čuva dimenzije originala (za width/height bez pomeranja sadržaja).
 *
 * U modelu: protected array $responsiveImages = ['cover' => ['cover_width', 'cover_height']];
 */
trait HasResponsiveImages
{
    public static function bootHasResponsiveImages(): void
    {
        static::saving(function (self $model) {
            foreach ($model->responsiveImages as $column => [$widthCol, $heightCol]) {
                if (! $model->isDirty($column)) {
                    continue;
                }

                ImageVariants::delete('public', $model->getOriginal($column));

                $size = $model->{$column} ? ImageVariants::generate('public', $model->{$column}) : null;
                $model->{$widthCol} = $size[0] ?? null;
                $model->{$heightCol} = $size[1] ?? null;
            }
        });

        static::deleted(function (self $model) {
            foreach (array_keys($model->responsiveImages) as $column) {
                ImageVariants::delete('public', $model->{$column});
            }
        });
    }
}
