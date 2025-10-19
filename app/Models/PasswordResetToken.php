<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordResetToken extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'token',
    ];

    // Scopes

    /**
     * Scope a query to retrieve the user with the phone number.
     */
    #[Scope]
    protected function withToken(Builder $query, string $token): void
    {
        $query->where('token', $token);
    }

    // Relationships

    /**
     * Get the user that owns the PasswordResetToken
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
