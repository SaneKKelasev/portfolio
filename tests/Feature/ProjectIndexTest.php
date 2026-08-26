<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Technology;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class ProjectIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_project_catalog_lists_published_projects(): void
    {
        Project::factory()->create([
            'title' => 'Published project',
            'published_at' => now(),
        ]);

        Project::factory()->create([
            'title' => 'Draft project',
            'published_at' => null,
        ]);

        $this->get('/projects')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Projects/Index')
                ->has('projects.data', 1)
                ->where('projects.data.0.title', 'Published project')
                ->where('meta.title', 'Проекты — PortfolioHub'));
    }

    public function test_project_catalog_filters_by_technology(): void
    {
        $laravel = Technology::query()->create([
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);

        $vue = Technology::query()->create([
            'name' => 'Vue',
            'slug' => 'vue',
        ]);

        $laravelProject = Project::factory()->create([
            'title' => 'Laravel CRM',
            'published_at' => now(),
        ]);

        $vueProject = Project::factory()->create([
            'title' => 'Vue Dashboard',
            'published_at' => now()->subDay(),
        ]);

        $laravelProject->technologies()->attach($laravel);
        $vueProject->technologies()->attach($vue);

        $this->get('/projects?technology=laravel')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->has('projects.data', 1)
                ->where('projects.data.0.title', 'Laravel CRM')
                ->where('filters.technology', 'laravel'));
    }

    public function test_project_catalog_searches_title_and_description(): void
    {
        Project::factory()->create([
            'title' => 'Billing Platform',
            'description' => 'Internal finance automation.',
            'published_at' => now(),
        ]);

        Project::factory()->create([
            'title' => 'Project Tracker',
            'description' => 'Kanban board for product teams.',
            'published_at' => now()->subDay(),
        ]);

        $this->get('/projects?search=finance')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->has('projects.data', 1)
                ->where('projects.data.0.title', 'Billing Platform')
                ->where('filters.search', 'finance'));
    }
}
