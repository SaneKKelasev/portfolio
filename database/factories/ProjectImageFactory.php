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
            'path' => sprintf('projects/tests/%s.webp', fake()->unique()->uuid()),
            'alt' => fake()->sentence(),
            'sort_order' => fake()->unique()->numberBetween(1, 10_000),
        ];
    }
}
