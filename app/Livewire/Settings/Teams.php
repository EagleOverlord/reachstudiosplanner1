<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Team;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class Teams extends Component
{
    public $teams = [];
    public $teamStats = [];
    public $selectedTeam = null;
    public $teamMembers = [];
    
    // Create team
    public $showCreateTeamModal = false;
    public $newTeamKey = '';
    public $newTeamName = '';
    public $newTeamDescription = '';
    
    // Edit team
    public $showEditTeamModal = false;
    public $editingTeamKey = '';
    public $editingTeamName = '';
    public $editingTeamDescription = '';
    
    // Delete team
    public $showDeleteTeamModal = false;
    public $deletingTeamKey = '';
    
    // Move user
    public $showMoveUserModal = false;
    public $movingUserId = null;
    public $moveToTeam = '';

    public function mount()
    {
        // Check if the current user is an admin
        if (Auth::user()->admin_status !== 'yes') {
            abort(403, 'Unauthorized access to teams management.');
        }
        
        $this->loadData();
    }

    public function loadData()
    {
        $this->teams = Team::getTeamsArray();
        $this->teamStats = Team::getTeamStats();
        
        if ($this->selectedTeam) {
            $this->loadTeamMembers();
        }
    }

    public function selectTeam($teamKey)
    {
        $this->selectedTeam = $teamKey;
        $this->loadTeamMembers();
    }

    public function loadTeamMembers()
    {
        if ($this->selectedTeam) {
            $this->teamMembers = User::where('team', $this->selectedTeam)->get()->toArray();
        } else {
            $this->teamMembers = [];
        }
    }

    public function showCreateModal()
    {
        $this->resetCreateForm();
        $this->showCreateTeamModal = true;
    }

    public function resetCreateForm()
    {
        $this->newTeamKey = '';
        $this->newTeamName = '';
        $this->newTeamDescription = '';
        $this->resetErrorBag(['newTeamKey', 'newTeamName', 'newTeamDescription']);
    }

    public function createTeam()
    {
        $this->validate([
            'newTeamKey' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9_]+$/', Rule::unique('teams', 'key')],
            'newTeamName' => 'required|string|max:100',
            'newTeamDescription' => 'nullable|string|max:500',
        ]);

        Team::create([
            'key' => $this->newTeamKey,
            'name' => $this->newTeamName,
            'description' => $this->newTeamDescription,
            'is_active' => true,
        ]);

        $this->loadData();
        $this->closeModals();
        
        $this->dispatch('team-created', message: "Team '{$this->newTeamName}' created successfully!");
    }

    public function showEditModal($teamKey)
    {
        $team = Team::where('key', $teamKey)->first();
        if ($team) {
            $this->editingTeamKey = $team->key;
            $this->editingTeamName = $team->name;
            $this->editingTeamDescription = $team->description ?? '';
            $this->showEditTeamModal = true;
        }
    }

    public function updateTeam()
    {
        $this->validate([
            'editingTeamName' => 'required|string|max:100',
            'editingTeamDescription' => 'nullable|string|max:500',
        ]);

        $team = Team::where('key', $this->editingTeamKey)->first();
        if ($team) {
            $team->update([
                'name' => $this->editingTeamName,
                'description' => $this->editingTeamDescription,
            ]);

            $this->loadData();
            $this->closeModals();
            
            $this->dispatch('team-updated', message: "Team '{$this->editingTeamName}' updated successfully!");
        }
    }

    public function showDeleteModal($teamKey)
    {
        $this->deletingTeamKey = $teamKey;
        $this->showDeleteTeamModal = true;
    }

    public function deleteTeam()
    {
        $team = Team::where('key', $this->deletingTeamKey)->first();
        
        if ($team) {
            // Check if team has members
            $memberCount = User::where('team', $this->deletingTeamKey)->count();
            
            if ($memberCount > 0) {
                $this->dispatch('team-deleted', message: "Cannot delete team '{$team->name}' because it has {$memberCount} member(s). Move all members first.");
                $this->closeModals();
                return;
            }

            $teamName = $team->name;
            $team->delete();

            // Reset selected team if it was deleted
            if ($this->selectedTeam === $this->deletingTeamKey) {
                $this->selectedTeam = null;
                $this->teamMembers = [];
            }

            $this->loadData();
            $this->closeModals();
            
            $this->dispatch('team-deleted', message: "Team '{$teamName}' deleted successfully!");
        }
    }

    public function showMoveModal($userId)
    {
        $this->movingUserId = $userId;
        $this->moveToTeam = '';
        $this->showMoveUserModal = true;
    }

    public function moveUser()
    {
        if (!$this->movingUserId || !$this->moveToTeam) {
            return;
        }

        $user = User::find($this->movingUserId);
        $targetTeam = Team::where('key', $this->moveToTeam)->first();
        
        if ($user && $targetTeam) {
            $oldTeam = $user->team;
            $user->update(['team' => $this->moveToTeam]);
            
            $this->loadData();
            $this->loadTeamMembers();
            $this->closeModals();
            
            $this->dispatch('user-moved', message: "User '{$user->name}' moved to '{$targetTeam->name}' team successfully!");
        } else {
            $this->dispatch('user-moved', message: "Selected team is not valid. Please try again.");
        }
    }

    public function closeModals()
    {
        $this->showCreateTeamModal = false;
        $this->showEditTeamModal = false;
        $this->showDeleteTeamModal = false;
        $this->showMoveUserModal = false;
    }

    public function render()
    {
        return view('livewire.settings.teams');
    }
}