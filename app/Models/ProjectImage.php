<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProjectImageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ProjectImage extends Model
{
    /** @use HasFactory<ProjectImageFactory> */
    use HasFactory;

    protected $fillable = [
        'path',
        'large_path',
        'card_path',
        'thumb_path',
        'alt',
        'sort_order',
    ];

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
