<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Shift extends Model
{
    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'location',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Check if this shift is in the future and can be edited
     */
    public function isUpcoming(): bool
    {
        return $this->start_time->isFuture();
    }
    
    /**
     * Check if this shift belongs to the given user
     */
    public function belongsToUser(User $user): bool
    {
        return $this->user_id === $user->id;
    }
    
    /**
     * Get the duration of the shift in hours
     */
    public function getDurationHours(): float
    {
        return $this->end_time->diffInHours($this->start_time, true);
    }
}
