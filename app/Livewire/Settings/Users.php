<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class Users extends Component
{
    use WithPagination;

    public $search = '';
    public $teamFilter = '';
    public $statusFilter = '';
    public $keysFilter = '';
    
    // Edit mode properties
    public $editingUser = null;
    public $editName = '';
    public $editEmail = '';
    public $editTeam = '';
    public $editAdminStatus = '';
    public $editKeysStatus = '';

    public function mount()
    {
        // Check if the current user is an admin
        if (Auth::user()->admin_status !== 'yes') {
            abort(403, 'Unauthorized access to user management.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTeamFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingKeysFilter()
    {
        $this->resetPage();
    }

    public function editUser($userId)
    {
        $user = User::findOrFail($userId);
        $this->editingUser = $userId;
        $this->editName = $user->name;
        $this->editEmail = $user->email;
        $this->editTeam = $user->team;
        $this->editAdminStatus = $user->admin_status;
        $this->editKeysStatus = $user->keys_status;
    }

    public function cancelEdit()
    {
        $this->editingUser = null;
        $this->editName = '';
        $this->editEmail = '';
        $this->editTeam = '';
        $this->editAdminStatus = '';
        $this->editKeysStatus = '';
    }

    public function saveUser()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editEmail' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->editingUser),
            ],
            'editTeam' => 'required|string|in:mobile,front_end,back_end,design,slt,e_commerce,bdm',
            'editAdminStatus' => 'required|string|in:yes,no',
            'editKeysStatus' => 'required|string|in:yes,no',
        ]);

        $user = User::findOrFail($this->editingUser);
        $user->update([
            'name' => $this->editName,
            'email' => $this->editEmail,
            'team' => $this->editTeam,
            'admin_status' => $this->editAdminStatus,
            'keys_status' => $this->editKeysStatus,
        ]);

        $this->cancelEdit();
        session()->flash('message', 'User updated successfully!');
    }

    public function render()
    {
        $users = User::when($this->search, function ($query) {
            $query->where(function ($subQuery) {
                $subQuery->where('name', 'like', '%' . $this->search . '%')
                         ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        })
        ->when($this->teamFilter, function ($query) {
            $query->where('team', $this->teamFilter);
        })
        ->when($this->statusFilter, function ($query) {
            $query->where('admin_status', $this->statusFilter);
        })
        ->when($this->keysFilter, function ($query) {
            $query->where('keys_status', $this->keysFilter);
        })
        ->orderBy('name')
        ->paginate(10);

        return view('livewire.settings.users', compact('users'));
    }

    public function getTeamDisplayName($team)
    {
        return match($team) {
            'mobile' => 'Mobile',
            'front_end' => 'Front End',
            'back_end' => 'Back End',
            'design' => 'Design',
            'slt' => 'Senior Leadership Team',
            'e_commerce' => 'E-Commerce',
            'bdm' => 'Business Development Manager',
            default => ucfirst($team),
        };
    }

    public function getTeamBadgeColor($team)
    {
        return match($team) {
            'slt' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
            'mobile' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'front_end' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'back_end' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            'design' => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200',
            'e_commerce' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'bdm' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        };
    }
}
