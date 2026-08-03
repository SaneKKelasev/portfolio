<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Technology;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $technologyIds = Technology::query()
            ->pluck('id', 'slug');

        $project = Project::query()->updateOrCreate(
            ['slug' => 'portfoliohub'],
            [
                'title' => 'PortfolioHub',
                'description' => 'Платформа для публикации проектов и демонстрации профессионального портфолио.',
                'website_url' => 'https://portfoliohub.ru',
                'repository_url' => 'https://github.com/SaneKKelasev/portfolio',
                'published_at' => now(),
            ],
        );

        $project->images()->delete();

        $project->images()->createMany([
            [
                'path' => 'projects/portfoliohub/cover.webp',
                'alt' => 'Главная страница PortfolioHub',
                'sort_order' => 1,
            ],
            [
                'path' => 'projects/portfoliohub/projects.webp',
                'alt' => 'Список проектов PortfolioHub',
                'sort_order' => 2,
            ],
        ]);

        $project->technologies()->sync([
            $technologyIds->get('php'),
            $technologyIds->get('laravel'),
            $technologyIds->get('vue'),
            $technologyIds->get('inertia'),
            $technologyIds->get('mysql'),
        ]);
    }
}
