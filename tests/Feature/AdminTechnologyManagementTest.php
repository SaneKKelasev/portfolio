<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class AdminTechnologyManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->user = User::factory()->create();
    }

    public function test_admin_can_view_technologies_index(): void
    {
        Technology::query()->create([
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);

        $this->actingAs($this->user)
            ->get('/admin/technologies')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Admin/Technologies/Index')
                ->has('technologies', 1)
                ->where('technologies.0.name', 'Laravel'));
    }

    public function test_admin_can_create_update_and_delete_unused_technology(): void
    {
        $this->actingAs($this->user)
            ->post('/admin/technologies', [
                'name' => 'Tailwind CSS',
                'slug' => '',
            ])
            ->assertRedirect();

        $technology = Technology::query()->where('slug', 'tailwind-css')->firstOrFail();

        $this->actingAs($this->user)
            ->put("/admin/technologies/{$technology->id}", [
                'name' => 'Tailwind',
                'slug' => 'tailwind',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('technologies', [
            'id' => $technology->id,
            'name' => 'Tailwind',
            'slug' => 'tailwind',
        ]);

        $this->actingAs($this->user)
            ->delete("/admin/technologies/{$technology->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('technologies', [
            'id' => $technology->id,
        ]);
    }

    public function test_admin_cannot_modify_technology_used_by_protected_project(): void
    {
        $technology = Technology::query()->create([
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);

        $project = Project::factory()->create([
            'is_protected' => true,
        ]);
        $project->technologies()->attach($technology);

        $this->actingAs($this->user)
            ->put("/admin/technologies/{$technology->id}", [
                'name' => 'Changed Laravel',
                'slug' => 'changed-laravel',
            ])
            ->assertForbidden();

        $this->actingAs($this->user)
            ->delete("/admin/technologies/{$technology->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('technologies', [
            'id' => $technology->id,
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);
    }
}
