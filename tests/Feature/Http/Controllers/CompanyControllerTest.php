<?php

declare(strict_types=1);

use App\Enum\VersionStatus;
use App\Http\Controllers\CompanyController;
use App\Models\Company;
use Database\Seeders\CompanySeeder;

use function Pest\Laravel\postJson;
use function Pest\Laravel\seed;

beforeEach(function (): void {
    seed(CompanySeeder::class);
});

it('company can be created', function (): void {
    $uri = action(CompanyController::class);

    $data = Company::factory()->make()->toArray();

    $response = postJson($uri, $data);

    $response->assertOk();

    $id = $response->json('company_id');

    $response->assertJson([
        'status' => VersionStatus::Created->value,
        'company_id' => $id,
        'version' => 1,
    ]);

    $company = Company::query()->find($id);

    expect($company)->not->toBeNull()
        ->and($company->name)->toBe($data['name'])
        ->and($company->edrpou)->toBe($data['edrpou'])
        ->and($company->address)->toBe($data['address'])
        ->and($company->versions()->count())->toBe(1);
});

it('company can be updated with the same edrpou', function (): void {
    $uri = action(CompanyController::class);

    $response = postJson($uri, [
        'name' => 'ТОВ Українська енергетична біржа - оновлена',
        'edrpou' => '37027819',
        'address' => '01001, Україна, м. Київ, вул. Хрещатик, 44',
    ]);

    $response->assertOk();

    $id = $response->json('company_id');

    $response->assertJson([
        'status' => VersionStatus::Updated->value,
        'company_id' => $id,
        'version' => 2,
    ]);

    expect(Company::query()->count())->toBe(1);

    $company = Company::query()->find($id);

    expect($company)->not->toBeNull()
        ->and($company->name)->toBe('ТОВ Українська енергетична біржа - оновлена')
        ->and($company->edrpou)->toBe('37027819')
        ->and($company->address)->toBe('01001, Україна, м. Київ, вул. Хрещатик, 44')
        ->and($company->versions()->count())->toBe(2);
});

it('version with the same data should be ignored', function (): void {
    $uri = action(CompanyController::class);

    $response = postJson($uri, [
        'name' => 'ТОВ Українська енергетична біржа',
        'edrpou' => '37027819',
        'address' => '01001, Україна, м. Київ, вул. Хрещатик, 44',
    ]);

    $response->assertOk();

    $id = $response->json('company_id');

    $response->assertJson([
        'status' => VersionStatus::Duplicate->value,
        'company_id' => $id,
        'version' => 1,
    ]);

    expect(Company::query()->count())->toBe(1);

    $company = Company::query()->find($id);

    expect($company)->not->toBeNull()
        ->and($company->name)->toBe('ТОВ Українська енергетична біржа')
        ->and($company->edrpou)->toBe('37027819')
        ->and($company->address)->toBe('01001, Україна, м. Київ, вул. Хрещатик, 44')
        ->and($company->versions()->count())->toBe(1);
});
