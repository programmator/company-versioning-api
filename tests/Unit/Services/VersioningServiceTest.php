<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\VersioningService;

it('throws exception when model does not implement Versionable', function (): void {
    $data = User::factory()->make()->toArray();

    app(VersioningService::class)->process(
        modelClass: User::class,
        attributes: ['email' => $data['email']],
        values: $data
    );
})->throws(InvalidArgumentException::class, 'Model App\Models\User must implement Versionable interface.');
