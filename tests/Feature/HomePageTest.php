<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Technology;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class HomePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_home_page_renders_published_projects_with_relations(): void
    {
        $project = Project::factory()->create([
            'title' => 'PortfolioHub',
            'slug' => 'portfoliohub',
            'repository_url' => 'https://github.com/SaneKKelasev/portfolio',
            'published_at' => now(),
        ]);

        $project->images()->create([
            'path' => 'projects/tests/cover.webp',
            'alt' => 'PortfolioHub cover',
            'sort_order' => 1,
        ]);

        $technology = Technology::query()->create([
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);

        $project->technologies()->attach($technology);

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Home/Index')
                ->has('projects', 1)
                ->where('projects.0.title', 'PortfolioHub')
                ->where('projects.0.slug', 'portfoliohub')
                ->where('projects.0.repository_url', 'https://github.com/SaneKKelasev/portfolio')
                ->has('projects.0.images', 1)
                ->where('projects.0.images.0.alt', 'PortfolioHub cover')
                ->has('projects.0.technologies', 1)
                ->where('projects.0.technologies.0.slug', 'laravel'));
    }

    public function test_home_page_does_not_include_unpublished_projects(): void
    {
        Project::factory()->create([
            'title' => 'Published project',
            'published_at' => now(),
        ]);

        Project::factory()->create([
            'title' => 'Draft project',
            'published_at' => null,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->has('projects', 1)
                ->where('projects.0.title', 'Published project'));
    }

    public function test_home_page_orders_published_projects_by_date_and_limits_results(): void
    {
        foreach (range(1, 8) as $index) {
            Project::factory()->create([
                'title' => "Project {$index}",
                'published_at' => now()->subDays(8 - $index),
            ]);
        }

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->has('projects', 6)
                ->where('projects.0.title', 'Project 8')
                ->where('projects.1.title', 'Project 7')
                ->where('projects.5.title', 'Project 3'));
    }
}
