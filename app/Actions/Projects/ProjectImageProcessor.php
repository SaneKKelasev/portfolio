<?php

declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use RuntimeException;

final class ProjectImageProcessor
{
    private const QUALITY = 82;

    /**
     * @var array<string, array{width: int, height: int}>
     */
    private const VARIANTS = [
        'large' => [
            'width' => 1600,
            'height' => 900,
        ],
        'card' => [
            'width' => 900,
            'height' => 506,
        ],
        'thumb' => [
            'width' => 360,
            'height' => 203,
        ],
    ];

    private readonly ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver);
    }

    /**
     * @return array{path: string, large_path: string, card_path: string, thumb_path: string}
     */
    public function process(Project $project, UploadedFile $file): array
    {
        $directory = "projects/{$project->slug}";
        $baseName = $this->fileBaseName($file);
        $paths = [];

        foreach (self::VARIANTS as $variant => $size) {
            $path = sprintf('%s/%s-%s.webp', $directory, $baseName, $variant);
            $image = $this->manager->decodePath($file->getRealPath());

            Storage::disk('public')->put(
                $path,
                $image
                    ->coverDown($size['width'], $size['height'])
                    ->encode(new WebpEncoder(quality: self::QUALITY, strip: true))
                    ->toString(),
            );

            if (! Storage::disk('public')->exists($path)) {
                throw new RuntimeException('Project image conversion failed.');
            }

            $paths["{$variant}_path"] = $path;
        }

        return [
            'path' => $paths['large_path'],
            'large_path' => $paths['large_path'],
            'card_path' => $paths['card_path'],
            'thumb_path' => $paths['thumb_path'],
        ];
    }

    private function fileBaseName(UploadedFile $file): string
    {
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slug = Str::slug($name) ?: 'image';

        return sprintf('%s-%s', $slug, Str::uuid());
    }
}
