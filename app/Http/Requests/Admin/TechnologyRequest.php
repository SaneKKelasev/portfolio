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
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Укажите название технологии.',
            'name.max' => 'Название технологии не должно быть длиннее 100 символов.',
            'name.unique' => 'Такая технология уже есть.',
            'slug.max' => 'Slug не должен быть длиннее 100 символов.',
            'slug.unique' => 'Такой slug уже занят.',
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
