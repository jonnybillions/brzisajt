<?php

namespace Database\Seeders;

use App\Enums\PageType;
use App\Models\PageTemplate;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Primeri radova i šabloni stranica iz database/seeders/assets/projects
 * (snimke ekrana pravi scripts/capture-examples.mjs).
 */
class ProjectSeeder extends Seeder
{
    private const MAX_TEMPLATES_PER_TYPE = 3;

    public function run(): void
    {
        $projects = json_decode(File::get(database_path('seeders/data/projects.json')), true);
        $manifestPath = database_path('seeders/assets/projects/manifest.json');
        $manifest = File::exists($manifestPath) ? json_decode(File::get($manifestPath), true) : [];
        $perType = [];

        foreach ($projects as $sort => $data) {
            $slug = $data['slug'];
            $meta = $manifest[$slug] ?? [];

            $project = Project::updateOrCreate(['slug' => $slug], [
                'name' => $data['name'],
                'industry' => $data['industry'],
                'url' => $meta['url'] ?? $data['url'],
                'cover' => $this->copyAsset($slug, 'cover.jpg', "projects/{$slug}/cover.jpg"),
                'sort' => $sort,
                'is_published' => true,
            ]);

            $types = array_merge(['home'], array_keys($meta['pages'] ?? []));

            foreach ($types as $i => $type) {
                $pageType = PageType::tryFrom($type);
                if (! $pageType || ($perType[$type] ?? 0) >= self::MAX_TEMPLATES_PER_TYPE) {
                    continue;
                }

                $image = $this->copyAsset($slug, $type === 'home' ? 'cover.jpg' : "{$type}.jpg", "templates/{$slug}/{$type}.jpg");
                if (! $image) {
                    continue;
                }

                PageTemplate::updateOrCreate(
                    ['project_id' => $project->id, 'page_type' => $pageType],
                    ['name' => "{$project->name} — {$pageType->label()}", 'image' => $image, 'sort' => $i],
                );
                $perType[$type] = ($perType[$type] ?? 0) + 1;
            }
        }
    }

    private function copyAsset(string $slug, string $file, string $target): ?string
    {
        $source = database_path("seeders/assets/projects/{$slug}/{$file}");
        if (! File::exists($source)) {
            return null;
        }

        Storage::disk('public')->put($target, File::get($source));

        return $target;
    }
}
