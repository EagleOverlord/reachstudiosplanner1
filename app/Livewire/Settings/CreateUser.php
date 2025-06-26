<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

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
     * The validation rules.
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', Password::defaults()],
            // MODIFICATION: Added validation rules for admin_status and keys_status
            'team' => ['required', 'string', 'in:yes,no'],
            'admin_status' => ['required', 'string', 'in:yes,no'],
            'keys_status' => ['required', 'string', 'in:yes,no'],
        ];
    }

    /**
     * Create a new user in the system.
     */
    public function createUser(): void
    {
        // Validate the form input data
        $validated = $this->validate();

        // Create the user in the database
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            // MODIFICATION: Added admin_status and keys_status to the create method
            // This assumes your User model has these fields in its $fillable array.
            'admin_status' => $validated['admin_status'],
            'keys_status' => $validated['keys_status'],
        ]);

        // Dispatch the success event to the front-end
        $this->dispatch('user-created');

        // MODIFICATION: Reset all form fields after successful creation
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