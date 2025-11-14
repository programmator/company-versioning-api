<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\VersionResource;
use App\Models\Company;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class CompanyVersionController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(string $edrpou): ResourceCollection
    {
        $company = Company::query()
            ->where('edrpou', $edrpou)
            ->firstOrFail();

        return $company->versions()
            ->orderByDesc('revision')
            ->get()
            ->toResourceCollection(VersionResource::class);
    }
}
