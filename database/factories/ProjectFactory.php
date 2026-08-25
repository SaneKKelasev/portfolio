<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 1_000_000),
            'description' => fake()->paragraphs(3, true),
            'website_url' => fake()->boolean(70) ? fake()->url() : null,
            'repository_url' => fake()->boolean(60) ? fake()->url() : null,
            'published_at' => fake()->boolean(75)
                ? fake()->dateTimeBetween('-3 years', 'now')
                : null,
        ];
    }
}
