<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use App\Services\VersioningService;
use Illuminate\Http\JsonResponse;

final class CompanyController
{
    public function __invoke(CompanyRequest $request, VersioningService $versioning): JsonResponse
    {
        $values = $request->validated();

        $result = $versioning->process(
            modelClass: Company::class,
            attributes: ['edrpou' => $values['edrpou']],
            values: $values
        );

        return response()->json([
            'status' => $result->status->value,
            'company_id' => $result->modelId,
            'version' => $result->revision,
        ]);
    }
}
