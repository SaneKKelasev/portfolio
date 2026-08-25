<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class ContactMessageController extends Controller
{
    public function index(): Response
    {
        $messages = ContactMessage::query()
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/ContactMessages/Index', [
            'messages' => $messages,
            'meta' => [
                'title' => 'Сообщения — Админка',
                'description' => 'Сообщения из контактной формы PortfolioHub.',
            ],
        ]);
    }

    public function show(ContactMessage $contactMessage): Response
    {
        return Inertia::render('Admin/ContactMessages/Show', [
            'message' => $contactMessage,
            'meta' => [
                'title' => "Сообщение от {$contactMessage->name} — Админка",
                'description' => 'Просмотр сообщения из контактной формы.',
            ],
        ]);
    }

    public function markAsRead(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->update([
            'read_at' => now(),
        ]);

        return back()->with('success', 'Сообщение отмечено как прочитанное.');
    }
}
