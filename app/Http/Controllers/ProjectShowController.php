<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Projects\RecordProjectViewAction;
use App\Http\Resources\ProjectDetailResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class ProjectShowController extends Controller
{
    public function __invoke(Project $project, Request $request, RecordProjectViewAction $recordView): Response
    {
        abort_if($project->published_at === null, 404);

        $recordView->execute($project, $request);

        $project->load([
            'images',
            'technologies',
        ]);

        return Inertia::render('Projects/Show', [
            'project' => ProjectDetailResource::make($project)->resolve(),
            'meta' => [
                'title' => "{$project->title} — PortfolioHub",
                'description' => $project->description,
            ],
        ]);
    }
}
