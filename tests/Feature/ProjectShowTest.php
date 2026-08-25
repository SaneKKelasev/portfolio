<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Technology;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class ProjectShowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_project_detail_page_displays_published_project(): void
    {
        $project = Project::factory()->create([
            'title' => 'PortfolioHub',
            'slug' => 'portfoliohub',
            'role' => 'Fullstack developer',
            'problem' => 'Show a strong Laravel portfolio project.',
            'solution' => 'Build a compact Inertia application.',
            'result' => 'A public code sample with tests.',
            'published_at' => now(),
        ]);

        $project->images()->create([
            'path' => 'projects/tests/cover.webp',
            'alt' => 'Project cover',
            'sort_order' => 1,
        ]);

        $technology = Technology::query()->create([
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);

        $project->technologies()->attach($technology);

        $this->get('/projects/portfoliohub')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Projects/Show')
                ->where('project.title', 'PortfolioHub')
                ->where('project.role', 'Fullstack developer')
                ->where('project.problem', 'Show a strong Laravel portfolio project.')
                ->where('project.solution', 'Build a compact Inertia application.')
                ->where('project.result', 'A public code sample with tests.')
                ->has('project.images', 1)
                ->has('project.technologies', 1)
                ->where('project.technologies.0.slug', 'laravel')
                ->where('meta.title', 'PortfolioHub — PortfolioHub'));
    }

    public function test_project_detail_page_does_not_display_draft_project(): void
    {
        Project::factory()->create([
            'slug' => 'draft-project',
            'published_at' => null,
        ]);

        $this->get('/projects/draft-project')
            ->assertNotFound();
    }
}
