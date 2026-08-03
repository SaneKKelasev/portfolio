<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ProjectImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectImage>
 */
final class ProjectImageFactory extends Factory
{
    protected $model = ProjectImage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path'          => fake()->imageUrl(1200, 800, 'technology'),
            'alt'           => fake()->sentence(),
            'sort_order'    => 1,
        ];
    }
}
