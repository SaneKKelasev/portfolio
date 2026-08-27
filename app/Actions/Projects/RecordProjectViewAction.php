<?php

declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;
use App\Models\ProjectView;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

final class RecordProjectViewAction
{
    public function execute(Project $project, Request $request): void
    {
        $viewedAt = Carbon::now();

        if (ProjectView::query()->where([
            'project_id' => $project->id,
            'visitor_hash' => $this->visitorHash($request),
            'viewed_on' => $viewedAt->toDateString(),
        ])->exists()) {
            return;
        }

        ProjectView::query()->create([
            'project_id' => $project->id,
            'visitor_hash' => $this->visitorHash($request),
            'viewed_on' => $viewedAt->toDateString(),
            'viewed_at' => $viewedAt,
        ]);
    }

    private function visitorHash(Request $request): string
    {
        return hash('sha256', implode('|', [
            $request->ip(),
            substr((string) $request->userAgent(), 0, 255),
        ]));
    }
}
