<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enum\VersionStatus;

final readonly class VersioningResult
{
    public function __construct(
        public VersionStatus $status,
        public int $modelId,
        public int $revision,
    ) {}
}
