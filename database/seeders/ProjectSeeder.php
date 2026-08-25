<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

final class ProjectSeeder extends Seeder
{
    /**
     * @var array<int, array{
     *     filename: string,
     *     alt: string,
     *     sort_order: int
     * }>
     */
    private const IMAGES = [
        [
            'filename' => 'cover.webp',
            'alt' => 'Главная страница PortfolioHub',
            'sort_order' => 1,
        ],
        [
            'filename' => 'gallery-1.webp',
            'alt' => 'Страница проекта PortfolioHub',
            'sort_order' => 2,
        ],
        [
            'filename' => 'gallery-2.webp',
            'alt' => 'Административная панель PortfolioHub',
            'sort_order' => 3,
        ],
        [
            'filename' => 'gallery-3.webp',
            'alt' => 'Редактирование проекта PortfolioHub',
            'sort_order' => 4,
        ],
        [
            'filename' => 'gallery-4.webp',
            'alt' => 'Адаптивная версия PortfolioHub',
            'sort_order' => 5,
        ],
    ];

    public function run(): void
    {
        $technologyIds = Technology::query()
            ->whereIn('slug', [
                'php',
                'laravel',
                'vue',
                'inertia',
                'mysql',
            ])
            ->pluck('id', 'slug');

        $project = Project::query()->firstOrNew([
            'slug' => 'portfoliohub',
        ]);

        $project->fill([
            'title' => 'PortfolioHub',
            'description' => 'Платформа для публикации проектов и демонстрации профессионального портфолио.',
            'website_url' => 'https://portfoliohub.ru',
            'repository_url' => 'https://github.com/SaneKKelasev/portfolio',
        ]);

        /*
         * Не обновляем дату публикации при каждом повторном запуске сидера.
         */
        $project->published_at ??= now();

        $project->save();

        $this->copyProjectImages();

        foreach (self::IMAGES as $image) {
            $project->images()->updateOrCreate(
                [
                    'sort_order' => $image['sort_order'],
                ],
                [
                    'path' => sprintf(
                        'projects/portfoliohub/%s',
                        $image['filename'],
                    ),
                    'alt' => $image['alt'],
                ],
            );
        }

        /*
         * Удаляем старые изображения, которых больше нет в конфигурации.
         */
        $project->images()
            ->whereNotIn(
                'sort_order',
                array_column(self::IMAGES, 'sort_order'),
            )
            ->delete();

        $project->technologies()->sync(
            $technologyIds->values()->all(),
        );
    }

    private function copyProjectImages(): void
    {
        foreach (self::IMAGES as $image) {
            $sourcePath = database_path(
                sprintf(
                    'seeders/assets/portfoliohub/%s',
                    $image['filename'],
                ),
            );

            if (! is_file($sourcePath)) {
                throw new RuntimeException(
                    sprintf('Project image not found: %s', $sourcePath),
                );
            }

            Storage::disk('public')->put(
                sprintf(
                    'projects/portfoliohub/%s',
                    $image['filename'],
                ),
                file_get_contents($sourcePath),
            );
        }
    }
}
