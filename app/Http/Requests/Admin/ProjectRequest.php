<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class ProjectRequest extends FormRequest
{
    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        $project = $this->route('project');

        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('projects', 'slug')->ignore($project?->id),
            ],
            'description' => [
                'required',
                'string',
                'max:5000',
            ],
            'role' => [
                'nullable',
                'string',
                'max:255',
            ],
            'problem' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'solution' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'result' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'website_url' => [
                'nullable',
                'url',
                'max:2048',
            ],
            'repository_url' => [
                'nullable',
                'url',
                'max:2048',
            ],
            'started_at' => [
                'nullable',
                'date',
            ],
            'finished_at' => [
                'nullable',
                'date',
                'after_or_equal:started_at',
            ],
            'published' => [
                'boolean',
            ],
            'sort_order' => [
                'required',
                'integer',
                'min:1',
                'max:10000',
            ],
            'technologies' => [
                'array',
            ],
            'technologies.*' => [
                'integer',
                Rule::exists('technologies', 'id'),
            ],
            'images' => [
                'array',
                'max:8',
            ],
            'images.*.path' => [
                'required',
                'string',
                'max:2048',
            ],
            'images.*.alt' => [
                'nullable',
                'string',
                'max:255',
            ],
            'images.*.sort_order' => [
                'required',
                'integer',
                'min:1',
                'max:10000',
                'distinct',
            ],
            'uploaded_images' => [
                'array',
                'max:8',
            ],
            'uploaded_images.*' => [
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $images = $this->input('images', []);
            $manualImages = is_array($images) ? count($images) : 0;
            $uploadedImages = count($this->uploadedImages());

            if ($manualImages + $uploadedImages > 8) {
                $validator->errors()->add(
                    'uploaded_images',
                    'У проекта может быть не больше 8 изображений.',
                );
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function projectData(): array
    {
        $validated = $this->validated();
        $project = $this->route('project');

        return [
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?: Str::slug($validated['title']),
            'description' => $validated['description'],
            'role' => $validated['role'] ?? null,
            'problem' => $validated['problem'] ?? null,
            'solution' => $validated['solution'] ?? null,
            'result' => $validated['result'] ?? null,
            'website_url' => $validated['website_url'] ?? null,
            'repository_url' => $validated['repository_url'] ?? null,
            'started_at' => $validated['started_at'] ?? null,
            'finished_at' => $validated['finished_at'] ?? null,
            'published_at' => $this->boolean('published')
                ? ($project?->published_at ?? now())
                : null,
            'sort_order' => $validated['sort_order'],
        ];
    }

    /**
     * @return list<int>
     */
    public function technologyIds(): array
    {
        return array_values($this->validated('technologies', []));
    }

    /**
     * @return list<array{path: string, alt?: string|null, sort_order: int}>
     */
    public function images(): array
    {
        return array_values($this->validated('images', []));
    }

    /**
     * @return list<UploadedFile>
     */
    public function uploadedImages(): array
    {
        $files = $this->file('uploaded_images', []);

        if ($files instanceof UploadedFile) {
            return [$files];
        }

        return array_values($files);
    }
}
