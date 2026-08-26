<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ContactMessageResource;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'messages' => ContactMessageResource::collection($messages),
            'meta' => [
                'title' => 'Сообщения — Админка',
                'description' => 'Сообщения из контактной формы PortfolioHub.',
            ],
        ]);
    }

    public function show(Request $request, ContactMessage $contactMessage): Response
    {
        return Inertia::render('Admin/ContactMessages/Show', [
            'message' => ContactMessageResource::make($contactMessage)->resolve($request),
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
