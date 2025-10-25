<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;

class Role extends Model
{
    use SoftDeletes;

    public const int CEO = 1;
    public const int DHR = 2;
    public const int EMPLOYEE = 8;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'code'
    ];

    // Relationships

    /**
     * The users that belong to the Role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user', 'user_id', 'role_id');
    }

    // Scopes

    /**
     * Scope a query to retrieve the role with uuid.
     */
    #[Scope]
    protected function withUuid(Builder $query, string $id): void
    {
        $query->where('uuid', $id);
    }
}
