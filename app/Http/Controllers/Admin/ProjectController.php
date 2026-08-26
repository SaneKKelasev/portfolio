<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Projects\SaveProjectAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectRequest;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

final class ProjectController extends Controller
{
    public function index(): Response
    {
        $projects = Project::query()
            ->with('technologies')
            ->latest('updated_at')
            ->get();

        return Inertia::render('Admin/Projects/Index', [
            'projects' => $projects->map(fn (Project $project): array => $this->projectRow($project)),
            'meta' => [
                'title' => 'Проекты — Админка',
                'description' => 'Управление проектами PortfolioHub.',
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Projects/Form', [
            'project' => $this->projectFormData(new Project([
                'sort_order' => 100,
            ])),
            'technologies' => $this->technologies(),
            'meta' => [
                'title' => 'Новый проект — Админка',
                'description' => 'Создание проекта PortfolioHub.',
            ],
        ]);
    }

    public function store(ProjectRequest $request, SaveProjectAction $action): RedirectResponse
    {
        $project = $action->execute(
            new Project,
            $request->projectData(),
            $request->technologyIds(),
            $request->images(),
            $request->uploadedImages(),
            $request->uploadedImageMeta(),
        );

        return redirect("/admin/projects/{$project->id}/edit")
            ->with('success', 'Проект создан.');
    }

    public function edit(Project $project): Response
    {
        abort_if($project->is_protected, 403);

        $project->load([
            'images',
            'technologies',
        ]);

        return Inertia::render('Admin/Projects/Form', [
            'project' => $this->projectFormData($project),
            'technologies' => $this->technologies(),
            'meta' => [
                'title' => "Редактирование {$project->title} — Админка",
                'description' => 'Редактирование проекта PortfolioHub.',
            ],
        ]);
    }

    public function update(ProjectRequest $request, Project $project, SaveProjectAction $action): RedirectResponse
    {
        abort_if($project->is_protected, 403);

        $action->execute(
            $project,
            $request->projectData(),
            $request->technologyIds(),
            $request->images(),
            $request->uploadedImages(),
            $request->uploadedImageMeta(),
        );

        return back()->with('success', 'Проект обновлён.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        abort_if($project->is_protected, 403);

        $project->delete();

        return redirect('/admin/projects')
            ->with('success', 'Проект удалён.');
    }

    /**
     * @return list<array{id: int, name: string, slug: string}>
     */
    private function technologies(): array
    {
        return Technology::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'slug',
            ])
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function projectRow(Project $project): array
    {
        return [
            'id' => $project->id,
            'title' => $project->title,
            'slug' => $project->slug,
            'published' => $project->published_at !== null,
            'is_protected' => $project->is_protected,
            'updated_at' => $project->updated_at?->format('Y-m-d H:i'),
            'technologies' => $project->technologies->pluck('name')->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function projectFormData(Project $project): array
    {
        return [
            'id' => $project->id,
            'title' => $project->title,
            'slug' => $project->slug,
            'description' => $project->description,
            'role' => $project->role,
            'problem' => $project->problem,
            'solution' => $project->solution,
            'result' => $project->result,
            'website_url' => $project->website_url,
            'repository_url' => $project->repository_url,
            'started_at' => $project->started_at?->format('Y-m-d'),
            'finished_at' => $project->finished_at?->format('Y-m-d'),
            'published' => $project->published_at !== null,
            'is_protected' => $project->is_protected,
            'sort_order' => $project->sort_order ?? 100,
            'technologies' => $project->technologies?->pluck('id')->all() ?? [],
            'images' => $project->images?->map(fn ($image): array => [
                'path' => $image->path,
                'large_path' => $image->large_path,
                'card_path' => $image->card_path,
                'thumb_path' => $image->thumb_path,
                'url' => Storage::disk('public')->url($image->thumb_path ?? $image->path),
                'alt' => $image->alt,
                'sort_order' => $image->sort_order,
            ])->values()->all() ?? [],
        ];
    }
}
