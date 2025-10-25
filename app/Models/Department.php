<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;

class Department extends Model
{
    public const int EXECUTIVE = 1;
    public const int TECHNICAL = 2;
    public const int PRODUCT = 3;
    public const int COMMERCIAL = 4;
    public const int HR = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'name',
    ];

    // Scopes

    /**
     * Scope a query to retrieve the department with uuid.
     */
    #[Scope]
    protected function withUuid(Builder $query, string $id): void
    {
        $query->where('uuid', $id);
    }
}
