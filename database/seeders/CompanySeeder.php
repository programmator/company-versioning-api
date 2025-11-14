<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Services\VersioningService;
use Illuminate\Database\Seeder;

final class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(VersioningService $versioning): void
    {
        $versioning->process(Company::class, [
            'name' => 'ТОВ Українська енергетична біржа',
            'edrpou' => '37027819',
            'address' => '01001, Україна, м. Київ, вул. Хрещатик, 44',
        ]);
    }
}
