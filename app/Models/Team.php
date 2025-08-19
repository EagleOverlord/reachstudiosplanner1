<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory;
    protected $fillable = [
        'key',
        'name',
        'description',
        'is_active',
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
        return static::pluck('name', 'key')->toArray();
    }

    /**
     * Get team statistics
     */
    public static function getTeamStats()
    {
        $teams = static::with('users')->get();
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