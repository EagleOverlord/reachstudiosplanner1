<?php

namespace App\Livewire\Settings;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Teams extends Component
{
    // Modal states
    public $showCreateTeamModal = false;
    public $showEditTeamModal = false;
    public $showDeleteTeamModal = false;
    public $showMoveUserModal = false;

    // Form fields
    public $newTeamKey = '';
    public $newTeamName = '';
    public $editingTeamKey = '';
    public $editingTeamName = '';
    public $moveUserId = null;
    public $moveToTeam = '';

    // Current selection
    public $selectedTeam = null;

    protected $rules = [
        'newTeamKey' => 'required|string|max:50|unique:teams,key',
        'newTeamName' => 'required|string|max:255',
        'editingTeamName' => 'required|string|max:255',
    ];

    public function mount()
    {
        // Select the first team by default if any exist
        $firstTeam = Team::first();
        if ($firstTeam) {
            $this->selectedTeam = $firstTeam->key;
        }
    }

    public function render()
    {
        return view('livewire.settings.teams', [
            'teamStats' => Team::getTeamStats(),
            'teams' => Team::getTeamsArray(),
            'teamMembers' => $this->getTeamMembers(),
        ]);
    }

    public function selectTeam($teamKey)
    {
        $this->selectedTeam = $teamKey;
    }

    public function getTeamMembers()
    {
        if (!$this->selectedTeam) {
            return [];
        }

        return User::where('team', $this->selectedTeam)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'admin_status' => $user->admin_status,
                    'keys_status' => $user->keys_status,
                ];
            })
            ->toArray();
    }

    public function showCreateModal()
    {
        $this->reset(['newTeamKey', 'newTeamName']);
        $this->showCreateTeamModal = true;
    }

    public function showEditModal($teamKey)
    {
        $team = Team::where('key', $teamKey)->first();
        if ($team) {
            $this->editingTeamKey = $team->key;
            $this->editingTeamName = $team->name;
            $this->showEditTeamModal = true;
        }
    }

    public function showDeleteModal($teamKey)
    {
        $this->editingTeamKey = $teamKey;
        $this->showDeleteTeamModal = true;
    }

    public function showMoveModal($userId)
    {
        $this->moveUserId = $userId;
        $this->moveToTeam = '';
        $this->showMoveUserModal = true;
    }

    public function createTeam()
    {
        $this->validate();

        Team::create([
            'key' => $this->newTeamKey,
            'name' => $this->newTeamName,
        ]);

        $this->closeModals();
        $this->dispatch('team-created', message: 'Team created successfully!');
    }

    public function updateTeam()
    {
        $this->validate(['editingTeamName' => 'required|string|max:255']);

        $team = Team::where('key', $this->editingTeamKey)->first();
        if ($team) {
            $team->update(['name' => $this->editingTeamName]);
            $this->closeModals();
            $this->dispatch('team-updated', message: 'Team updated successfully!');
        }
    }

    public function deleteTeam()
    {
        $team = Team::where('key', $this->editingTeamKey)->first();
        if ($team) {
            // Check if team has members
            $memberCount = User::where('team', $this->editingTeamKey)->count();
            
            if ($memberCount > 0) {
                $this->dispatch('team-deleted', message: 'Cannot delete team with existing members. Please move all members to other teams first.');
                $this->closeModals();
                return;
            }

            $team->delete();
            
            // If we deleted the selected team, clear selection
            if ($this->selectedTeam === $this->editingTeamKey) {
                $this->selectedTeam = null;
            }
            
            $this->closeModals();
            $this->dispatch('team-deleted', message: 'Team deleted successfully!');
        }
    }

    public function moveUser()
    {
        if (!$this->moveUserId || !$this->moveToTeam) {
            $this->dispatch('user-moved', message: 'User move not valid - missing information.');
            return;
        }

        $user = User::find($this->moveUserId);
        if ($user) {
            $user->update(['team' => $this->moveToTeam]);
            $this->closeModals();
            $this->dispatch('user-moved', message: 'User moved successfully!');
        }
    }

    public function closeModals()
    {
        $this->showCreateTeamModal = false;
        $this->showEditTeamModal = false;
        $this->showDeleteTeamModal = false;
        $this->showMoveUserModal = false;
        $this->reset(['newTeamKey', 'newTeamName', 'editingTeamKey', 'editingTeamName', 'moveUserId', 'moveToTeam']);
    }
}