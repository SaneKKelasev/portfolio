<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Technology;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\ProjectImage;
use Illuminate\Support\Facades\Storage;

final class ProjectCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'slug'          => $this->slug,
            'description'   => $this->description,
            'website_url'   => $this->website_url,

            'images'        => $this->images
                                ->take(5)
                                ->map(
                                    static fn (ProjectImage $image): array => [
                                        'id'    => $image->id,
                                        'url'   => Storage::disk('public')->url($image->path),
                                        'alt'   => $image->alt,
                                    ],
                                ),

            'technologies'  => $this->technologies->map(
                static fn (Technology $technology): array => [
                    'name' => $technology->name,
                    'slug' => $technology->slug,
                ],
            ),
        ];
    }
}
