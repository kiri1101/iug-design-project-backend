<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;

class LeaveType extends Model
{
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
     * Scope a query to retrieve the leave type with uuid.
     */
    #[Scope]
    protected function withUuid(Builder $query, string $id): void
    {
        $query->where('uuid', $id);
    }
}
