<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class AdminContactMessageTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->user = User::factory()->create();
    }

    public function test_admin_can_view_contact_messages(): void
    {
        ContactMessage::query()->create([
            'name' => 'Client',
            'email' => 'client@example.com',
            'message' => 'I want to discuss a Laravel project with you.',
        ]);

        $this->actingAs($this->user)
            ->get('/admin/contact-messages')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Admin/ContactMessages/Index')
                ->has('messages.data', 1)
                ->where('messages.data.0.name', 'Client'));
    }

    public function test_admin_can_mark_contact_message_as_read(): void
    {
        $message = ContactMessage::query()->create([
            'name' => 'Client',
            'email' => 'client@example.com',
            'message' => 'I want to discuss a Laravel project with you.',
        ]);

        $this->actingAs($this->user)
            ->patch("/admin/contact-messages/{$message->id}/read")
            ->assertRedirect();

        $this->assertNotNull($message->refresh()->read_at);
    }
}
