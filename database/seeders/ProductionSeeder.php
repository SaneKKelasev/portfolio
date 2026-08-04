<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TechnologySeeder::class,
            ProjectSeeder::class,
        ]);
    }
}