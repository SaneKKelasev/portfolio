<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ContactMessage
 */
final class ContactMessageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
            'created_at' => $this->created_at?->timezone(config('app.display_timezone'))->format('d.m.Y H:i'),
            'read_at' => $this->read_at?->timezone(config('app.display_timezone'))->format('d.m.Y H:i'),
            'is_read' => $this->read_at !== null,
        ];
    }
}
