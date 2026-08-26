<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ProjectIndexRequest extends FormRequest
{
    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'search' => [
                'nullable',
                'string',
                'max:100',
            ],
            'technology' => [
                'nullable',
                'string',
                Rule::exists('technologies', 'slug'),
            ],
        ];
    }

    public function search(): ?string
    {
        $search = $this->validated('search');

        return is_string($search) && $search !== '' ? $search : null;
    }

    public function technology(): ?string
    {
        $technology = $this->validated('technology');

        return is_string($technology) && $technology !== '' ? $technology : null;
    }
}
