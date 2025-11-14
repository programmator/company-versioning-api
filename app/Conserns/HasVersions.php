<?php

declare(strict_types=1);

namespace App\Conserns;

use App\Models\Version;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasVersions
{
    /**
     * Get all of the versions for the model.
     */
    public function versions(): MorphMany
    {
        return $this->morphMany(Version::class, 'versionable');
    }

    /**
     * Get the latest version for the model.
     */
    public function latestVersion(): MorphOne
    {
        return $this->morphOne(Version::class, 'versionable')->latest('revision');
    }
}
