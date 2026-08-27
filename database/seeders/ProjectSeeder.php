<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
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
        $technologyIds = Technology::query()->pluck('id', 'slug');

        $this->copyProjectImages();

        $this->seedPortfolioHub($technologyIds);
        $this->seedSupportingProjects($technologyIds);
    }

    /**
     * @param  Collection<string, int>  $technologyIds
     */
    private function seedPortfolioHub(Collection $technologyIds): void
    {
        $project = Project::query()->updateOrCreate(
            [
                'slug' => 'portfoliohub',
            ],
            [
                'title' => 'PortfolioHub',
                'description' => 'Платформа для публикации проектов и демонстрации профессионального портфолио.',
                'problem' => 'Нужно было собрать компактный публичный проект, который показывает Laravel, Inertia, Vue, работу с БД, тесты и CI/CD без искусственного усложнения.',
                'solution' => 'Проект построен вокруг опубликованных кейсов: модели связаны через Eloquent, данные отдаются через Resources, frontend получает готовый Inertia-контракт, а критичные сценарии покрыты feature tests.',
                'result' => 'Получился небольшой, но цельный portfolio-проект с галереей, технологиями, публичными страницами и предсказуемым deploy pipeline.',
                'website_url' => 'https://portfoliohub.ru',
                'repository_url' => 'https://github.com/SaneKKelasev/portfolio',
                'started_at' => now()->subMonths(2)->startOfMonth(),
                'finished_at' => now()->startOfMonth(),
                'published_at' => now(),
                'sort_order' => 1,
                'is_protected' => true,
            ],
        );

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
            $technologyIds
                ->only([
                    'php',
                    'laravel',
                    'vue',
                    'inertia',
                    'mysql',
                ])
                ->values()
                ->all(),
        );
    }

    /**
     * @param  Collection<string, int>  $technologyIds
     */
    private function seedSupportingProjects(Collection $technologyIds): void
    {
        $projects = [
            [
                'slug' => 'taskflow',
                'title' => 'TaskFlow',
                'description' => 'Мини-система управления задачами с досками, статусами и фильтрацией.',
                'problem' => 'Команде нужен был простой способ отслеживать задачи без перегруженного интерфейса.',
                'solution' => 'Сценарии задач разделены на статусы, выборки оптимизированы под частые фильтры, а интерфейс сфокусирован на быстрых действиях.',
                'result' => 'Проект демонстрирует CRUD-подход, связи между сущностями и аккуратную работу с пользовательскими фильтрами.',
                'website_url' => null,
                'repository_url' => null,
                'started_at' => now()->subMonths(7)->startOfMonth(),
                'finished_at' => now()->subMonths(5)->startOfMonth(),
                'published_at' => now()->subDays(10),
                'sort_order' => 2,
                'is_protected' => true,
                'technologies' => [
                    'php',
                    'laravel',
                    'mysql',
                ],
            ],
            [
                'slug' => 'metricboard',
                'title' => 'MetricBoard',
                'description' => 'Дашборд для просмотра продуктовых метрик и быстрых операционных срезов.',
                'problem' => 'Метрики хранились разрозненно, и пользователям было сложно быстро понять состояние проекта.',
                'solution' => 'Данные сгруппированы в понятные виджеты, а frontend показывает только нужные для принятия решения показатели.',
                'result' => 'Получился быстрый dashboard с понятной структурой данных и компактным Vue-интерфейсом.',
                'website_url' => null,
                'repository_url' => null,
                'started_at' => now()->subMonths(5)->startOfMonth(),
                'finished_at' => now()->subMonths(3)->startOfMonth(),
                'published_at' => now()->subDays(20),
                'sort_order' => 3,
                'is_protected' => true,
                'technologies' => [
                    'vue',
                    'inertia',
                    'mysql',
                ],
            ],
        ];

        foreach ($projects as $projectData) {
            $technologySlugs = $projectData['technologies'];
            unset($projectData['technologies']);

            $project = Project::query()->updateOrCreate(
                [
                    'slug' => $projectData['slug'],
                ],
                $projectData,
            );

            $project->technologies()->sync(
                $technologyIds
                    ->only($technologySlugs)
                    ->values()
                    ->all(),
            );
        }
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
