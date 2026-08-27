<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectView;
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
                ->where('project.problem', 'Show a strong Laravel portfolio project.')
                ->where('project.solution', 'Build a compact Inertia application.')
                ->where('project.result', 'A public code sample with tests.')
                ->has('project.images', 1)
                ->has('project.technologies', 1)
                ->where('project.technologies.0.slug', 'laravel')
                ->where('meta.title', 'PortfolioHub — PortfolioHub'));

        $this->assertDatabaseHas('project_views', [
            'project_id' => $project->id,
        ]);
    }

    public function test_project_view_is_counted_once_per_visitor_per_day(): void
    {
        $project = Project::factory()->create([
            'slug' => 'portfoliohub',
            'published_at' => now(),
        ]);

        $this->get('/projects/portfoliohub')->assertOk();
        $this->get('/projects/portfoliohub')->assertOk();

        $this->assertSame(1, ProjectView::query()->whereBelongsTo($project)->count());
    }

    public function test_project_detail_page_does_not_display_draft_project(): void
    {
        Project::factory()->create([
            'slug' => 'draft-project',
            'published_at' => null,
        ]);

        $this->get('/projects/draft-project')
            ->assertNotFound();

        $this->assertDatabaseCount('project_views', 0);
    }
}
