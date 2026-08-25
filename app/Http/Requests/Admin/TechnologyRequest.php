<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

final class TechnologyRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (! $this->filled('slug') && is_string($this->input('name'))) {
            $this->merge([
                'slug' => Str::slug($this->input('name')),
            ]);
        }
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        $technology = $this->route('technology');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('technologies', 'name')->ignore($technology?->id),
            ],
            'slug' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('technologies', 'slug')->ignore($technology?->id),
            ],
        ];
    }

    /**
     * @return array{name: string, slug: string}
     */
    public function technologyData(): array
    {
        $validated = $this->validated();

        return [
            'name' => $validated['name'],
            'slug' => $validated['slug'],
        ];
    }
}
