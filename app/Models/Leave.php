<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    public const int PENDING = 1;
    public const int AWAIT_SUPERIOR_VALIDATION = 2;
    public const int AWAIT_HR_VALIDATION = 3;
    public const int GRANTED = 4;
    public const int RETURNED_FROM_LEAVE = 5;
    public const int HR_CONFIRM_RETURN = 6;
    public const int RETURN_OVERDUE = 7;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'user_id',
        'type_id',
        'cause',
        'departure',
        'return',
        'status',
    ];

    // Relationships

    /**
     * Get the leaveType that owns the Leave
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class, 'type_id');
    }

    /**
     * Get the user that owns the Leave
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
