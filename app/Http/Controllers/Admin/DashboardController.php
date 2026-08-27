<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ContactMessageResource;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\ProjectView;
use App\Models\Technology;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                [
                    'label' => 'Опубликовано',
                    'value' => Project::query()->published()->count(),
                ],
                [
                    'label' => 'Черновики',
                    'value' => Project::query()->whereNull('published_at')->count(),
                ],
                [
                    'label' => 'Технологии',
                    'value' => Technology::query()->count(),
                ],
                [
                    'label' => 'Просмотры за 7 дней',
                    'value' => ProjectView::query()
                        ->where('viewed_at', '>=', now()->subDays(6)->startOfDay())
                        ->count(),
                ],
            ],
            'analytics' => [
                'viewsByDay' => $this->viewsByDay(),
                'messagesByDay' => $this->messagesByDay(),
                'topProjects' => $this->topProjects(),
                'technologyUsage' => $this->technologyUsage(),
            ],
            'latestMessages' => ContactMessageResource::collection(ContactMessage::query()
                ->latest()
                ->limit(5)
                ->get([
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'read_at',
                ]))->resolve($request),
            'meta' => [
                'title' => 'Панель управления — PortfolioHub',
                'description' => 'Панель управления PortfolioHub.',
            ],
        ]);
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    private function viewsByDay(): array
    {
        $start = now()->subDays(6)->startOfDay();
        $totals = ProjectView::query()
            ->selectRaw('DATE(viewed_at) as day, COUNT(*) as total')
            ->where('viewed_at', '>=', $start)
            ->groupBy(DB::raw('DATE(viewed_at)'))
            ->pluck('total', 'day');

        return $this->dailySeries($totals, $start);
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    private function messagesByDay(): array
    {
        $start = now()->subDays(6)->startOfDay();
        $totals = ContactMessage::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->where('created_at', '>=', $start)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total', 'day');

        return $this->dailySeries($totals, $start);
    }

    /**
     * @return list<array{title: string, views: int}>
     */
    private function topProjects(): array
    {
        return Project::query()
            ->published()
            ->withCount([
                'views as views_count' => fn ($query) => $query
                    ->where('viewed_at', '>=', now()->subDays(29)->startOfDay()),
            ])
            ->orderByDesc('views_count')
            ->orderBy('title')
            ->limit(5)
            ->get(['id', 'title'])
            ->map(fn (Project $project): array => [
                'title' => $project->title,
                'views' => (int) $project->views_count,
            ])
            ->all();
    }

    /**
     * @return list<array{name: string, projects: int}>
     */
    private function technologyUsage(): array
    {
        return Technology::query()
            ->withCount([
                'projects' => fn ($query) => $query->published(),
            ])
            ->orderByDesc('projects_count')
            ->orderBy('name')
            ->limit(6)
            ->get(['id', 'name'])
            ->map(fn (Technology $technology): array => [
                'name' => $technology->name,
                'projects' => (int) $technology->projects_count,
            ])
            ->all();
    }

    /**
     * @param  Collection<string, int>  $totals
     * @return array{labels: list<string>, values: list<int>}
     */
    private function dailySeries($totals, Carbon $start): array
    {
        $labels = [];
        $values = [];

        foreach (range(0, 6) as $offset) {
            $date = $start->copy()->addDays($offset);
            $key = $date->toDateString();

            $labels[] = $date->format('d.m');
            $values[] = (int) ($totals[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}
