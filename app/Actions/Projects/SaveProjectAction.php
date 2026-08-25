<?php

declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

final class SaveProjectAction
{
    /**
     * @param  array<string, mixed>  $data
     * @param  list<int>  $technologyIds
     * @param  list<array{path: string, alt?: string|null, sort_order: int}>  $images
     * @param  list<UploadedFile>  $uploadedImages
     */
    public function execute(
        Project $project,
        array $data,
        array $technologyIds,
        array $images,
        array $uploadedImages = [],
    ): Project {
        $storedPaths = [];

        try {
            return DB::transaction(function () use ($project, $data, $technologyIds, $images, $uploadedImages, &$storedPaths): Project {
                $project->fill($data);
                $project->save();

                $project->technologies()->sync($technologyIds);

                foreach ($uploadedImages as $file) {
                    $path = $this->storeUploadedImage($project, $file);
                    $storedPaths[] = $path;

                    $images[] = [
                        'path' => $path,
                        'alt' => $this->imageAlt($file),
                        'sort_order' => $this->nextImageSortOrder($images),
                    ];
                }

                $project->images()->delete();

                foreach ($images as $image) {
                    $project->images()->create($image);
                }

                return $project->refresh();
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);

            throw $exception;
        }
    }

    private function storeUploadedImage(Project $project, UploadedFile $file): string
    {
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slug = Str::slug($baseName) ?: 'image';
        $extension = $file->extension() ?: $file->guessExtension() ?: 'jpg';

        $path = $file->storeAs(
            "projects/{$project->slug}",
            sprintf('%s-%s.%s', $slug, Str::uuid(), $extension),
            'public',
        );

        if (! is_string($path)) {
            throw new RuntimeException('Project image upload failed.');
        }

        return $path;
    }

    private function imageAlt(UploadedFile $file): string
    {
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $alt = Str::of($baseName)
            ->replace(['-', '_'], ' ')
            ->squish()
            ->title()
            ->toString();

        return $alt !== '' ? $alt : 'Project image';
    }

    /**
     * @param  list<array{path: string, alt?: string|null, sort_order: int}>  $images
     */
    private function nextImageSortOrder(array $images): int
    {
        if ($images === []) {
            return 1;
        }

        return max(array_column($images, 'sort_order')) + 1;
    }
}
