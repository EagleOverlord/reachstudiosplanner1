<?php

namespace App\Livewire\Settings;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CreateUser extends Component
{
    /**
     * The user's name.
     */
    public string $name = '';

    /**
     * The user's email address.
     */
    public string $email = '';

    /**
     * The user's password.
     */
    public string $password = '';

    /**
     * The user's team.
     */
    public string $team = '';

    /**
     * The user's admin status.
     * Default to 'no' to ensure a value is set.
     */
    public string $admin_status = 'no';

    /**
     * The user's keys status.
     * Default to 'no' to ensure a value is set.
     */
    public string $keys_status = 'no';

    /**
     * Mount the component and check admin permissions.
     */
    public function mount()
    {
        // Check if the current user is an admin
        if (Auth::user()->admin_status !== 'yes') {
            abort(403, 'Access denied. Admin privileges required.');
        }
    }

    /**
     * The validation rules.
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', Password::defaults()],
            'team' => ['required', 'string', 'in:mobile,front_end,back_end,design,slt,e_commerce,bdm'],
            'admin_status' => ['required', 'string', 'in:yes,no'],
            'keys_status' => ['required', 'string', 'in:yes,no'],
        ];
    }

    /**
     * Create a new user in the system.
     */
    public function createUser(): void
    {
        // Double-check admin privileges before creating user
        if (Auth::user()->admin_status !== 'yes') {
            abort(403, 'Access denied. Admin privileges required.');
        }

        // Validate the form input data
        $validated = $this->validate();

        // Create the user in the database
        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'team' => $validated['team'],
            'admin_status' => $validated['admin_status'],
            'keys_status' => $validated['keys_status'],
        ]);

        // Create a notification for the new user creation
        Notification::createUserNotification(
            'user_created',
            'New User Created',
            "A new user '{$newUser->name}' has been created by " . Auth::user()->name . ".",
            [
                'user_id' => $newUser->id,
                'created_by' => Auth::user()->id,
                'user_email' => $newUser->email,
                'user_team' => $newUser->team,
                'admin_status' => $newUser->admin_status,
                'keys_status' => $newUser->keys_status,
            ]
        );

        // Dispatch the success event to the front-end
        $this->dispatch('user-created');

        // Reset all form fields after successful creation
        $this->reset();
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.settings.create-user');
    }
}