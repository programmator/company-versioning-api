<?php

declare(strict_types=1);

use App\Http\Controllers\CompanyVersionController;
use App\Models\Company;
use App\Services\VersioningService;
use Database\Seeders\CompanySeeder;
use Illuminate\Support\Facades\Date;

use function Pest\Laravel\getJson;
use function Pest\Laravel\seed;

beforeEach(function (): void {
    Date::setTestNow('2025-11-15 10:00:00');

    seed(CompanySeeder::class);
});

it('versions can be listed for a company', function (): void {
    app(VersioningService::class)->process(
        modelClass: Company::class,
        attributes: ['edrpou' => '37027819'],
        values: [
            'name' => 'ТОВ Українська енергетична біржа - оновлена назва',
            'edrpou' => '37027819',
            'address' => '01001, Україна, м. Київ, вул. Хрещатик, 44',
        ],
    );

    $uri = action(CompanyVersionController::class, [
        'edrpou' => '37027819',
    ]);

    $response = getJson($uri);

    $response->assertOk();
    $response->assertJsonCount(2);

    $response->assertJson([
        [
            'name' => 'ТОВ Українська енергетична біржа - оновлена назва',
            'edrpou' => '37027819',
            'address' => '01001, Україна, м. Київ, вул. Хрещатик, 44',
            'version' => 2,
            'created_at' => '2025-11-15 10:00:00',
        ],
        [
            'name' => 'ТОВ Українська енергетична біржа',
            'edrpou' => '37027819',
            'address' => '01001, Україна, м. Київ, вул. Хрещатик, 44',
            'version' => 1,
            'created_at' => '2025-11-15 10:00:00',
        ],
    ]);
});

it('404 is returned when company not found', function (): void {
    $uri = action(CompanyVersionController::class, [
        'edrpou' => '99999999',
    ]);

    $response = getJson($uri);

    $response->assertNotFound();
});
