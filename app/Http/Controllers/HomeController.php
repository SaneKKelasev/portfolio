<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectCardResource;
use App\Models\Project;
use Inertia\Inertia;
use Inertia\Response;

final class HomeController extends Controller
{
    public function __invoke(): Response
    {
        $projects = Project::query()
            ->with([
                'images',
                'technologies'
            ])
            ->whereNotNull('published_at')
            ->latest('published_at')
            ->limit(6)
            ->get();
        
        return Inertia::render('Home/Index', [
            'projects' => ProjectCardResource::collection($projects)->resolve(),
        ]);
    }
}