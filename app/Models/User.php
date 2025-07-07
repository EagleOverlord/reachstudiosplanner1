<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'admin_status',
        'keys_status',
        'team',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'admin_status' => 'string',
        'keys_status' => 'string',
    ];

    /**
     * Check if user has office keys
     */
    public function hasKeys(): bool
    {
        return $this->keys_status === 'yes';
    }
    
    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->admin_status === 'yes';
    }
    
    /**
     * Get user's shifts
     */
    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }
}
