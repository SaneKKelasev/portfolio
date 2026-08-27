<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TechnologyRequest;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class TechnologyController extends Controller
{
    public function index(): Response
    {
        $technologies = Technology::query()
            ->withCount('projects')
            ->withExists([
                'projects as has_protected_projects' => fn ($query) => $query->where('is_protected', true),
            ])
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Technologies/Index', [
            'technologies' => $technologies->map(fn (Technology $technology): array => [
                'id' => $technology->id,
                'name' => $technology->name,
                'slug' => $technology->slug,
                'projects_count' => $technology->projects_count,
                'has_protected_projects' => (bool) $technology->has_protected_projects,
                'can_update' => ! $technology->has_protected_projects || $this->canManageProtectedContent(),
                'can_delete' => ! $technology->has_protected_projects && $technology->projects_count === 0,
            ]),
            'meta' => [
                'title' => 'Технологии — Админка',
                'description' => 'Управление технологиями PortfolioHub.',
            ],
        ]);
    }

    public function store(TechnologyRequest $request): RedirectResponse
    {
        Technology::query()->create($request->technologyData());

        return back()->with('success', 'Технология добавлена.');
    }

    public function update(TechnologyRequest $request, Technology $technology): RedirectResponse
    {
        $this->ensureTechnologyCanBeChanged($technology);

        $technology->update($request->technologyData());

        return back()->with('success', 'Технология обновлена.');
    }

    public function destroy(Technology $technology): RedirectResponse
    {
        $this->ensureTechnologyCanBeChanged($technology);

        abort_if($technology->projects()->exists(), 409);

        $technology->delete();

        return back()->with('success', 'Технология удалена.');
    }

    private function ensureTechnologyCanBeChanged(Technology $technology): void
    {
        abort_if($this->cannotChange($technology), 403);
    }

    private function cannotChange(Technology $technology): bool
    {
        return $technology->projects()->where('is_protected', true)->exists()
            && ! $this->canManageProtectedContent();
    }

    private function canManageProtectedContent(): bool
    {
        $user = request()->user();

        return $user instanceof User && $user->canManageProtectedContent();
    }
}
