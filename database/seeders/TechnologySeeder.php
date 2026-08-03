<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Technology;

final class TechnologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
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
