<?php

declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

final class SaveProjectAction
{
    public function __construct(
        private readonly ProjectImageProcessor $imageProcessor,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  list<int>  $technologyIds
     * @param  list<array{path: string, large_path?: string|null, card_path?: string|null, thumb_path?: string|null, alt?: string|null, sort_order: int}>  $images
     * @param  list<UploadedFile>  $uploadedImages
     * @param  list<array{alt?: string|null, sort_order: int}>  $uploadedImageMeta
     */
    public function execute(
        Project $project,
        array $data,
        array $technologyIds,
        array $images,
        array $uploadedImages = [],
        array $uploadedImageMeta = [],
    ): Project {
        $storedPaths = [];

        try {
            return DB::transaction(function () use ($project, $data, $technologyIds, $images, $uploadedImages, $uploadedImageMeta, &$storedPaths): Project {
                $project->fill($data);
                $project->save();

                $project->technologies()->sync($technologyIds);

                foreach ($uploadedImages as $index => $file) {
                    $paths = $this->imageProcessor->process($project, $file);
                    $storedPaths = [
                        ...$storedPaths,
                        ...array_unique(array_values($paths)),
                    ];
                    $meta = $uploadedImageMeta[$index] ?? [];

                    $images[] = [
                        ...$paths,
                        'alt' => $meta['alt'] ?? $this->imageAlt($file),
                        'sort_order' => $meta['sort_order'] ?? $this->nextImageSortOrder($images),
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
     * @param  list<array{path: string, large_path?: string|null, card_path?: string|null, thumb_path?: string|null, alt?: string|null, sort_order: int}>  $images
     */
    private function nextImageSortOrder(array $images): int
    {
        if ($images === []) {
            return 1;
        }

        return max(array_column($images, 'sort_order')) + 1;
    }
}
