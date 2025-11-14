<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

interface Versionable
{
    /**
     * Get the attributes that should be versioned.
     *
     * @return array<int, string>
     */
    public function versionableAttributes(): array;

    /**
     * Get all of the versions for the model.
     */
    public function versions(): MorphMany;

    /**
     * Get the latest version for the model.
     */
    public function latestVersion(): MorphOne;
}
