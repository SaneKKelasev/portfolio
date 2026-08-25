<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\Technology;
use Inertia\Inertia;
use Inertia\Response;

final class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'published_projects' => Project::query()->published()->count(),
                'draft_projects' => Project::query()->whereNull('published_at')->count(),
                'technologies' => Technology::query()->count(),
                'unread_messages' => ContactMessage::query()->whereNull('read_at')->count(),
            ],
            'latestMessages' => ContactMessage::query()
                ->latest()
                ->limit(5)
                ->get([
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'read_at',
                ]),
            'meta' => [
                'title' => 'Админка — PortfolioHub',
                'description' => 'Панель управления PortfolioHub.',
            ],
        ]);
    }
}
