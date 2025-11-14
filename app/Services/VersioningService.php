<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Versionable;
use App\DTOs\VersioningResult;
use App\Enum\VersionStatus;
use App\Models\Version;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final readonly class VersioningService
{
    /**
     * Process versioning for the given model and values.
     */
    public function process(string $modelClass, array $attributes, array $values = []): VersioningResult
    {
        if (! is_subclass_of($modelClass, Versionable::class)) {
            throw new InvalidArgumentException(
                sprintf('Model %s must implement Versionable interface.', $modelClass)
            );
        }

        $model = $modelClass::query()->updateOrCreate(
            $attributes,
            $values
        );

        $fields = $model->versionableAttributes();

        $status = match (true) {
            $model->wasRecentlyCreated => VersionStatus::Created,
            $model->wasChanged($fields) => VersionStatus::Updated,
            default => VersionStatus::Duplicate,
        };

        if ($status !== VersionStatus::Duplicate) {
            $version = $this->createVersion($model);
        }

        return new VersioningResult(
            status: $status,
            modelId: $model->getKey(),
            revision: $version?->revision ?? $model->latestVersion?->revision ?? 0,
        );
    }

    /**
     * Create a new version for the given model.
     */
    private function createVersion(Model&Versionable $model): Version
    {
        return DB::transaction(function () use ($model): Version {
            $revision = ($model->versions()->max('revision') ?? 0) + 1;

            return $model->versions()->create([
                'revision' => $revision,
                'data' => $model->only($model->versionableAttributes()),
            ]);
        });
    }
}
