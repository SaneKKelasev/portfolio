<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'problem',
        'solution',
        'result',
        'website_url',
        'repository_url',
        'started_at',
        'finished_at',
        'published_at',
        'sort_order',
        'is_protected',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'finished_at' => 'date',
            'published_at' => 'datetime',
            'is_protected' => 'boolean',
        ];
    }

    /**
     * @param  Builder<Project>  $query
     * @return Builder<Project>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at');
    }

    /**
     * @param  Builder<Project>  $query
     * @return Builder<Project>
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if ($search === null) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($search): void {
            $query
                ->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * @param  Builder<Project>  $query
     * @return Builder<Project>
     */
    public function scopeWithTechnology(Builder $query, ?string $technology): Builder
    {
        if ($technology === null) {
            return $query;
        }

        return $query->whereHas(
            'technologies',
            static fn (Builder $query): Builder => $query->where('slug', $technology),
        );
    }

    /**
     * @return HasMany<ProjectImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)
            ->orderBy('sort_order');
    }

    /**
     * @return BelongsToMany<Technology, $this>
     */
    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class);
    }
}
