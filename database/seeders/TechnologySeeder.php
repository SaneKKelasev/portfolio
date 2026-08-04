<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Technology;
use Illuminate\Database\Seeder;

final class TechnologySeeder extends Seeder
{
    public function run(): void
    {
        $technologies = [
            ['name' => 'PHP', 'slug' => 'php'],
            ['name' => 'Laravel', 'slug' => 'laravel'],
            ['name' => 'Vue', 'slug' => 'vue'],
            ['name' => 'Inertia', 'slug' => 'inertia'],
            ['name' => 'MySQL', 'slug' => 'mysql'],
            ['name' => 'Redis', 'slug' => 'redis'],
            ['name' => 'Docker', 'slug' => 'docker'],
        ];

        Technology::query()->upsert(
            $technologies,
            ['slug'],
            ['name'],
        );
    }
}
