<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\ProjectView;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $this->get('/admin')
            ->assertRedirect('/login');
    }

    public function test_user_can_login_and_view_dashboard(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])
            ->assertRedirect('/admin');

        $this->get('/admin')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Admin/Dashboard')
                ->has('stats')
                ->has('analytics.viewsByDay.labels', 7)
                ->has('analytics.viewsByDay.values', 7)
                ->has('analytics.messagesByDay.labels', 7)
                ->has('analytics.messagesByDay.values', 7));
    }

    public function test_dashboard_displays_portfolio_analytics(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'title' => 'PortfolioHub',
            'published_at' => now(),
        ]);
        $technology = Technology::query()->create([
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);
        $project->technologies()->attach($technology);

        ProjectView::query()->create([
            'project_id' => $project->id,
            'visitor_hash' => hash('sha256', 'visitor'),
            'viewed_on' => now()->toDateString(),
            'viewed_at' => now(),
        ]);
        ContactMessage::query()->create([
            'name' => 'Александр',
            'email' => 'alex@example.com',
            'message' => 'Здравствуйте.',
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Admin/Dashboard')
                ->where('analytics.viewsByDay.values.6', 1)
                ->where('analytics.messagesByDay.values.6', 1)
                ->where('analytics.topProjects.0.title', 'PortfolioHub')
                ->where('analytics.topProjects.0.views', 1)
                ->where('analytics.technologyUsage.0.name', 'Laravel')
                ->where('analytics.technologyUsage.0.projects', 1));
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
