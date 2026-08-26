<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProjectIndexRequest;
use App\Http\Resources\ProjectCardResource;
use App\Models\Project;
use App\Models\Technology;
use Inertia\Inertia;
use Inertia\Response;

final class ProjectIndexController extends Controller
{
    public function __invoke(ProjectIndexRequest $request): Response
    {
        $projects = Project::query()
            ->with([
                'images',
                'technologies',
            ])
            ->published()
            ->search($request->search())
            ->withTechnology($request->technology())
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        $technologies = Technology::query()
            ->whereHas('projects', static fn ($query) => $query->published())
            ->orderBy('name')
            ->get([
                'name',
                'slug',
            ]);

        return Inertia::render('Projects/Index', [
            'projects' => ProjectCardResource::collection($projects)->response()->getData(true),
            'technologies' => $technologies,
            'filters' => [
                'search' => $request->search(),
                'technology' => $request->technology(),
            ],
            'meta' => [
                'title' => 'Проекты — PortfolioHub',
                'description' => 'Каталог проектов PortfolioHub с фильтрацией по технологиям и поиском.',
            ],
        ]);
    }
}
