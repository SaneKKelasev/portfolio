<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ContactMessageResource;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                [
                    'label' => 'Опубликовано',
                    'value' => Project::query()->published()->count(),
                ],
                [
                    'label' => 'Черновики',
                    'value' => Project::query()->whereNull('published_at')->count(),
                ],
                [
                    'label' => 'Технологии',
                    'value' => Technology::query()->count(),
                ],
                [
                    'label' => 'Новые сообщения',
                    'value' => ContactMessage::query()->whereNull('read_at')->count(),
                ],
            ],
            'latestMessages' => ContactMessageResource::collection(ContactMessage::query()
                ->latest()
                ->limit(5)
                ->get([
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'read_at',
                ]))->resolve($request),
            'meta' => [
                'title' => 'Панель управления — PortfolioHub',
                'description' => 'Панель управления PortfolioHub.',
            ],
        ]);
    }
}
