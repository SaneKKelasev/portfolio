<?php

declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;
use Illuminate\Support\Facades\DB;

final class SaveProjectAction
{
    /**
     * @param  array<string, mixed>  $data
     * @param  list<int>  $technologyIds
     * @param  list<array{path: string, alt?: string|null, sort_order: int}>  $images
     */
    public function execute(Project $project, array $data, array $technologyIds, array $images): Project
    {
        return DB::transaction(function () use ($project, $data, $technologyIds, $images): Project {
            $project->fill($data);
            $project->save();

            $project->technologies()->sync($technologyIds);

            $project->images()->delete();

            foreach ($images as $image) {
                $project->images()->create($image);
            }

            return $project->refresh();
        });
    }
}
