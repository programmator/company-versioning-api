<?php

declare(strict_types=1);

namespace App\Enum;

enum VersionStatus: string
{
    case Created = 'created';
    case Updated = 'updated';
    case Duplicate = 'duplicate';
}
