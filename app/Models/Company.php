<?php

declare(strict_types=1);

namespace App\Models;

use App\Conserns\HasVersions;
use App\Contracts\Versionable;
use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Company extends Model implements Versionable
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory;

    use HasVersions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'edrpou',
        'address',
    ];

    /**
     * Get the attributes that should be versioned.
     *
     * @return array<int, string>
     */
    public function versionableAttributes(): array
    {
        return [
            'name',
            'edrpou',
            'address',
        ];
    }
}
