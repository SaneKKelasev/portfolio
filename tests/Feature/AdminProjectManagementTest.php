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
            'updated_at' => now(),
        ]);
        Project::factory()->create([
            'title' => 'Draft project',
            'published_at' => null,
            'updated_at' => now()->subMinute(),
        ]);

        $this->actingAs($this->user)
            ->get('/admin/projects')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Admin/Projects/Index')
                ->has('projects', 2)
                ->where('projects.0.title', 'Admin visible project')
                ->where('projects.0.is_protected', true)
                ->where('projects.1.title', 'Draft project')
                ->where('projects.1.published', false));
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
                'description' => 'Updated description.',
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
                'description' => 'Project with uploaded images.',
                'published' => true,
                'uploaded_images' => [
                    UploadedFile::fake()->image('detail-view.jpg', 1200, 800),
                    UploadedFile::fake()->image('admin-cover.jpg', 1200, 800),
                ],
                'uploaded_images_meta' => [
                    [
                        'alt' => 'Detail view',
                        'sort_order' => 1,
                    ],
                    [
                        'alt' => 'Cover image',
                        'sort_order' => 2,
                    ],
                ],
            ])
            ->assertRedirect();

        $project = Project::query()->where('slug', 'uploaded-project')->firstOrFail();
        $images = $project->images()->orderBy('sort_order')->get();

        $this->assertCount(2, $images);
        $this->assertSame('Detail view', $images[0]->alt);
        $this->assertSame(1, $images[0]->sort_order);
        $this->assertSame('Cover image', $images[1]->alt);
        $this->assertSame(2, $images[1]->sort_order);
        $this->assertStringStartsWith('projects/uploaded-project/detail-view-', $images[0]->path);
        $this->assertStringEndsWith('-large.webp', $images[0]->path);

        foreach ($images as $image) {
            $this->assertNotNull($image->large_path);
            $this->assertNotNull($image->card_path);
            $this->assertNotNull($image->thumb_path);
            Storage::disk('public')->assertExists($image->large_path);
            Storage::disk('public')->assertExists($image->card_path);
            Storage::disk('public')->assertExists($image->thumb_path);
        }

        $this->assertImageSize($images[0]->large_path, 1200, 675);
        $this->assertImageSize($images[0]->card_path, 900, 506);
        $this->assertImageSize($images[0]->thumb_path, 360, 203);
    }

    public function test_admin_cannot_save_more_than_five_project_images(): void
    {
        Storage::fake('public');

        $uploads = [];
        $meta = [];

        foreach (range(1, 6) as $index) {
            $uploads[] = UploadedFile::fake()->image("project-{$index}.jpg", 1200, 800);
            $meta[] = [
                'alt' => "Project image {$index}",
                'sort_order' => $index,
            ];
        }

        $this->actingAs($this->user)
            ->post('/admin/projects', [
                'title' => 'Too Many Images',
                'description' => 'Project with too many uploaded images.',
                'published' => true,
                'uploaded_images' => $uploads,
                'uploaded_images_meta' => $meta,
            ])
            ->assertSessionHasErrors([
                'uploaded_images' => 'У проекта может быть не больше 5 изображений вместе с главным.',
            ]);
    }

    public function test_demo_admin_cannot_modify_protected_project(): void
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

    public function test_owner_can_modify_protected_project(): void
    {
        $owner = User::factory()->owner()->create();
        $project = Project::factory()->create([
            'title' => 'Protected project',
            'slug' => 'protected-project',
            'is_protected' => true,
        ]);

        $this->actingAs($owner)
            ->get("/admin/projects/{$project->id}/edit")
            ->assertOk();

        $this->actingAs($owner)
            ->put("/admin/projects/{$project->id}", [
                'title' => 'Updated protected project',
                'description' => 'Owner can update protected portfolio projects.',
                'published' => true,
            ])
            ->assertRedirect('/admin/projects');

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'Updated protected project',
            'slug' => 'updated-protected-project',
        ]);
    }

    private function assertImageSize(string $path, int $width, int $height): void
    {
        $size = getimagesize(Storage::disk('public')->path($path));

        $this->assertIsArray($size);
        $this->assertSame($width, $size[0]);
        $this->assertSame($height, $size[1]);
        $this->assertSame('image/webp', $size['mime']);
    }
}
