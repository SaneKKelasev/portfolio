<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class AdminProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->user = User::factory()->create();
    }

    public function test_admin_can_view_projects_index(): void
    {
        Project::factory()->create([
            'title' => 'Admin visible project',
            'published_at' => now(),
            'is_protected' => true,
        ]);

        $this->actingAs($this->user)
            ->get('/admin/projects')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Admin/Projects/Index')
                ->has('projects', 1)
                ->where('projects.0.title', 'Admin visible project')
                ->where('projects.0.is_protected', true));
    }

    public function test_admin_can_create_project_with_technologies_and_images(): void
    {
        $technology = Technology::query()->create([
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);

        $this->actingAs($this->user)
            ->post('/admin/projects', [
                'title' => 'Admin Project',
                'slug' => 'admin-project',
                'description' => 'Project created from admin panel.',
                'problem' => 'Need a CRUD demo.',
                'solution' => 'Use Laravel validation and transactions.',
                'result' => 'Project saved atomically.',
                'website_url' => 'https://example.com',
                'repository_url' => 'https://github.com/example/project',
                'started_at' => '2026-01-01',
                'finished_at' => '2026-02-01',
                'published' => true,
                'technologies' => [
                    $technology->id,
                ],
                'images' => [
                    [
                        'path' => 'projects/admin/cover.webp',
                        'alt' => 'Admin cover',
                        'sort_order' => 1,
                    ],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('projects', [
            'slug' => 'admin-project',
            'title' => 'Admin Project',
            'sort_order' => 10,
        ]);

        $project = Project::query()->where('slug', 'admin-project')->firstOrFail();

        $this->assertNotNull($project->published_at);
        $this->assertDatabaseHas('project_technology', [
            'project_id' => $project->id,
            'technology_id' => $technology->id,
        ]);
        $this->assertDatabaseHas('project_images', [
            'project_id' => $project->id,
            'path' => 'projects/admin/cover.webp',
            'sort_order' => 1,
        ]);
    }

    public function test_admin_can_update_project_atomically(): void
    {
        $oldTechnology = Technology::query()->create([
            'name' => 'PHP',
            'slug' => 'php',
        ]);

        $newTechnology = Technology::query()->create([
            'name' => 'Vue',
            'slug' => 'vue',
        ]);

        $project = Project::factory()->create([
            'title' => 'Old title',
            'slug' => 'old-title',
            'published_at' => null,
        ]);

        $project->technologies()->attach($oldTechnology);
        $project->images()->create([
            'path' => 'projects/old/cover.webp',
            'alt' => 'Old cover',
            'sort_order' => 1,
        ]);

        $this->actingAs($this->user)
            ->put("/admin/projects/{$project->id}", [
                'title' => 'Updated title',
                'slug' => 'updated-title',
                'description' => 'Updated description.',
                'role' => null,
                'problem' => null,
                'solution' => null,
                'result' => null,
                'website_url' => null,
                'repository_url' => null,
                'started_at' => null,
                'finished_at' => null,
                'published' => false,
                'sort_order' => 20,
                'technologies' => [
                    $newTechnology->id,
                ],
                'images' => [
                    [
                        'path' => 'projects/new/cover.webp',
                        'alt' => 'New cover',
                        'sort_order' => 1,
                    ],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'slug' => 'updated-title',
            'title' => 'Updated title',
            'published_at' => null,
            'sort_order' => 20,
        ]);

        $this->assertDatabaseMissing('project_technology', [
            'project_id' => $project->id,
            'technology_id' => $oldTechnology->id,
        ]);
        $this->assertDatabaseHas('project_technology', [
            'project_id' => $project->id,
            'technology_id' => $newTechnology->id,
        ]);
        $this->assertDatabaseMissing('project_images', [
            'project_id' => $project->id,
            'path' => 'projects/old/cover.webp',
        ]);
        $this->assertDatabaseHas('project_images', [
            'project_id' => $project->id,
            'path' => 'projects/new/cover.webp',
        ]);
    }

    public function test_admin_can_upload_project_images(): void
    {
        Storage::fake('public');

        $this->actingAs($this->user)
            ->post('/admin/projects', [
                'title' => 'Uploaded Project',
                'slug' => 'uploaded-project',
                'description' => 'Project with uploaded images.',
                'published' => true,
                'uploaded_images' => [
                    UploadedFile::fake()->image('admin-cover.jpg', 1200, 800),
                ],
            ])
            ->assertRedirect();

        $project = Project::query()->where('slug', 'uploaded-project')->firstOrFail();
        $image = $project->images()->firstOrFail();

        $this->assertSame(1, $image->sort_order);
        $this->assertStringStartsWith('projects/uploaded-project/admin-cover-', $image->path);
        Storage::disk('public')->assertExists($image->path);
    }

    public function test_admin_cannot_modify_protected_project(): void
    {
        $project = Project::factory()->create([
            'slug' => 'protected-project',
            'is_protected' => true,
        ]);

        $this->actingAs($this->user)
            ->get("/admin/projects/{$project->id}/edit")
            ->assertForbidden();

        $this->actingAs($this->user)
            ->put("/admin/projects/{$project->id}", [
                'title' => 'Changed title',
                'slug' => 'protected-project',
                'description' => 'Updated description.',
                'published' => true,
            ])
            ->assertForbidden();

        $this->actingAs($this->user)
            ->delete("/admin/projects/{$project->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => $project->title,
        ]);
    }
}
