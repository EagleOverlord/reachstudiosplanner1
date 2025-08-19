<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get users that belong to this team
     */
    public function users()
    {
        return $this->hasMany(User::class, 'team', 'key');
    }

    /**
     * Get team name by key
     */
    public static function getTeamName($key)
    {
        $team = static::where('key', $key)->first();
        return $team ? $team->name : ucfirst($key);
    }

    /**
     * Get all teams as key-value pairs
     */
    public static function getTeamsArray()
    {
        return static::where('is_active', true)->pluck('name', 'key')->toArray();
    }

    /**
     * Get team statistics
     */
    public static function getTeamStats()
    {
        $teams = static::with('users')->where('is_active', true)->get();
        $stats = [];

        foreach ($teams as $team) {
            $stats[$team->key] = [
                'name' => $team->name,
                'count' => $team->users->count(),
                'admin_count' => $team->users->where('admin_status', 'yes')->count(),
            ];
        }

        return $stats;
    }
}
