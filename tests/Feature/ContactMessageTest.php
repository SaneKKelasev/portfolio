<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ContactMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_stores_message(): void
    {
        $this->from('/')
            ->post('/contact', [
                'name' => 'Alexander',
                'email' => 'alexander@example.com',
                'message' => 'Hello, I would like to discuss a Laravel project.',
            ])
            ->assertRedirect('/')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Alexander',
            'email' => 'alexander@example.com',
            'message' => 'Hello, I would like to discuss a Laravel project.',
        ]);
    }

    public function test_contact_form_validates_payload(): void
    {
        $this->from('/')
            ->post('/contact', [
                'name' => '',
                'email' => 'not-an-email',
                'message' => 'Too short',
            ])
            ->assertRedirect('/')
            ->assertInvalid([
                'name',
                'email',
                'message',
            ]);

        $this->assertDatabaseCount('contact_messages', 0);
    }
}
