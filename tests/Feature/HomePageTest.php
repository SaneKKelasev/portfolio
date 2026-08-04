<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_projects_page(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertInertia(
                fn (Assert $page): Assert => $page
                    ->component('Home/Index')
                    ->has('projects', 0),
            );
    }
}